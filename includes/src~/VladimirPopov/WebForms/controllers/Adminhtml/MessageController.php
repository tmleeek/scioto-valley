<?php
class VladimirPopov_WebForms_Adminhtml_MessageController
	extends Mage_Adminhtml_Controller_Action
{
	
	public function ajaxDeleteAction(){
		$id = $this->getRequest()->getParam('id');
		Mage::getModel('webforms/message')->load($id)->delete();
		
		$this->getResponse()->setBody('');
	}
	
	public function ajaxEmailAction(){
		$return = array(
			"success" => true,
			"errors" => ''
		);
		
		$message = Mage::getModel('webforms/message')->load($this->getRequest()->getParam('id'));
		
		$result = Mage::getModel('webforms/results')->load($message->getResultId());
				
		if($result->getCustomerEmail()){			
			$success = $message->sendEmail();
			
			if($success){
				$message->setIsCustomerEmailed(1)->save();
			}else{
				$return["errors"] = $this->__('E-mail could not be sent!');
			}
			
			$return["success"] = $success;
		} else {
			$return["errors"] = $this->__('Selected result has no reply-to address!');
		}
		
		if($return["errors"]) { $return["success"] = false;}
		
		$this->getResponse()->setBody(htmlspecialchars(json_encode($return), ENT_NOQUOTES));
	}
}
?>
