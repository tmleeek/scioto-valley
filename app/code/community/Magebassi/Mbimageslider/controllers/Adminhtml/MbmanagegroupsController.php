<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Umesh Sharma
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Bannerslider
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Adminhtml_MbmanagegroupsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('mbimageslider/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Group Manager'), Mage::helper('adminhtml')->__('Group Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
	
		$this->_title($this->__('Mbimageslider'))->_title($this->__('Manage groups'));
		$this->_initAction()->renderLayout();
	}	
	
	public function newAction(){
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$id     			= $this->getRequest()->getParam('id');		
		$model  			= Mage::getModel('mbimageslider/mbgroups')->load($id);		
		$location_type 		= $model->getLocationtype();		
		
		if($location_type=='cmspage'){ 
			$cmscollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$id);			
			$cmspages = array();
			foreach($cmscollection as $key){						
				$cmspages[] = $key['page_id'];					
			}			
			$form_data = $model->setCmspages($cmspages);					
		}
		$this->_getSession()->setData(Magebassi_Mbimageslider_Helper_Data::FORM_DATA_KEY, $form_data);

		if ($model->getId() || $id == 0) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('mbimageslider_data', $model);
			
			$this->_title($this->__('Mbimageslider'))->_title($this->__('Manage banner'));
			if ($model->getId()){
				$this->_title($model->getGroupname());
			}else{
				$this->_title($this->__('New Banner'));
			}

			$this->loadLayout();
			$this->_setActiveMenu('mbimageslider/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit'))
					->_addLeft($this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mbimageslider')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
		
	}
	
	public function locationtypeAction()
	{			
		$result = array('text' => Mage::getSingleton('core/layout')
                    ->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_general_locationtype')
                    ->setData('type', $this->getRequest()->getParam('type'))                   
                    ->toHtml());        
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
	}
	
	public function groupbannersgridAction(){
		$this->loadLayout();
        $this->getLayout()->getBlock('bgroup.grid')->setBannersBglist($this->getRequest()->getPost('banners_bglist', null));
        $this->renderLayout();
	}
	
	public function groupbannersgridsecAction(){
		$this->loadLayout();
        $this->getLayout()->getBlock('bgroup.grid')->setBannersBglist($this->getRequest()->getPost('banners_bglist', null));
        $this->renderLayout();
	}
	
	public function productsgridAction()
	{
		$this->loadLayout();
        $this->getLayout()->getBlock('products.grid');
        $this->renderLayout();	
	}
	
	public function productsgridsecAction()
	{
		$this->loadLayout();
        $this->getLayout()->getBlock('products.grid');
        $this->renderLayout();	
	}
	
	
	public function saveAction() 
	{	
	
		if ($data = $this->getRequest()->getPost()) 
		{		
			$model = Mage::getModel('mbimageslider/mbgroups');
			$model->setData($data)->setId($this->getRequest()->getParam('id'));
			
			try 
			{
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}				
				
				// Save Group Model
				$model->save();
				$group_id = $model->getId();	
				
				
				// CMS Page Save
				if(empty($data['locationtype']) || $data['locationtype']=='leftcol' || $data['locationtype']=='rightcol'){
					$locationcollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$group_id);
					foreach($locationcollection as $obj){
						$obj->delete();
					}
				}
				
				if(isset($data['cmspages'])){					
					$cmsgroups = $data['cmspages'];
					$cmscollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$group_id);
					foreach($cmscollection as $obj){
						$obj->delete();
					}
					foreach($cmsgroups as $key => $value){						
						$gmodel = Mage::getModel('mbimageslider/mbcmspages');
						$gmodel->setGroupId($group_id);						
						$gmodel->setPageId($value);						
						$gmodel->save();						
					}					
				}
				
				//Category Page Save
				if(isset($data['category_ids'])){						
					$categories = explode(",",$data['category_ids']);
					$cat = array_unique($categories);
					$cat = array_filter($cat);						
					$catcollection = Mage::getModel('mbimageslider/mbcatpages')->getCollection()->addFieldToFilter('group_id',$group_id);
					foreach($catcollection as $obj){
						$obj->delete();
					}
					foreach($cat as $key => $value){						
						$catmodel = Mage::getModel('mbimageslider/mbcatpages');
						$catmodel->setGroupId($group_id);						
						$catmodel->setCategoryIds($value);						
						$catmodel->save();						
					}			
				}
				
				// Store Save
				if(isset($data['store'])){					
					$groupstores = $data['store'];
					$storecollection = Mage::getModel('mbimageslider/mbgroupstores')->getCollection()->addFieldToFilter('group_id',$group_id);
					foreach($storecollection as $obj){
						$obj->delete();
					}
					foreach($groupstores as $key => $value){						
						$storemodel = Mage::getModel('mbimageslider/mbgroupstores');
						$storemodel->setGroupId($group_id);						
						$storemodel->setStoreId($value);						
						$storemodel->save();						
					}					
				}
				
				// Selected Banner Save
				if(isset($data['links']))
				{   
					$groups = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['banners_bglist']);					
					$collection = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$group_id);
					
					foreach($collection as $obj){
						$obj->delete();
					}					
					foreach($groups as $key => $value){
						$pos 	= $value['position'];
						$gmodel = Mage::getModel('mbimageslider/mbseclist');
						$gmodel->setGroupId($group_id);						
						$gmodel->setSelectedList($key);
						$gmodel->setPosition($pos);
						$gmodel->save();						
					}					
				}

				// Selected Products Save
				if(isset($data['product']))
				{
					$products = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['product']['products_list']);					
					$collection = Mage::getModel('mbimageslider/mbproductpages')->getCollection()->addFieldToFilter('group_id',$group_id);					
					foreach($collection as $obj){
						$obj->delete();
					}					
					foreach($products as $key => $value){						
						$productmodel = Mage::getModel('mbimageslider/mbproductpages');
						$productmodel->setGroupId($group_id);						
						$productmodel->setProductIds($value);						
						$productmodel->save();						
					}					
				}				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mbimageslider')->__('Group was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);			
				

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mbimageslider')->__('Unable to find group to save'));
		$this->_redirect('*/*/');
	}
	
	public function deleteAction() 
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$groupId = $this->getRequest()->getParam('id');
				$model = Mage::getModel('mbimageslider/mbgroups');				 
				$model->setId($this->getRequest()->getParam('id'))->delete();
				
				$mbseclistColl = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$groupId);					
				foreach ($mbseclistColl as $slId) {						
					$delColl = Mage::getModel('mbimageslider/mbseclist')->load($slId['id']);
					$delColl->delete();
				} 
				
				$cmscollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$groupId);
				foreach($cmscollection as $obj){
					$obj->delete();
				}
				
				$catcollection = Mage::getModel('mbimageslider/mbcatpages')->getCollection()->addFieldToFilter('group_id',$groupId);
				foreach($catcollection as $obj){
					$obj->delete();
				}
				
				$productcollection = Mage::getModel('mbimageslider/mbproductpages')->getCollection()->addFieldToFilter('group_id',$groupId);
				foreach($productcollection as $obj){
					$obj->delete();
				}			
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Group was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() 
	{
        $groupIds = $this->getRequest()->getParam('mbimageslider');		
        if(!is_array($groupIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select group(s)'));
        } else {
            try {
                foreach ($groupIds as $gpId) {
                    $mbgroupsColl = Mage::getModel('mbimageslider/mbgroups')->load($gpId);
                    $mbgroupsColl->delete();
					
					$mbseclistColl = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$gpId);					
					foreach ($mbseclistColl as $slId) {						
						$delColl = Mage::getModel('mbimageslider/mbseclist')->load($slId['id']);
						$delColl->delete();
					} 
					
					$cmscollection = Mage::getModel('mbimageslider/mbcmspages')->getCollection()->addFieldToFilter('group_id',$gpId);
					foreach($cmscollection as $obj){
						$obj->delete();
					}
					
					$catcollection = Mage::getModel('mbimageslider/mbcatpages')->getCollection()->addFieldToFilter('group_id',$gpId);
					foreach($catcollection as $obj){
						$obj->delete();
					}
					
					$productcollection = Mage::getModel('mbimageslider/mbproductpages')->getCollection()->addFieldToFilter('group_id',$gpId);
					foreach($productcollection as $obj){
						$obj->delete();
					}

					
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d group(s) were successfully deleted', count($groupIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function massStatusAction()
    {
        $groupIds = $this->getRequest()->getParam('mbimageslider');
        if(!is_array($groupIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select group(s)'));
        } else {
            try {
                foreach ($groupIds as $gpId) {
                    $imageslider = Mage::getSingleton('mbimageslider/mbgroups')
                        ->load($gpId)
                        ->setGroupstatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d group(s) were successfully updated', count($groupIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function exportCsvAction()
    {
        $fileName   = 'mbgroups.csv';
        $content    = $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_grid')->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'mbgroups.xml';
        $content    = $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_grid')->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }
	
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	
	/* Get categories fieldset block */    
    public function categoriesAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_categories')->toHtml()
        );   
    }
	
	public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
	
}