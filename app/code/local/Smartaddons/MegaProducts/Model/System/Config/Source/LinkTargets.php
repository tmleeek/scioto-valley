<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_System_Config_Source_LinkTargets
{
	public function toOptionArray()
	{
		return array(
			array('value'=>'', 		'label'=>Mage::helper('megaproducts')->__('Same Window')),
        	array('value'=>'_blank','label'=>Mage::helper('megaproducts')->__('New Window')),
			array('value'=>'_popup','label'=>Mage::helper('megaproducts')->__('Popup Window'))
		);
	}
}
