<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class IsotopeOrder extends IsotopeProductCollection
{

	/**
	 * Name of the current table
	 * @var string
	 */
	protected $strTable = 'tl_iso_orders';
	
	/**
	 * Name of the child table
	 * @var string
	 */
	protected $ctable = 'tl_iso_order_items';
	
	/**
	 * Cache get requests to improve speed. Cart data cannot change without reload...
	 * @var array
	 */
	protected $arrCache = array();
	
	/**
	 * Shipping object if shipping module is set in session
	 * @var object
	 */
	public $Shipping;
	
	/**
	 * Payment object if payment module is set in session
	 * @var object
	 */
	public $Payment;

				
	public function __get($strKey)
	{
		switch($strKey)
		{
			case 'table':
				return $this->strTable;
				break;
				
			case 'ctable':
				return  $this->ctable;
				break;
			case 'surcharges':
				return $this->arrData['surcharges'] ? deserialize($this->arrData['surcharges']) : array();		
				break;
			case 'subTotal':
				
				return $this->calculateTotal($this->getProducts());
				
			case 'taxTotal':
				$intTaxTotal = 0;
				$arrSurcharges = $this->getSurcharges();
				
				foreach( $arrSurcharges as $arrSurcharge )
				{
					if ($arrSurcharge['add'])
						$intTaxTotal += $arrSurcharge['total_price'];
				}
				
				$this->arrCache[$strKey] = $intTaxTotal;
				break;
				
			case 'taxTotalWithShipping':
				$this->arrCache[$strKey] =  $this->taxTotal + $this->shippingTotal;
				break;
			
			case 'hasShipping':
				return (is_object($this->Shipping) ? true : false);
				break;
			case 'hasPayment':
				return (is_object($this->Payment) ? true : false);
				break;
			case 'shippingTotal':
				//instantiate shipping to reclaculate...
				if($this->shipping_id)
				{					
					$fltPrice = (float)$this->Shipping->price;
				}
				else
				{
					$fltPrice = 0.00;
				}
				
				$this->arrCache[$strKey] = $fltPrice;
				break;
				
			case 'grandTotal':
				$intTotal = $this->calculateTotal($this->getProducts());
				$arrSurcharges = $this->getSurcharges();
				
				foreach( $arrSurcharges as $arrSurcharge )
				{
					if ($arrSurcharge['add'] !== false)
						$intTotal += $arrSurcharge['total_price'];
				}				
				return $intTotal;
								
			case 'billingAddress':
				return deserialize($this->arrData['billing_address'], true);
				
			case 'shippingAddress':
				return deserialize($this->arrData['shipping_address'], true);
			default:
				return $this->arrData[$strKey];
		
		}
	}
	
	public function initializeOrder()
	{
		if($this->arrData['shipping_id'])
		{		
			$objShipping = $this->Database->query("SELECT * FROM tl_iso_shipping_modules WHERE id=" . $this->shipping_id);
									
			$strClass = $GLOBALS['ISO_SHIP'][$objShipping->type];
													
			$this->Shipping = new $strClass($objShipping->row());
		}
		
		if($this->arrData['payment_id'])
		{
			$objPayment = $this->Database->query("SELECT * FROM tl_iso_payment_modules WHERE id=" . $this->payment_id);
								
			$strClass = $GLOBALS['ISO_PAY'][$objPayment->type];
													
			$this->Payment = new $strClass($objPayment->row());
		}
	
	}
	
	/**
	 * Add downloads to this order
	 */
	public function transferFromCollection(IsotopeProductCollection $objCollection, $blnDuplicate=true)
	{
		$arrIds = parent::transferFromCollection($objCollection, $blnDuplicate);
		
		foreach( $arrIds as $id )
		{
			$objDownloads = $this->Database->execute("SELECT * FROM tl_iso_downloads WHERE pid=(SELECT product_id FROM {$this->ctable} WHERE id=$id)");
			
			while( $objDownloads->next() )
			{
				$arrSet = array
				(
					'pid'					=> $id,
					'tstamp'				=> time(),
					'download_id'			=> $objDownloads->id,
					'downloads_remaining'	=> ($objDownloads->downloads_allowed > 0 ? $objDownloads->downloads_allowed : ''),
				);
				
				$this->Database->prepare("INSERT INTO tl_iso_order_downloads %s")->set($arrSet)->executeUncached();
			}
		}
	}
	
	
	/**
	 * Remove downloads when removing a product
	 */
	public function deleteProduct($intId)
	{
		$this->Database->query("DELETE FROM tl_iso_order_downloads WHERE pid=$intId");
		
		return parent::deleteProduct($intId);
	}

	
	/**
	 * Also delete downloads when deleting this order.
	 */
	public function delete()
	{
		$this->Database->query("DELETE FROM tl_iso_order_downloads WHERE pid IN (SELECT id FROM {$this->ctable} WHERE pid={$this->id})");
		
		return parent::delete();
	}

	public function getSurcharges()
	{
		$this->import('Isotope');
		
		$arrPreTax = $arrPostTax = $arrTaxes = array();
		$arrProducts = $this->getProducts();
		
		foreach( $arrProducts as $pid => $objProduct )
		{						
			$arrTaxIds = array();
			$arrTax = $this->Isotope->calculateTax($objProduct->tax_class, $objProduct->total_price);
			
			if (is_array($arrTax))
			{
				foreach ($arrTax as $k => $tax)
				{
					if (array_key_exists($k, $arrTaxes))
					{
						$arrTaxes[$k]['total_price'] += $tax['total_price'];
						
						if (is_numeric($arrTaxes[$k]['price']) && is_numeric($tax['price']))
						{
							$arrTaxes[$k]['price'] += $tax['price'];
						}
					}
					else
					{
						$arrTaxes[$k] = $tax;
					}
					
					$arrTaxes[$k]['tax_id'] = array_search($k, array_keys($arrTaxes)) + 1;
					$arrTaxIds[] = array_search($k, array_keys($arrTaxes)) + 1;
				}
			}
			
			$this->arrProducts[$pid]->tax_id = implode(',', $arrTaxIds);
		}
		
		$arrSurcharges = array();
		
		$arrSurcharges = $this->getShippingSurcharge($arrSurcharges);
		$arrSurcharges = $this->getPaymentSurcharge($arrSurcharges);
		
		foreach( $arrSurcharges as $arrSurcharge )
		{
			if ($arrSurcharge['tax_class'] > 0)
			{
				$arrPreTax[] = $arrSurcharge;
			}
			else
			{
				$arrPostTax[] = $arrSurcharge;
			}
		}
		
		foreach( $arrPreTax as $arrSurcharge )
		{
			$arrTax = $this->Isotope->calculateTax($arrSurcharge['tax_class'], $arrSurcharge['total_price'], $arrSurcharge['add_tax']);
			
			if (is_array($arrTax))
			{
				foreach ($arrTax as $k => $tax)
				{
					if (array_key_exists($k, $arrTaxes))
					{
						$arrTaxes[$k]['total_price'] += $tax['total_price'];
						
						if (is_numeric($arrTaxes[$k]['price']) && is_numeric($tax['price']))
						{
							$arrTaxes[$k]['price'] += $tax['price'];
						}
					}
					else
					{
						$arrTaxes[$k] = $tax;
					}
					
					$arrTaxes[$k]['tax_id'] = array_search($k, array_keys($arrTaxes)) + 1;
				}
			}
		}
		
		return array_merge($arrPreTax, $arrTaxes, $arrPostTax);
	}
	
	/**
	 * Hook-callback for isoCheckoutSurcharge. Accesses the shipping module to get a shipping surcharge.
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function getShippingSurcharge($arrSurcharges)
	{			
		if ($this->hasShipping && $this->Shipping->price > 0)
		{
			$arrSurcharges[] = array
			(
				'label'			=> ($GLOBALS['TL_LANG']['MSC']['shippingLabel'] . ' (' . $this->Shipping->label . ')'),
				'price'			=> '&nbsp;',
				'total_price'	=> $this->Shipping->price,
				'tax_class'		=> $this->Shipping->tax_class,
				'add_tax'		=> ($this->Shipping->tax_class ? true : false),
			);
		}
		
		return $arrSurcharges;
	}
	
	
	/**
	 * Hook-callback for isoCheckoutSurcharge.
	 *
	 * @todo	Accesses the payment module to get a payment surcharge.
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function getPaymentSurcharge($arrSurcharges)
	{
		if ($this->hasPayment && $this->Payment->price > 0)
		{
			$arrSurcharges[] = array
			(
				'label'			=> ($GLOBALS['TL_LANG']['MSC']['paymentLabel'] . ' (' . $this->Payment->label . ')'),
				'price'			=> '&nbsp;',
				'total_price'	=> $this->Payment->price,
				'tax_class'		=> $this->Payment->tax_class,
				'add_tax'		=> ($this->Payment->tax_class ? true : false),
			);
		}
		
		return $arrSurcharges;
	}
	
	/**
	 * Calculate total price for products.
	 * 
	 * @access protected
	 * @param array $arrProductData
	 * @return float
	 */
	protected function calculateTotal($arrProducts)
	{		
		if (!is_array($arrProducts) || !count($arrProducts))
			return 0;
			
		$fltTotal = 0;
		
		foreach($arrProducts as $objProduct)
		{
			$fltTotal += ((float)$objProduct->price * (int)$objProduct->quantity_requested);
		}
			
		$taxPriceAdjustment = 0; // $this->getTax($floatSubTotalPrice, $arrTaxRules, 'MULTIPLY');
		
		return (float)$fltTotal + (float)$taxPriceAdjustment;
	}
	
}
