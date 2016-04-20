<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.8.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Group Based Bannerslider
 * 
 */
?>
<?php 
class Magebassi_Mbimageslider_Block_Mbimageslider extends Mage_Core_Block_Template {

	public function _prepareLayout() {
        $headBlock = $this->getLayout()->getBlock('head');
        $headBlock->addJs('magebassi/mbimageslider/jquery.min.js');        
        return parent::_prepareLayout();
    }

	public function getImageCollectionContent() 
	{
		$collObj	= Mage::getModel('mbimageslider/mbgroups');
		$collection = $collObj->getCollection()->addFieldToFilter('groupstatus',1);		
		
		$storeId = Mage::app()->getStore()->getId();
		if (!Mage::app()->isSingleStoreMode()) {
            $collection->addStoreFilter($storeId);
        }
		
		if (Mage::registry('current_product')) {
				$_productId = Mage::registry('current_product')->getId();
				$collection->addProductFilter($_productId);
		}
		else if (Mage::registry('current_category')) {
            $_categoryId 	= Mage::registry('current_category')->getId();			
            $collection->addCategoryFilter($_categoryId);			
        }
        else if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms') {
            $_pageId = Mage::getBlockSingleton('cms/page')->getPage()->getPageId();			
			$collection->addPageFilter($_pageId);			         
        }else{
			$moduleName = Mage::app()->getRequest()->getModuleName();
			$collection->addFieldToFilter('locationtype',$moduleName);
		}
		
		$banners = array();
		$groupData = array();
		foreach ($collection as $group) 
		{
			$groupid = $group->getId();
			$groupData = $group->loadByField('id',$groupid);			
			$mbseclist_collection = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$groupid)->setOrder('position', 'ASC');
			//$mbseclist_collection->enabledFilter();
			foreach ($mbseclist_collection as $sld)
			{
				$sliderid = $sld->getSelectedList();
				$model    = Mage::getModel('mbimageslider/mbslider')->load($sliderid);				
							
				$slidertype = $model->getSlidertype();
				$sliderid   = $model->getId();
				if($slidertype == 'imageslider'){
					$mbimageslider_model = Mage::getModel('mbimageslider/mbimageslider');
					$form_data 	= $mbimageslider_model->loadByField('sliderid',$sliderid);
					$form_data['slidertype'] = $slidertype;
				}										
				$banners[] = $form_data;
			}
		}
		$data['sliderdata'] = $banners;
		$data['groupdata']	= $groupData;		
		return $data;
	}

	public function getImageCollectionLeft() 
	{
		$collObj	= Mage::getModel('mbimageslider/mbgroups');
		$collection = $collObj->getCollection()->addFieldToFilter('groupstatus',1);	
		
		$storeId = Mage::app()->getStore()->getId();
		if (!Mage::app()->isSingleStoreMode()) {
            $collection->addStoreFilter($storeId);
        }
		
		$collection->addFieldToFilter('locationtype','leftcol');				
		$banners = array();
		$groupData = array();
		foreach ($collection as $group) 
		{
			$groupid = $group->getId();
			$groupData = $group->loadByField('id',$groupid);			
			$mbseclist_collection = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$groupid);
			foreach ($mbseclist_collection as $sld)
			{
				$sliderid = $sld->getSelectedList();
				$model    = Mage::getModel('mbimageslider/mbslider')->load($sliderid);				
							
				$slidertype = $model->getSlidertype();
				$sliderid   = $model->getId();
				if($slidertype == 'imageslider'){
					$mbimageslider_model = Mage::getModel('mbimageslider/mbimageslider');
					$form_data 	= $mbimageslider_model->loadByField('sliderid',$sliderid);
					$form_data['slidertype'] = $slidertype;
				}										
				$banners[] = $form_data;
			}
		}
		$data['sliderdata'] = $banners;
		$data['groupdata']	= $groupData;		
		return $data;
	}
	
	public function getImageCollectionRight() 
	{
		$collObj	= Mage::getModel('mbimageslider/mbgroups');
		$collection = $collObj->getCollection()->addFieldToFilter('groupstatus',1);	
		
		$storeId = Mage::app()->getStore()->getId();
		if (!Mage::app()->isSingleStoreMode()) {
            $collection->addStoreFilter($storeId);
        }
		
		$collection->addFieldToFilter('locationtype','rightcol');				
		$banners = array();
		$groupData = array();
		foreach ($collection as $group) 
		{
			$groupid = $group->getId();
			$groupData = $group->loadByField('id',$groupid);			
			$mbseclist_collection = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$groupid);
			foreach ($mbseclist_collection as $sld)
			{
				$sliderid = $sld->getSelectedList();
				$model    = Mage::getModel('mbimageslider/mbslider')->load($sliderid);				
							
				$slidertype = $model->getSlidertype();
				$sliderid   = $model->getId();
				if($slidertype == 'imageslider'){
					$mbimageslider_model = Mage::getModel('mbimageslider/mbimageslider');
					$form_data 	= $mbimageslider_model->loadByField('sliderid',$sliderid);
					$form_data['slidertype'] = $slidertype;
				}										
				$banners[] = $form_data;
			}
		}
		$data['sliderdata'] = $banners;
		$data['groupdata']	= $groupData;		
		return $data;
	}	
} 