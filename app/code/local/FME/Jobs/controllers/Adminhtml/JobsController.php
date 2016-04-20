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

class FME_Jobs_Adminhtml_JobsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('fme_extensions/jobs/jobs')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Jobs Manager'), Mage::helper('adminhtml')->__('Jobs Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
        
        public function applicantsAction()
        {
            //$this->_initAction();
            $this->loadLayout();
            $this->getLayout()->getBlock('jobs.edit.tab.applicants');
                                                   //->setApplicants($this->getRequest()->getPost('applicants', null));
            $this->renderLayout();
        }

//        public function applicantsgridAction()
//        {
//            $this->_initAction();
//            $this->loadLayout();
//            $this->getLayout()->getBlock('jobs.edit.tab.applicants');
//                                                      //->setApplicants($this->getRequest()->getPost('applicants', null));
//            $this->renderLayout();
//        }

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('jobs/jobs')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('jobs_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('fme_extensions/jobs/jobs');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Jobs Manager'), Mage::helper('adminhtml')->__('Jobs Manager'));
			

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('jobs/adminhtml_jobs_edit'))
				->_addLeft($this->getLayout()->createBlock('jobs/adminhtml_jobs_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Jobs does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
		//	echo "<pre>"; print_r($data); exit;
         
            $data['apply_by'] = date("Y-m-d",strtotime($data['apply_by']));
                        $jobsurl_temp1 = trim($data['jobs_url']);
                        if($jobsurl_temp1 =="")
                        {
                            $jobtitle_temp1 = trim($data['jobtitle']);
                            $jobsurl_temp1 = str_replace(' ', '_', $jobtitle_temp1);
                            $jobsurl = strtolower($jobsurl_temp1);
                            $data['jobs_url'] = $jobsurl;
                        }
                        else {
                            $joburl_temp1 = trim($data['jobs_url']);
                            $jobsurl_temp2 = str_replace(' ', '_', $joburl_temp1);
                            $jobsurl = strtolower($jobsurl_temp2);
                            $data['jobs_url'] = $jobsurl;
                        }
                        
                        
			$model = Mage::getModel('jobs/jobs');
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
                        
//			$model->setPositionsJobs($data['positions_jobs']);
//                        $model->setCareerLevel($data['career_level']);
			$model->setMinExp($data['min_exp']);
			try {
				
				if(!$this->getRequest()->getParam('id') && !$model->getCreateDates()) {
					$model->setCreateDates(now());
				}
//                                echo "<pre>"; print_r($model); exit;
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('jobs')->__('Jobs was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('jobs')->__('Unable to find Jobs to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
            
                 Mage::helper('jobs')->deleteshit($this->getRequest()->getParam('id'));
				$model = Mage::getModel('jobs/jobs');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
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
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
           try {
                foreach ($jobsIds as $jobsId) {
                 Mage::helper('jobs')->deleteshit($jobsId);
                    $jobs = Mage::getModel('jobs/jobs')->load($jobsId);
                    $jobs->delete();
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
                    $jobs = Mage::getSingleton('jobs/jobs')
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
        $fileName   = 'jobs.csv';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_jobs_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'jobs.xml';
        $content    = $this->getLayout()->createBlock('jobs/adminhtml_jobs_grid')
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