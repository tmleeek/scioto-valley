<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */  
class Amasty_Zoom_Model_Source_CountOfCarouselItems extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => '1', 'label' => $hlp->__('One')),
			array('value' => '2', 'label' => $hlp->__('Two')),
            array('value' => '3', 'label' => $hlp->__('Three')),
		);
	}
	
}