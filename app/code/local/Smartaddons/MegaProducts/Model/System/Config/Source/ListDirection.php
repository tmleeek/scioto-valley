<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_System_Config_Source_ListDirection
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'hori', 'label'=>Mage::helper('megaproducts')->__('Horizontal')),
        	array('value'=>'vert', 'label'=>Mage::helper('megaproducts')->__('Vertical'))
		);
	}
}
