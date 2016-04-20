<?php

/**
 * Testimonial submit controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Hm_Testimonial_SubmitController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle('Testimonial submit');                                               
        }           
             
        $this->renderLayout();
    }
    
    public function postAction()
    {	
        $data = $this->getRequest()->getPost();	 
        $dataForm = new Varien_Object;      
        if (!empty($data)) {
            $session = Mage::getSingleton('core/session', array('name'=>'frontend'));            
        	if(isset($_FILES['media1']['name']) && $_FILES['media1']['name'] != '') {
				try {
				    if((int)Mage::getStoreConfig('hm_testimonial/general/maxfilesize') >0)
				    {
				    	$realMax = (int)Mage::getStoreConfig('hm_testimonial/general/maxfilesize');
				    	$max_Mb = $realMax.'M';
				    	$maxFileSize_Byte = Mage::helper('testimonial')->convertBytes($max_Mb);
				    	$data_media1 = $_FILES['media1'];
			    		if($data_media1['size']>$maxFileSize_Byte){	
			    			$dataForm->setData($data);
							$session->setFormData($dataForm);					
							$session->addError($this->__('The upload file size is too big for up load, it mustn\'t greater than ').$max_Mb);
							$this->_redirect('testimonial/submit/index');	
							return;								
						}
						else{
							$session->unsFormData();
						}
				    }	
												
					$uploader = new Varien_File_Uploader('media1');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','bmp','avi','flv','swf','mp3','mp4','wmv'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').DS.'testimonial'.DS;
					$result= $uploader->save($path, $_FILES['media1']['name'] );	
					if(isset($result['file']))
					$data['media'] = 'testimonial/' . $result['file'];
					else 					
	                $data['media'] = 'testimonial/' . $_FILES['media']['name'];					
					
				} catch (Exception $e) {
						$dataForm->setData($data);
						$session->setFormData($dataForm);											
						$session->addError($e->getMessage());
						$this->_redirect('testimonial/submit/index');	
						return;		      
		        }	
		       
			}
			
            if (!Mage::app()->isSingleStoreMode()) {
            	$data['stores'] = array(Mage::app()->getStore()->getId());
            }
                        
            $testimonial  = Mage::getModel('testimonial/testimonial')->setData($data);
            
        	if ($testimonial->getCreatedTime() == NULL || $testimonial->getUpdateTime() == NULL) {
				$testimonial->setCreatedTime(now())
					->setUpdateTime(now());
			} else {
				$testimonial->setUpdateTime(now());
			}
						
            $validate = $testimonial->validate();
            if ($validate === true) {               
                    if($testimonial->save()){
	                    $session->addSuccess($this->__('Your testimonial has been accepted for moderation'));
	                    $session->unsFormData();
	                   	if(Mage::getStoreConfig('hm_testimonial/email/enable')){
	                   		 try {
	                   			$this->sendmail($data);
	                   		 }
		                	catch (Exception $e) {		                    
			                   // $session->addError($e->getMessage());			                    
		                	}
	                   	}
                    }
                    else 
                    {
	                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
	                    $dataForm->setData($data);
                   		$session->setFormData($dataForm);                     
	                    $this->_redirect('testimonial/submit/index');  
	                    return;		
                    }
            }
            else {                                 
                if (is_array($validate)) {                   
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                   $dataForm->setData($data);
                   $session->setFormData($dataForm);                   
                   $this->_redirect('testimonial/submit/index');  
                   return;                                 
                }
                else {
                    $session->addError($this->__('Unable to post testimonial. Please, try again later.'));
                    $dataForm->setData($data);
				    $session->setFormData($dataForm);	                  
                    $this->_redirect('testimonial/submit/index');  
                    return;
                }
            }
        }
        
        if($session->getFormData())
        $session->unsFormData(); 
        $this->_redirect('testimonial/submit/index');
        return;
    }
    
	public function sendmail($data)
	{
	    // Transactional Email Template's ID
	    $templateId = Mage::getStoreConfig('hm_testimonial/email/template_email');
	 
	    // Set sender information          
	    $senderName = $data['client_name'];
	    $senderEmail = $data['email'];    
	    $sender = array('name' => $senderName,
	                'email' => $senderEmail);
	     
	    // Set recepient information
	    $recepientEmail = Mage::getStoreConfig('hm_testimonial/email/admin_email');
	    $recepientName = Mage::getStoreConfig('hm_testimonial/email/admin_name');       
	     
	    // Get Store ID    
	    $storeId = Mage::app()->getStore()->getId();
	 
	    // Set variables that can be used in email template
	    
	    $vars = array('client_name' => $data['client_name'],
	            'client_email' => $data['email'],
	    		'company' => $data['company'],	    		
	    		'address' => $data['address'],
	    		'testimonial' => $data['description'],
	    		);
	             
	    $translate  = Mage::getSingleton('core/translate');
	 
	    // Send Transactional Email
	    Mage::getModel('core/email_template')
	        ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);	             
	    $translate->setTranslateInline(true);   
	}
    
}
