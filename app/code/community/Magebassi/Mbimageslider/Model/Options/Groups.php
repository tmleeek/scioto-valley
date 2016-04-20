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
class Magebassi_Mbimageslider_Model_Options_Groups{
    protected $_options;	   
    
    public function toOptionArray(){
        if (!$this->_options) {
			$collection = Mage::getModel('mbimageslider/mbgroups')->getCollection();
			
			foreach($collection as $link)
			{
				$group_id 	= $link->getId();
				$groupname 	= $link->getGroupname();
				$this->_options[] = array(
			   'value'=>$group_id,
			   'label'=>$groupname
				);			
			}		
					
		}
		return $this->_options;
	}
}