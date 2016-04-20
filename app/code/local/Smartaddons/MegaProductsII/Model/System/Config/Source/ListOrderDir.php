<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProductsII_Model_System_Config_Source_ListOrderDir
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'asc',			'label' => Mage::helper('megaproductsii')->__('Asc')),
			array('value' => 'desc', 		'label' => Mage::helper('megaproductsii')->__('Desc'))
		);
	}
}
