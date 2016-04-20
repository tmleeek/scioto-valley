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
 * File Type		: Model Resource File
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Model_Mysql4_Mbproductpages extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('mbimageslider/mbproductpages', 'id'); // Here 'id' is primary key of table identifier 'mbproductpages'
    }
}