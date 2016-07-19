<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */  
class Amasty_Zoom_Model_Source_ChangeMainImg extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
			array('value' => 'mouseover', 'label' => $hlp->__('On Mouse Hover')),
			array('value' => 'click', 'label' => $hlp->__('On Click')),
            array('value' => '0', 'label' => $hlp->__('Disable')),
		);
	}
	
}