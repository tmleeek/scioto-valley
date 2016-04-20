<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ FME_Jobs extension \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Jobs                   \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 © free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class FME_Jobs_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('fme_extensions/jobs/store')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Jobs Manager'), Mage::helper('adminhtml')->__('Jobs Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jobs/store')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('store_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('fme_extensions/jobs/store');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Grid For Two Manager'), Mage::helper('adminhtml')->__('Request Data'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Grid For Two Manager'), Mage::helper('adminhtml')->__('Request Data'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('jobs/adminhtml_store_edit'))
			//$this->_addContent($this->getLayout()->createBlock('jobs/adminhtml_jobs_edit_tab_form'));
				->_addLeft($this->getLayout()->createBlock('jobs/adminhtml_store_edit_tabs'));
			
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Record does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function validate($name) {
		$model = Mage::getModel('jobs/store')->getCollection()->addFieldToFilter('store_name',$name)->load()->getData();
		if($model)
		{return true;}return false;
	}
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(!$this->getRequest()->getParam('id') && $this->validate($data['store_name'])){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Store already Exists, try with different name'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			$model = Mage::getModel('jobs/store');		
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
			try {
				$model = Mage::getModel('jobs/store');
				$del_id = $this->getRequest()->getParam('id');
                                
                                $coll = $model->load($del_id);
                                $store_name = $coll->getStoreName();
                                
                                $jobmodel = Mage::getModel('jobs/jobs')->getCollection()
                                            ->addFieldToFilter('store_name', $store_name);
                                $jobsassociated = count($jobmodel);
                                
                                if($jobsassociated < 1)
                                {
                                    $model->setId($this->getRequest()->getParam('id'))
                                            ->delete();

                                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Record was successfully deleted'));
                                    $this->_redirect('*/*/');
                                }
                                else {
                                    if( $jobsassociated == 1 )
                                    {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not delete. 1 job is associated with it.'));
                                    $this->_redirect('*/*/');
                                    }
                                    else if( $jobsassociated > 1 )
                                    {
                                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can not delete. %d jobs are associated with it.', $jobsassociated));
                                        $this->_redirect('*/*/');
                                    }
                                }
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
            try {
                $rows_deleted = 0;
                $rows_notdeletd = 0;
                foreach ($jobsIds as $jobsId) {
                    
                    $jobs = Mage::getModel('jobs/store')->load($jobsId);
                    $store_name = $jobs->getStoreName();
                    
                    $jobmodel = mage::getModel('jobs/jobs')->getCollection()
                                            ->addFieldToFilter('store_name', $store_name);
                    $jobsassociated = count($jobmodel);
                    
                    if($jobsassociated < 1)
                    {
                        $jobs->delete();
                        ++$rows_deleted;
                    }
                    else {
                        ++$rows_notdeleted;
                    }
                    
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', $rows_deleted
                    )
                );
                
                if($rows_notdeleted > 0)
                {
                    Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('adminhtml')->__(
                        'Could not delete %d record(s) due to Jobs associations', $rows_notdeleted
                    )
                );
                }
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
                    $jobs = Mage::getSingleton('jobs/store')
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
        $fileName   = 'store.csv';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_store_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'store.xml';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_store_grid')
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