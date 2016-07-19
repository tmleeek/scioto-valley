<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
*/
class Amasty_Zoom_Model_Source_TypeOfZoom extends Varien_Object
{
	public function toOptionArray()
	{
	    $hlp = Mage::helper('amzoom');
		return array(
            array('value' => 'window',  'label' => $hlp->__('Outside')),
            array('value' => 'inner',   'label' => $hlp->__('Inside')),
			array('value' => 'lens',    'label' => $hlp->__('Lens')),
		);
	}
	
}