<?php
/**
 * Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Jobs
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Jobs_Adminhtml_ApplicationsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('fme_extension/jobs/applications')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Applications Manager'), Mage::helper('adminhtml')->__('Applications Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jobs/jobsapplications')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('applications_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('fme_extension/jobs/applications');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Applications Manager'), Mage::helper('adminhtml')->__('Applications Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('jobs/adminhtml_applications_edit'))
				->_addLeft($this->getLayout()->createBlock('jobs/adminhtml_applications_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Department does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
// public function validate($name) {
//		$model = Mage::getModel('jobs/applications')->getCollection()->addFieldToFilter('department_name',$name)->load()->getData();
//		if($model)
//		{return true;}return false;
//	}
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('jobs/jobsapplications');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if (!$model->getCreateDate() && !$this->getRequest()->getParam('id')) {
					$model->setCreateDate(now());
					
				} 	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jobs')->__('Record was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Unable to find Record'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
                    $path = Mage::getBaseDir('media') . DS ."jobs". DS. "cv". DS;
			try {
				$model = Mage::getModel('jobs/jobsapplications')->load($this->getRequest()->getParam('id'));
                                $cv_filename = $model->getData('cvfile');
                                $model->setId($this->getRequest()->getParam('id'))
                                        ->delete();
                                
                                unlink($path.$cv_filename);

                                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Record was successfully deleted'));
                                $this->_redirect('*/*/');
                                
                                

			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $jobsIds = $this->getRequest()->getParam('jobs');
        if(!is_array($jobsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Record(s)'));
        } else {
            $path = Mage::getBaseDir('media') . DS ."jobs". DS. "cv". DS;
            try {
                foreach ($jobsIds as $jobsId) {
                    
                    $jobs = Mage::getModel('jobs/jobsapplications')->load($jobsId);
                    $cv_filename = $jobs->getData('cvfile');
                        $jobs->delete();
                        
                    unlink($path.$cv_filename);
                        
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($jobsIds)
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
        $jobsIds = $this->getRequest()->getParam('jobs');
        if(!is_array($jobsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select Record(s)'));
        } else {
            try {
                foreach ($jobsIds as $jobsId) {
                    $jobs = Mage::getSingleton('jobs/applications')
                        ->load($jobsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($jobsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'application.csv';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_applications_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'application.xml';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_applications_grid')
            ->getXml();

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
     
}