<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */
class  Amasty_Zoom_Model_Source_CarouselDirection extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 0, 'label' => $hlp->__('Under the main image')),
			array('value' => 1, 'label' => $hlp->__('To the left of the main image')),
		);
	}
	
}