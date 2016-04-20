<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_System_Config_Source_ListTheme
{
	public function toOptionArray()
	{
		return array(
		array('value'=>'theme1', 'label'=>Mage::helper('megaproducts')->__('Theme 01')),
		array('value'=>'theme2', 'label'=>Mage::helper('megaproducts')->__('Theme 02')),
		array('value'=>'theme3', 'label'=>Mage::helper('megaproducts')->__('Theme 03')),
		array('value'=>'theme4', 'label'=>Mage::helper('megaproducts')->__('Theme 04')),
		);
	}
}
