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
class Magebassi_Mbimageslider_Model_Bannerscollection{
    protected $_options;    
    
    public function toOptionArray(){
        if (!$this->_options) {
			
			$collection = Mage::getModel('mbimageslider/mbslider')->getCollection()->addFieldToFilter('status',1);
						
			foreach ($collection as $banner) 
			{
				$this->_options[] = array(
				   'value'=>$banner->getId(),
				   'label'=>$banner->getBannername()
				);
			}						

		}
		return $this->_options;
	}
}