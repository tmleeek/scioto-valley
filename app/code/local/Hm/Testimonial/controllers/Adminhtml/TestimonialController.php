<?php

class Hm_Testimonial_Adminhtml_TestimonialController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('testimonial/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('testimonial/testimonial')->load($id);

        if ($model->getTestimonialId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('testimonial_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('testimonial/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit'))
                    ->_addLeft($this->getLayout()->createBlock('testimonial/adminhtml_testimonial_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('testimonial')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {        	
        	if(isset($data['delete_media']) && ($data['delete_media'] == 1 || $data['delete_media']==2) )
        	{
        		if($data['delete_media'] ==1)
        		$data['media'] = '';
        		if($data['delete_media'] ==2)
        		$data['media_url'] = '';
        	}
        	else
        	{
	            if (isset($_FILES['media']['name']) && $_FILES['media']['name'] != '') {
	                try {
	                    /* Starting upload */
	                    $uploader = new Varien_File_Uploader('media');
	
	                    // Any extention would work
	                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','bmp','avi','flv','swf','mp3','mp4','wmv'));
	                    $uploader->setAllowRenameFiles(false);
	
	                    // Set the file upload mode 
	                    // false -> get the file directly in the specified folder
	                    // true -> get the file in the product like folders 
	                    //	(file.jpg will go in something like /media/f/i/file.jpg)
	                    $uploader->setFilesDispersion(false);
	
	                    // We set media as the upload dir
	                    $path = Mage::getBaseDir('media') . DS . 'testimonial' . DS;
	                    $result = $uploader->save($path, $_FILES['media']['name']);
	                    						
	                   //this way the name is saved in DB
		                if(isset($result['file']))
						$data['media'] = 'testimonial/' . $result['file'];
						else 					
		                $data['media'] = 'testimonial/' . $_FILES['media']['name'];
	                
	                } catch (Exception $e) {
	                    
	                }
	
	            } else {
	                if (isset($data['media']['delete']) && $data['media']['delete'] == 1) {
	                    $data['media'] = '';
	                } else {
	                    unset($data['media']);
	                }
	            }
        	}
	            
            try {
			
				if($this->getRequest()->getParam('id'))
					$model = Mage::getModel('testimonial/testimonial')->load($this->getRequest()->getParam('id'));
				else
					$model = Mage::getModel('testimonial/testimonial');
				
				$model->setData($data)
						->setTestimonialId($this->getRequest()->getParam('id'));
				
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
					->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}

                $model->save();

                if (isset($data['stores'])) {
                    $stores = $data['stores'];
                } else {
                    $stores = array(null);
                }


                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('testimonial')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('testimonial')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('testimonial/testimonial');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if (!is_array($testimonialIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($testimonialIds as $testimonialId) {
                    $testimonial = Mage::getModel('testimonial/testimonial')->load($testimonialId);
                    $testimonial->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($testimonialIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if (!is_array($testimonialIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($testimonialIds as $testimonialId) {
                    $testimonial = Mage::getSingleton('testimonial/testimonial')
                            ->load($testimonialId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($testimonialIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction() {
        $fileName = 'testimonial.csv';
        $content = $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'testimonial.xml';
        $content = $this->getLayout()->createBlock('testimonial/adminhtml_testimonial_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}