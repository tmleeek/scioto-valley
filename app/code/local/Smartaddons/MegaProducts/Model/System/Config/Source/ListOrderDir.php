<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_System_Config_Source_ListOrderDir
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'asc',			'label' => Mage::helper('megaproducts')->__('Asc')),
			array('value' => 'desc', 		'label' => Mage::helper('megaproducts')->__('Desc'))
		);
	}
}
