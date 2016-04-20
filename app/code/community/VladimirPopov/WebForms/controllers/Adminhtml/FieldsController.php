<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Adminhtml_FieldsController
	extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('webforms/webforms');
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function gridAction()
	{
		$this->loadLayout();
		if(!Mage::registry('webforms_data')){
			Mage::register('webforms_data',Mage::getModel('webforms/webforms')->load($this->getRequest()->getParam('id')));
		}
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_fields')->toHtml()
		);
	}	
	
	public function editAction(){
		if((float)substr(Mage::getVersion(),0,3) > 1.3)
			$this->_title($this->__('Web-forms'))->_title($this->__('Edit Field'));
		$fieldsId = $this->getRequest()->getParam('id');
		$webformsId = $this->getRequest()->getParam('webform_id');
		
		$store = $this->getRequest()->getParam('store');
		$field = Mage::getModel('webforms/fields')->setStoreId($store)->load($fieldsId);
		if($field->getWebformId()){
			$webformsId = $field->getWebformId();
		}
		$webformsModel = Mage::getModel('webforms/webforms')->setStoreId($store)->load($webformsId);
		
		if($field->getId() || $fieldsId == 0){
			Mage::register('webforms_data',$webformsModel);
			Mage::register('field',$field);
			
			$this->loadLayout();
			$this->_setActiveMenu('webforms/webforms');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('WebForms'),Mage::helper('adminhtml')->__('Web-forms'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('webforms/adminhtml_fields_edit'))
				->_addLeft($this->getLayout()->createBlock('webforms/adminhtml_fields_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webforms')->__('Field does not exist'));
			$this->_redirect('*/adminhtml_webforms/edit',array('id' => $webformsId));
		}
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function saveAction()
	{
		if( $this->getRequest()->getPost()){
			try{
				$id = $this->getRequest()->getParam('id');
				$postData = $this->getRequest()->getPost('field');
				$webform_id = $postData["webform_id"];
				$saveandcontinue = $this->getRequest()->getParam('back');
				
				unset($postData["saveandcontinue"]);
				
				$field = Mage::getModel('webforms/fields');
				
				$field->setId($id);
					
				$store = Mage::app()->getRequest()->getParam('store');							
				if($store){
					unset($postData["webform_id"]);
					$field->saveStoreData($store,$postData);
				} else
					$field->setData($postData)->setId($id)->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())->save();

				if( $this->getRequest()->getParam('id') <= 0 )
					$field->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Field was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setWebFormsData(false);
				
				if($saveandcontinue){
					$this->_redirect('*/adminhtml_fields/edit',array('id' => $field->getId(),'webform_id' => $webform_id,'store'=>$store,'active_tab'=>$this->getRequest()->getParam('active_tab')));
				} else {
					$this->_redirect('*/adminhtml_webforms/edit',array('id' => $webform_id,'tab' => 'form_fields','store'=>$store));
				}
				
				return;
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setWebFormsData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit',array('id' => $this->getRequest()->getParam('id'),'store'=>$store));
				return;
			}
			
		}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webforms')->__('Unexpected error'));
        $this->_redirect('*/adminhtml_webforms/index');
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0){
			try{
				$field = Mage::getModel('webforms/fields')->load($this->getRequest()->getParam('id'));
				$webform_id = $field->getWebformId();
				$field->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Field was successfully deleted'));
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/adminhtml_webforms/edit',array('id' => $webform_id,'tab' => 'form_fields'));
	}
	
	public function massDeleteAction()
	{
		$Ids = (array)$this->getRequest()->getParam('id');
		
		try {
			foreach($Ids as $id){
				$result = Mage::getModel('webforms/fields')->load($id);
				$result->delete();
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been deleted.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
		}

		$this->_redirect('webforms/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields'));
	}
	
	public function massStatusAction(){
		$Ids = (array)$this->getRequest()->getParam('id');
		$status = (int)$this->getRequest()->getParam('status');
		$store = $this->getRequest()->getParam('store');
		$data = array('is_active',$status);
		
		try {
			foreach($Ids as $id){
				if($store){
					$result = Mage::getModel('webforms/fields')->setId($id);
					$result->updateStoreData($store,$data);					
				} else {
					$result = Mage::getModel('webforms/fields')->load($id);
					$result->setData('is_active',$status);
					$result->save();
				}
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been updated.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
		}
		
		$this->_redirect('webforms/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields','store'=>$store));
	}
	
	public function massFieldsetAction(){
		$Ids = (array)$this->getRequest()->getParam('id');
		$fieldset = (int)$this->getRequest()->getParam('fieldset');
		
		try {
			foreach($Ids as $id){
				$result = Mage::getModel('webforms/fields')->load($id);
				$result->setData('fieldset_id',$fieldset);
				$result->save();
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been updated.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
		}
		
		$this->_redirect('webforms/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields'));
	}
	
	public function massDuplicateAction(){
		$Ids = (array)$this->getRequest()->getParam('id');
		
		try {
			foreach($Ids as $id){
				$result = Mage::getModel('webforms/fields')->load($id);
				$result->duplicate();
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been duplicated.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while duplicating records.'));
		}
		
		$this->_redirect('webforms/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields'));
	}
	
}
?>