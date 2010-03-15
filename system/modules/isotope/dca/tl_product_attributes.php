<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Winans Creative 2009
 * @author     Fred Bliss <fred@winanscreative.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Table tl_product_attributes 
 */
$GLOBALS['TL_DCA']['tl_product_attributes'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'onload_callback'			  => array
		(
			array('tl_product_attributes', 'disableFieldName'),
		),
		'onsubmit_callback'			  => array
		(
			array('tl_product_attributes', 'modifyColumn')
		),
		'ondelete_callback'			  => array
		(
			array('tl_product_attributes', 'deleteAttribute'),
		),
	),
	
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 1,
			'fields'                  => array('legend', 'name'),
			'flag'					  => 1,
			'panelLayout'             => 'sort,filter,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_product_attributes']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_product_attributes']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_product_attributes']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_product_attributes']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

    // Palettes
    'palettes' => array
    (
        '__selector__'				=> array('type','use_alternate_source','is_customer_defined'),
		'default'					=> '{attribute_legend},name,field_name,type,legend,description;',
		'text'                     	=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined;{validation_legend},is_required;{search_filters_legend},is_searchable,is_order_by_enabled;rgxp',
		/*'shorttext'               	=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},is_customer_defined;{validation_legend},is_required;{search_filters_legend},is_searchable,is_order_by_enabled;rgxp',*/
		'integer'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined;{validation_legend},is_required;{search_filters_legend},is_filterable,is_order_by_enabled,is_used_for_price_rules',
		'decimal'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;multilingual,is_customer_defined;is_required,is_filterable,is_order_by_enabled,is_used_for_price_rules,is_listing_field',
		'textarea'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,use_rich_text_editor,is_customer_defined;{validation_legend},is_required;{search_filters_legend},is_searchable,is_order_by_enabled;rgxp',
		'datetime'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},multilingual,is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{validation_legend},is_required;{search_filters_legend},is_order_by_enabled',
		'select'					=> '{attribute_legend},name,field_name,type,legend,description;{options_legend},option_list,use_alternate_source;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined,is_multiple_select;{validation_legend},is_required;{search_filters_legend},is_filterable,is_order_by_enabled',
		/*'boolean'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined,is_multiple_select;{validation_legend},is_required;{search_filters_legend},is_filterable,is_order_by_enabled',*/
		'options'					=> '{attribute_legend},name,field_name,type,legend,description;{options_legend},option_list,{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined,is_multiple_select;{validation_legend},is_required;{search_filters_legend},is_filterable,is_order_by_enabled',
		'fileattach'				=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined;{validation_legend},is_required',
		'filetree'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined,is_multiple_select,show_files;{validation_legend},is_required,{search_filters_legend},is_filterable',
		'media'						=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,show_files;{validation_legend},is_required',
		'checkbox'					=> '{attribute_legend},name,field_name,type,legend,description;{visibility_legend},is_listing_field,is_visible_on_front,is_hidden_on_backend,disabled;{use_mode_legend},multilingual,is_customer_defined;{validation_legend},is_required;{search_filters_legend},is_filterable,is_order_by_enabled'


    ),

    // Subpalettes
    'subpalettes' => array
    (
		'use_alternate_source'		=> 'list_source_table,list_source_field',
    	'is_customer_defined'		=> 'add_to_product_variants'
    ),

    // Fields
	'fields' => array
	(
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['name'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
		),
		'field_name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['field_name'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>30, 'unique'=>true, 'doNotSaveEmpty'=>true, 'tl_class'=>'w50'),
			'save_callback'			  => array
			(
				array('tl_product_attributes', 'createColumn'),
			),
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['type'],
			'inputType'               => 'select',
			'options'				  => array_keys($GLOBALS['ISO_ATTR']),
			'reference'				  => &$GLOBALS['TL_LANG']['ATTR'],
			'eval'                    => array('mandatory'=>true, 'includeBlankOption'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
		),
		'legend' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['legend'],
			'inputType'               => 'select',
			'options_callback'		  => array('tl_product_attributes','getLegends'),
			'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['description'],
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'clr long'),
		),
		'option_list' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['option_list'],
			'exclude'                 => true,
			'inputType'               => 'optionWizard'
		),
		'show_files' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['show_files'],
			'exclude'                 => true,
			'default'				  => 0,
			'inputType'               => 'checkbox'
		),
		'add_to_product_variants' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['add_to_product_variants'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		)
		/*,
		'attr_use_mode' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['attr_use_mode'],	//Is customer defined will determine what to do - whether to show just 
																									//a label or a form control
			'exclude'                 => true,
			'default'				  => 'label',
			'inputType'               => 'select',
			'options'				  => array('label','input'),
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'submitOnChange'=>true)
		)*/,
		'attr_default_value' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['attr_default_value'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255),
			'save_callback'			  => array
			(
				array('tl_product_attributes','validateInput')
			)
		),
		'text_collection_rows' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['text_collection_rows'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'rgxp'=>'digit')	
		),
		'is_customer_defined' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_customer_defined'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'					  => array('submitOnChange'=>true)
		),
		'is_visible_on_front' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_visible_on_front'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_hidden_on_backend' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_hidden_on_backend'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_required' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_required'],
			'exclude'                 => true,
			'default'				  => 0,
			'inputType'               => 'checkbox'
		),
		'is_filterable' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_filterable'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_searchable' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_searchable'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_order_by_enabled' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_order_by_enabled'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_used_for_price_rules' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_used_for_price_rules'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'is_multiple_select' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_multiple_select'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'use_rich_text_editor' => array
		(
			'label'					  => &$GLOBALS['TL_LANG']['tl_product_attributes']['use_rich_text_editor'],
			'exclude'				  => true,
			'inputType'				  => 'checkbox'
		),
		'is_listing_field' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['is_listing_field'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		),
		'multilingual' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['multilingual'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
		),
		'use_alternate_source' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['use_alternate_source'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'					  => array('submitOnChange'=>true)
		),
		'list_source_table' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['list_source_table'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('includeBlankOption'=>true,'submitOnChange'=>true),
			'options_callback'		  => array('tl_product_attributes','getTables')
		),
		'list_source_field' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['list_source_field'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'eval'                    => array('includeBlankOption'=>true,'submitOnChange'=>true),
			'options_callback'		  => array('tl_product_attributes','getFields'),
			'save_callback'			  => array
			(
//				array('ProductCatalog','importAlternateSourceToCollection')
			)
		),
		'rgxp' => array
		(
			'label'					  =>  &$GLOBALS['TL_LANG']['tl_product_attributes']['rgxp'],
			'exclude'				  => true,
			'inputType'				  => 'select',
			'options'				  => array('alnum','extnd','email','url'),
			'eval'				 	  => array('includeBlankOption'=>true),
			'reference'				  => &$GLOBALS['TL_LANG']['tl_product_attributes']['rgxpOptions']	
		),
		'disabled' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_product_attributes']['disabled'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		)
	)
);


