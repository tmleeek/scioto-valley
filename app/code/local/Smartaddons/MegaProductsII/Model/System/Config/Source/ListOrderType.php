<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProductsII_Model_System_Config_Source_ListOrderType
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'Position',	'label' => Mage::helper('megaproductsii')->__('Position')),
			array('value' => 'created_at', 	'label' => Mage::helper('megaproductsii')->__('Created')),
			array('value' => 'name', 		'label' => Mage::helper('megaproductsii')->__('Name')),
			array('value' => 'price', 		'label' => Mage::helper('megaproductsii')->__('Price')),
			array('value' => 'random', 		'label' => Mage::helper('megaproductsii')->__('Random')),
		);
	}
}
