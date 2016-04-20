<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProductsII_Model_System_Config_Source_LinkTargets
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'', 		'label'=>Mage::helper('megaproductsii')->__('Same Window')),
        	array('value'=>'_blank','label'=>Mage::helper('megaproductsii')->__('New Window')),
			array('value'=>'_popup','label'=>Mage::helper('megaproductsii')->__('Popup Window'))
		);
	}
}
