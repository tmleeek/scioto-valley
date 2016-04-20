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
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Model_Options_Cmspages {
     public function toOptionArray() {
        $_collection = Mage::getSingleton('cms/page')->getCollection()->addFieldToFilter('is_active', 1);

        $_result = array();
        foreach ($_collection as $item) {
            $data = array(
                'value' => $item->getData('page_id'),
                'label' => $item->getData('title'));
            $_result[] = $data;
        }
        return $_result;
    }
}