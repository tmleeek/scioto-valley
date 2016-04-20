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

class Magebassi_Mbimageslider_Block_Adminhtml_Renderer_Grouplocation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {		
		$model = Mage::getModel('mbimageslider/mbgroups');
		$data  = $model->loadByField('id',$row->getId());		
		$locationtype = $data['locationtype'];				
		$gp_id = $row->getId();
		$location = '';		
		
		$cmscollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$gp_id);			
		if(count($cmscollection)){
			$location .= "<b>Cms Pages &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;</b>";
			$resultcmsstr = array();
			foreach($cmscollection as $key){
				$resultcmsstr[] = Mage::getModel('cms/page')->load($key['page_id'])->getTitle();									
			}
			$location .= implode(", ",$resultcmsstr)."<br>";
		}
							
				
		$catcollection = Mage::getModel('mbimageslider/mbcatpages')->getCollection()->addFieldToFilter('group_id',$gp_id);
		if(count($catcollection)){
			$location .= "<b>Categories &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;</b>";
			$resultcatstr = array();
			foreach($catcollection as $obj){
				$resultcatstr[] = Mage::getModel('catalog/category')->load($obj['category_ids'])->getName();				
			}
			$location .= implode(", ",$resultcatstr)."<br>";
		}
		
			
		$procollection = Mage::getModel('mbimageslider/mbproductpages')->getCollection()->addFieldToFilter('group_id',$gp_id);
		if(count($procollection)){
			$location  .= "<b>Product SKU's &nbsp;: &nbsp;</b>";
			$resultprostr = array();
			foreach($procollection as $obj){
				$resultprostr[] = Mage::getModel('catalog/product')->load($obj['product_ids'])->getSku();				
			}
			$location .= implode(", ",$resultprostr)."<br>";
		}
			
		if($locationtype=='leftcol'){
			$location  .= "<b>Left Column</b>";
		}
		
		if($locationtype=='rightcol'){
			$location  .= "<b>Right Column</b>";
		}
		return $location;
	}
}