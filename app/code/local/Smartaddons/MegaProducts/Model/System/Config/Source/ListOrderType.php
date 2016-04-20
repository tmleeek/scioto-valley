<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_System_Config_Source_ListOrderType
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'Position',	'label' => Mage::helper('megaproducts')->__('Position')),
			array('value' => 'created_at', 	'label' => Mage::helper('megaproducts')->__('Created')),
			array('value' => 'name', 		'label' => Mage::helper('megaproducts')->__('Name')),
			array('value' => 'price', 		'label' => Mage::helper('megaproducts')->__('Price')),
			array('value' => 'random', 		'label' => Mage::helper('megaproducts')->__('Random')),
		);
	}
}
