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
class Magebassi_Mbimageslider_Model_Options_Linktarget {
    protected $_options;
	const TARGET_SELF = 'self';
	const TARGET_BLANK = 'blank';	  
    
    public function toOptionArray(){
        if (!$this->_options) {
			$this->_options[] = array(
			   'value'=>self::TARGET_SELF,
			   'label'=>Mage::helper('mbimageslider')->__('_Self')
			);
			$this->_options[] = array(
			   'value'=>self::TARGET_BLANK,
			   'label'=>Mage::helper('mbimageslider')->__('_Blank')
			);			
		}
		return $this->_options;
	}
}