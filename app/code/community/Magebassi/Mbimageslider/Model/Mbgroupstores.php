<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Bannerslider
 * File Type		: Model file
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Model_Mbgroupstores extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mbimageslider/mbgroupstores'); // Localtion of the resource file
    }	
}