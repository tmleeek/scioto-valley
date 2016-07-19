<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */  
class Amasty_Zoom_Model_Source_LightboxEffects extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'fade', 'label' => $hlp->__('Effect of disappearance')),
			array('value' => 'elastic', 'label' => $hlp->__('Effect of motion')),
            array('value' => 'none', 'label' => $hlp->__('None')),
		);
	}
	
}