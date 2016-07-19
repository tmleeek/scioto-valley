<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */
class Amasty_Zoom_Model_Source_LBoxActivation extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'mouse', 'label' => $hlp->__('On Mouse Over')),
			array('value' => 'click',  'label' => $hlp->__('On Click')),
		);
	}
	
}