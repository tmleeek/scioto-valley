<?php


 /* Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Jobs
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
 
class FME_Jobs_IndexController extends Mage_Core_Controller_Front_Action
{  
    const XML_PATH_ENABLED          = 'jobs/general/enable';
  
   /* public function preDispatch()
    {
        parent::preDispatch();
        if(!Mage::getStoreConfig(self::XML_PATH_ENABLED)) {
	    Mage::getSingleton('core/session')->addError(Mage::helper('jobs')->__('Sorry Jobss have been stopped temporarily'));
	    $this->norouteAction();
	}
    }*/
    public function indexAction()
    {

    
        $resource = Mage::getSingleton('core/resource');
           
             $jobstore = $resource->getTableName('job_store');  
	$storefilter = $this->getRequest()->getParam('store'); $departmentfilter = $this->getRequest()->getParam('department');
    	$collection=Mage::getModel('jobs/jobs')->getCollection();
        $collection->getSelect()
    ->join(
    $jobstore,
    'main_table.jobs_id = '.$jobstore.'.jobs_id');

  // echo $collection->getSelect()->__toString();

    $collection->addFieldToFilter('store_id',Mage::app()->getStore()->getStoreId());       
	$collection->addFieldToFilter('status',1);
	if($storefilter){
	    $collection->addFieldToFilter('store_name',$storefilter);
	}
	if($departmentfilter){
	    $collection->addFieldToFilter('department_name',$departmentfilter);
	}
	$order=$this->getRequest()->getParam('sort')?($this->getRequest()->getParam('sort')=='department'?'department_name':$this->getRequest()->getParam('sort')):'department_name';
	$collection->addOrder($order,'Asc');
	$itemsPerPage = Mage::helper('jobs')->getStoreConfig('limit');

		
		// Use paginator
		if ( $itemsPerPage != 0 ) {		
			$paginator = Zend_Paginator::factory((array)$collection->getData());
			$paginator->setCurrentPageNumber((int)$this->_request->getParam('page', 1))
					  ->setItemCountPerPage($itemsPerPage);
			Mage::register('jobs', $paginator);
			
		} else {
			Mage::register('jobs',$collection->getData());
		}
	
    	$this->loadLayout();
     $head = $this->getLayout()->getBlock('head');
     $head->setTitle(Mage::helper('jobs')->getjobsLabel());
     
        $this->renderLayout();
    }/*03475007597 / 4688*/
    
    public function viewAction()
    {
	$id=$this->getRequest()->getParam('id');
	$detail=Mage::getModel('jobs/jobs')->load($id);

	if($detail && $detail->getStatus()==1){
	    Mage::register('item',$detail);

	    $this->loadLayout();
	    $this->renderLayout();
	}
	else{
	    Mage::getSingleton('core/session')->addError(Mage::helper('jobs')->__('Sorry, No Such Jobs Found'));
	    $this->_forward('index');
	}
	
      
    }
    
    public function formsubmitAction()
    {
        
        
        if ($data = $this->getRequest()->getPost()) {
            
            $jobmodel = Mage::getModel('jobs/jobs')->load($data['job_id'])->getData();
            $job_url = $jobmodel['jobs_url'];
            
            $identifier = Mage::getStoreConfig('jobs/general/identifier');
            
            if(trim($identifier == ''))
            {
                $identifier = 'jobs';
            }
            
            $urlsuffix = Mage::getStoreConfig('jobs/general/urlsuffix');
            if(trim($urlsuffix == ''))
            {
                $urlsuffix = '.html';
            }
            
            
            
            //echo $data['captacha_code'];
            $captchaerror = false;
        
            if (Zend_Validate::is(trim($data['security_code']) , 'NotEmpty')) { 
                $captchaerror = true;
            }
            
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            
            if (!$captchaerror or $data['security_code']!= $data['captacha_code']) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('core/session')->addError("The CAPTCHA you entered was incorrrect.");
                     Mage::getSingleton('core/session')->setFormData($data);
                     $this->_redirect($identifier.'/'.$job_url.''.$urlsuffix);
                     return;
            }
                                
                                
            if(isset($_FILES['cvfile']['name']) && $_FILES['cvfile']['name'] != '') {
                try {	
                    /* Starting upload */	
                    $uploader = new Varien_File_Uploader('cvfile');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('doc','pdf','docx'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ."jobs". DS. "cv". DS;
                    $rnum = mt_rand(0,999999);
                    
                    $temp_filename = str_replace(' ', '_', $_FILES['cvfile']['name']); 
                    
                    $temp_filename_1 = substr($temp_filename, 0, strlen($temp_filename)-4);
                    
                    $temp_filename_2 = substr($temp_filename, strripos($temp_filename, '.'), strlen($temp_filename));
                    
                    $cv_filename = $temp_filename_1."_".$rnum.$temp_filename_2;
                    
                    $uploader->save($path, $cv_filename );

                } catch (Exception $e) {
                     Mage::getSingleton('core/session')->addError($e->getMessage());
                     Mage::getSingleton('core/session')->setFormData($data);
                     //$this->_redirect('*/*/view', array('id' => $data['job_id']));
                     $this->_redirect($identifier.'/'.$job_url.''.$urlsuffix);
                     return;
            }

            //this way the name is saved in DB
                    $data['cvfile'] = $cv_filename;
            }
            
            
            
            $model = Mage::getModel('jobs/jobsapplications');		
            $model->setData($data);
            
            try {
                if (!$model->getCreateDate()) {
                        $model->setCreateDate(now());

                } 	
//                echo "<pre>";
//                print_r($model); exit;
                $model->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('jobs')->__('Application was successfully submitted.'));
                Mage::getSingleton('core/session')->setFormData(false);
                
                $client_email_enabled = Mage::getStoreConfig('jobs/email_settings/enable_client_notification');
                if($client_email_enabled)
                {
                    $this->notifybyemail($jobmodel, $data['email'], $data['fullname']);
                }
                
                $admin_email_enabled = Mage::getStoreConfig('jobs/email_settings/enable_admin_notification');
                if($admin_email_enabled)
                {
                    $this->notifyadminbyemail($jobmodel, $data);
                }

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                Mage::getSingleton('core/session')->setFormData($data);
                //$this->_redirect('*/*/view', array('id' => $data['job_id']));
                $this->_redirect($identifier.'/'.$job_url.''.$urlsuffix);
                return;
            }
            
            
            
        }//if data Posted
        else {
            Mage::getSingleton('core/session')->addError(Mage::helper('jobs')->__('Form was not submitted properly.'));
                $this->_redirect('*/*/');
                return;
        }
    }
    
    
    public function notifybyemail($jobmodel, $email_addr, $applicant_name){ 
        //$email_addr = Mage::getStoreConfig('iacf/iacf_config/email_addr');
        //$email_addr = 'kaleem.ullah@unitedsol.net';
        
        $c_subject	=	Mage::getStoreConfig('jobs/email_settings/client_email_subject');
        $app_template	=	Mage::getStoreConfig('jobs/email_settings/client_email_template');

        $sender_id	=	Mage::getStoreConfig('jobs/email_settings/email_sender');
        $sender_email	=	'owner@example.com';
        $sender_name	=	'Owner';

        if($sender_id == 'general'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_general/name');

        }elseif($sender_id == 'sales'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');

        }elseif($sender_id == 'support'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_support/name');

        }elseif($sender_id == 'custom1'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_custom1/name');

        }elseif($sender_id == 'custom2'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_custom2/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_custom2/name');
        }
        
        
        
        $emailTemplate  = Mage::getModel('core/email_template')
                                ->loadDefault($app_template);                                

        //Create an array of variables to assign to template
        $emailTemplateVariables = array();
        //$emailTemplateVariables['name'] = 'Branko';
        $emailTemplateVariables['applicant_name'] = $applicant_name;
        $emailTemplateVariables['from'] = $sender_name;
        $emailTemplateVariables['jobtitle'] = $jobmodel['jobtitle'];

        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        /*
         * Or you can send the email directly,
         * note getProcessedTemplate is called inside send()
         */
        $emailTemplate->setSenderName($sender_name);
        $emailTemplate->setSenderEmail($sender_email);
        $emailTemplate->setTemplateSubject($c_subject);
        $emailTemplate->send($email_addr, $applicant_name, $emailTemplateVariables);
    }
    
    
    
    public function notifyadminbyemail($jobmodel, $data){ 
        //$email_addr = Mage::getStoreConfig('iacf/iacf_config/email_addr');
        //$email_addr = 'kaleem.ullah@unitedsol.net';
        
        $email_addr	=	Mage::getStoreConfig('jobs/email_settings/admin_email');
        $c_subject	=	Mage::getStoreConfig('jobs/email_settings/admin_email_subject');
        $app_template	=	Mage::getStoreConfig('jobs/email_settings/admin_email_template');

        $sender_id	=	Mage::getStoreConfig('jobs/email_settings/email_sender');
        $sender_email	=	'owner@example.com';
        $sender_name	=	'Owner';

        if($sender_id == 'general'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_general/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_general/name');

        }elseif($sender_id == 'sales'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_sales/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_sales/name');

        }elseif($sender_id == 'support'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_support/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_support/name');

        }elseif($sender_id == 'custom1'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_custom1/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_custom1/name');

        }elseif($sender_id == 'custom2'){
                $sender_email = Mage::getStoreConfig('trans_email/ident_custom2/email');
                $sender_name = Mage::getStoreConfig('trans_email/ident_custom2/name');
        }
        
        
        
        $emailTemplate  = Mage::getModel('core/email_template')
                                ->loadDefault($app_template);                                

        //Create an array of variables to assign to template
        $emailTemplateVariables = array();
        //$emailTemplateVariables['name'] = 'Branko';
        $emailTemplateVariables['from'] = $sender_name;
        $emailTemplateVariables['applicant_name'] = $data['fullname'];
        $emailTemplateVariables['email'] = $data['email'];
        $emailTemplateVariables['dob'] = $data['dob'];
        $emailTemplateVariables['nationality'] = $data['pob'];
        $emailTemplateVariables['telephone'] = $data['telephone'];
        $emailTemplateVariables['address'] = $data['address'];
        $emailTemplateVariables['zipcode'] = $data['zipcode'];
        $emailTemplateVariables['jobtitle'] = $jobmodel['jobtitle'];

        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        /*
         * Or you can send the email directly,
         * note getProcessedTemplate is called inside send()
         */
        $emailTemplate->setSenderName($sender_name);
        $emailTemplate->setSenderEmail($sender_email);
        $emailTemplate->setTemplateSubject($c_subject);
        $emailTemplate->send($email_addr, $applicant_name, $emailTemplateVariables);
    }
    
}