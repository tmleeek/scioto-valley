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

class Magebassi_Mbimageslider_Adminhtml_MbimagesliderController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('mbimageslider/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banner Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
	
		$this->_title($this->__('Mbimageslider'))
			->_title($this->__('Manage banner'));
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() 
	{
		$id     			= $this->getRequest()->getParam('id');		
		$model  			= Mage::getModel('mbimageslider/mbslider')->load($id);
		$mbslider_id 		= $model->getId();
		$mbslider_type 		= $model->getSlidertype();	

		if($mbslider_type=='imageslider'){
			$mbimageslider_model = Mage::getModel('mbimageslider/mbimageslider');
			$form_data 	= $mbimageslider_model->loadByField('sliderid',$mbslider_id);
			$this->_title($this->__('Edit Image Slider'))->_title($this->__('Edit Image Slider'));
		}				
		
        $this->_getSession()->setData(Magebassi_Mbimageslider_Helper_Data::FORM_DATA_KEY, $form_data);

		if ($model->getId() || $id == 0) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('mbimageslider_data', $model);
			
			$this->_title($this->__('Mbimageslider'))
				->_title($this->__('Manage banner'));
			if ($model->getId()){
				$this->_title($model->getBannername());
			}else{
				$this->_title($this->__('New Banner'));
			}

			$this->loadLayout();
			$this->_setActiveMenu('mbimageslider/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_edit'))
				->_addLeft($this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mbimageslider')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function slidertypeAction(){
		
		$result = array('text' => Mage::getSingleton('core/layout')
                    ->createBlock('mbimageslider/adminhtml_mbimageslider_edit_tab_general_typeoptions')
                    ->setData('type', $this->getRequest()->getParam('type'))                   
                    ->toHtml());        
        $this->getResponse()->setBody(Zend_Json::encode($result));
        return;
	}	
	
	public function addbannerAction()
	{	
		$this->_title($this->__('Add Banner'));	
		$this->loadLayout();
		$this->_setActiveMenu('mbimageslider/items');			

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_edit'))
			->_addLeft($this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_edit_tabs'));

		$this->renderLayout();	
	}
 
	public function newAction() {
		$this->_forward('edit');
	}

	public function addAction(){
		$this->_forward('addbanner');
	}
 
	public function saveAction() 
	{
		if ($data = $this->getRequest()->getPost()) 
		{			
		
			$mbslider_model = Mage::getModel('mbimageslider/mbslider');
			$mbslider_model->setData($data['banner_info'])->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($mbslider_model->getCreatedTime == NULL || $mbslider_model->getUpdateTime() == NULL) {
					$mbslider_model->setCreatedTime(now())->setUpdateTime(now());
				} else {
					$mbslider_model->setUpdateTime(now());
				}

				$mbslider_model->setSlidertype('imageslider');				
				$mbslider_model->save();
				$mbslider_id = $mbslider_model->getId();
				
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }						
			
			if($data['type_data']['filename']['delete']==1){
				$data['type_data']['filename']='';
			}
			elseif(is_array($data['type_data']['filename'])){
				$data['type_data']['filename']=$data['type_data']['filename']['value'];
			}			
		
			$file = new Varien_Io_File();			
			//$baseDir = Mage::getBaseDir();
			//$mediaDir = $baseDir.DS.'media';
			//$imageDir = $mediaDir.DS.'mbimages';
			$imageDir = Mage::getBaseDir('media') . DS .  'mbimages';
			$thumbimageyDir = Mage::getBaseDir('media').DS.'mbimages'.DS.'thumbs';
		
			if(!is_dir($imageDir)){
				$imageDirResult = $file->mkdir($imageDir, 0777);         
			}			
			if(!is_dir($thumbimageyDir)){
				$thumbimageDirResult = $file->mkdir($thumbimageyDir, 0777);     
			}			
		
		
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') 
			{
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
				
					// Any extention would work
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
				
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(true);
						
					// We set media as the upload dir
					//$path = Mage::getBaseDir('media') . DS ;
					$path = $imageDir . DS ;
					$result = $uploader->save($path, $_FILES['filename']['name']);
					$file = str_replace(DS, '/', $result['file']);
					###############################################################################
					// actual path of image
					$imageUrl = Mage::getBaseDir('media').DS."mbimages".$file;
					 
					// path of the resized image to be saved
					// here, the resized image is saved in media/resized folder
					$imageResized = Mage::getBaseDir('media').DS."mbimages".DS."thumbs".DS."mbimages".$file;					
					 
					// resize image only if the image file exists and the resized image file doesn't exist
					// the image is resized proportionally with the width/height 135px
					if (!file_exists($imageResized)&&file_exists($imageUrl)) :
						$imageObj = new Varien_Image($imageUrl);
						$imageObj->constrainOnly(TRUE);
						$imageObj->keepAspectRatio(FALSE);
						$imageObj->keepFrame(FALSE);
						$imageObj->quality(100);
						$imageObj->resize(80, 50);
						$imageObj->save($imageResized);
					endif;				
				
					$data['type_data']['filename'] = 'mbimages'.$file;
				} catch (Exception $e) {
					$data['type_data']['filename'] = 'mbimages'.'/'.$_FILES['filename']['name'];
				}
			}
		
			$data['type_data']['sliderid'] = $mbslider_id;
			$imageslider_model = Mage::getModel('mbimageslider/mbimageslider');
			$imagecollection = $imageslider_model->loadByField('sliderid',$this->getRequest()->getParam('id'));
			$imagesliderid = $imagecollection['imageslider_id'];
			$imageslider_model->setData($data['type_data'])->setImagesliderId ($imagesliderid);
			
			try {
				if ($imageslider_model->getCreatedTime == NULL || $imageslider_model->getUpdateTime() == NULL) {
					$imageslider_model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$imageslider_model->setUpdateTime(now());
				}
				$imageslider_model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mbimageslider')->__('Banner Image was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);				

				if ($this->getRequest()->getParam('back')) {					
					$this->_redirect('*/*/edit', array('id' => $mbslider_model->getId()));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mbimageslider')->__('Unable to save Banner Image'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() 
	{
		if( $this->getRequest()->getParam('id') > 0 ) 
		{
			try {
				$sId   = $this->getRequest()->getParam('id');
				$model = Mage::getModel('mbimageslider/mbslider')->load($sId);				 
				$model->delete();
				
				$stype = $model->getSlidertype();				
				
				if($stype=='imageslider'){
					$imageslider_model = Mage::getModel('mbimageslider/mbimageslider');
					$imagecollection   = $imageslider_model->loadByField('sliderid',$sId);
					$imagesliderid     = $imagecollection['imageslider_id'];
					$delColl 		   = $imageslider_model->load($imagesliderid);
					$delColl->delete();
				}
				
				$mbseclist_model 	 = Mage::getModel('mbimageslider/mbseclist');
				$mbseclistCollection = $mbseclist_model->getCollection()->addFieldToFilter('selected_list',$sId);				
				foreach($mbseclistCollection as $mbs){
					$delColl	 = $mbseclist_model->load($mbs['id']);
					$delColl->delete();
				}
				
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully deleted'));
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
        $sliderIds = $this->getRequest()->getParam('mbimageslider');				
        if(!is_array($sliderIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select banner(s)'));
        } else {
            try {
                foreach ($sliderIds as $sId) 
				{
                    $mbslider = Mage::getModel('mbimageslider/mbslider')->load($sId);
					$mbslider->delete();
					 
					$stype = $mbslider->getSlidertype();					
					if($stype=='imageslider'){
						$imageslider_model = Mage::getModel('mbimageslider/mbimageslider');
						$imagecollection   = $imageslider_model->loadByField('sliderid',$sId);
						$imagesliderid     = $imagecollection['imageslider_id'];
						$delColl 		   = $imageslider_model->load($imagesliderid);
						$delColl->delete();
					}
					
					$mbseclist_model 	 = Mage::getModel('mbimageslider/mbseclist');
					$mbseclistCollection = $mbseclist_model->getCollection()->addFieldToFilter('selected_list',$sId);				
					foreach($mbseclistCollection as $mbs){
						$delColl	 = $mbseclist_model->load($mbs['id']);
						$delColl->delete();
					}
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d banner record(s) were successfully deleted', count($sliderIds)
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
        $sliderIds = $this->getRequest()->getParam('mbimageslider');
        if(!is_array($sliderIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select banner(s)'));
        } else {
            try {
                foreach ($sliderIds as $sId) {
                    $imageslider = Mage::getSingleton('mbimageslider/mbslider')
                        ->load($sId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d banner record(s) were successfully updated', count($sliderIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'mbimageslider.csv';
        $content    = $this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'mbimageslider.xml';
        $content    = $this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_grid')
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