/**
 * tl_product_attributes class.
 * 
 * @extends Backend
 */
class tl_product_attributes extends Backend
{

	public function getLegends()
	{
		$this->loadLanguageFile('tl_product_data');
		
		foreach($GLOBALS['TL_LANG']['tl_product_data'] as $k=>$v)
		{
			if(preg_match('(_legend)', $k))
			{
				$arrGroups[$k] = $v;			
			}
		
		}
		
		return $arrGroups;
	}
	

	/**
	 * getTables function.
	 * 
	 * @access public
	 * @return array
	 */
	public function getTables()
    {
		$arrReturn = array();

		$objTables = $this->Database->prepare("SHOW TABLES FROM " . $GLOBALS['TL_CONFIG']['dbDatabase'])->execute();
		
		if($objTables->numRows > 0)
		{
			$arrTables = $objTables->fetchAllAssoc();
			
			foreach ($arrTables as $arrTable)
			{
				if($this->Database->fieldExists('pid',current($arrTable)))
				{
					$arrReturn[] = current($arrTable);
				}
			}
		}

		return $arrReturn;
    }
	
	
	/**
	 * getFields function.
	 * 
	 * @access public
	 * @param object DataContainer $dc
	 * @return array
	 */
	public function getFields(DataContainer $dc)
    {
        $objTable = $this->Database->prepare("SELECT list_source_table FROM tl_product_attributes WHERE id=?")
								   ->limit(1)
								   ->execute($dc->id);
         
        if ($objTable->numRows > 0 && $this->Database->tableExists($objTable->list_source_table))
        {
            $fields = $this->Database->listFields($objTable->list_source_table);
			
            return array_map(create_function('$x', 'return $x["name"];'), $fields);
        }
    }
    
    
    public function deleteAttribute($dc)
    {
    	$objAttribute = $this->Database->prepare("SELECT * FROM tl_product_attributes WHERE id=?")->execute($dc->id);
    	
    	if ($this->Database->fieldExists($objAttribute->field_name, 'tl_product_data'))
    	{
			$this->import('IsotopeDatabase');
			$this->IsotopeDatabase->delete($objAttribute->field_name);
    	}
    }
    
    
    public function disableFieldName($dc)
    {
    	$objAttribute = $this->Database->prepare("SELECT * FROM tl_product_attributes WHERE id=?")->execute($dc->id);
    	
    	if (strlen($objAttribute->field_name))
    	{
    		$GLOBALS['TL_DCA']['tl_product_attributes']['fields']['field_name']['eval']['disabled'] = true;
    		$GLOBALS['TL_DCA']['tl_product_attributes']['fields']['field_name']['eval']['mandatory'] = false;
    	}
    }
    
    
    public function createColumn($varValue, $dc)
    {
    	$varValue = standardize($varValue);
    	
    	if (in_array($varValue, array('id', 'pid', 'sorting', 'tstamp')))
    	{
    		throw new Exception($GLOBALS['TL_LANG']['ERR']['systemColumn'], $varValue);
    		return '';
    	}
    	
    	if (strlen($varValue) && !$this->Database->fieldExists($varValue, 'tl_product_data'))
    	{
    		$strType = strlen($GLOBALS['ISO_ATTR'][$this->Input->post('type')]['sql']) ? $this->Input->post('type') : 'text';
    		
    		$this->Database->execute(sprintf("ALTER TABLE tl_product_data ADD %s %s", $varValue, $GLOBALS['ISO_ATTR'][$strType]['sql']));
    		
    		$this->import('IsotopeDatabase');
			$this->IsotopeDatabase->add($varValue, $GLOBALS['ISO_ATTR'][$strType]['sql']);
    	}
    	
    	return $varValue;
    }
    
    
	public function modifyColumn($dc)
	{
		$objAttribute = $this->Database->prepare("SELECT * FROM tl_product_attributes WHERE id=?")->execute($dc->id);
		
		if ($objAttribute->type != $dc->activeRecord->type && strlen($dc->activeRecord->type) && strlen($GLOBALS['ISO_ATTR'][$dc->activeRecord->type]['sql']))
		{
			$this->Database->execute(sprintf("ALTER TABLE tl_product_data MODIFY %s %s", $objAttribute->field_name, $GLOBALS['ISO_ATTR'][$dc->activeRecord->type]['sql']));
		}
	}
}

