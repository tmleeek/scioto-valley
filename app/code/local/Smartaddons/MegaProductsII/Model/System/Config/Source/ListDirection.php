<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProductsII_Model_System_Config_Source_ListDirection
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'hori', 'label'=>Mage::helper('megaproductsii')->__('Horizontal')),
        	array('value'=>'vert', 'label'=>Mage::helper('megaproductsii')->__('Vertical'))
		);
	}
}
