<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\   Jobs extension  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    Jobs                    \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 � free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */
class FME_Jobs_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function initControllerRouters($observer)
    {   	
        $front = $observer->getEvent()->getFront();
        $router = new FME_Jobs_Controller_Router();
        $front->addRouter('jobs', $router);
        
    }

     public function match(Zend_Controller_Request_Http $request)
    {
		if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
		
        
        $route = Mage::helper('jobs')->getListIdentifier();		
       	$identifier = trim($request->getPathInfo(), '/');		
        $identifier = str_replace(Mage::helper('jobs')->getSeoUrlSuffix(), '', $identifier);
        
        $ident_temp = explode('/',$identifier);
        $identifier_lastpart = $ident_temp[(int)(count($ident_temp)-1)];
        
        if ( $identifier == $route ) {
        	$request->setModuleName('jobs')
        			->setControllerName('index')
        			->setActionName('index');
        			
        	return true;		
        }   elseif ($identifier != $route) {
			$jobId = Mage::getModel('jobs/jobs')->checkIdentifier($identifier_lastpart);
        	if ( !$jobId ) {
            	return false;
        	}
			$request->setModuleName('jobs')
            		->setControllerName('index')
            		->setActionName('view')
            		->setParam('id', $jobId);
            		
			$request->setAlias(
					Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
					$identifier
			);
			
			return true;
			
		} elseif ( strpos($identifier, $route) === 0 && strlen($identifier) > strlen($route)) {
        	$identifier = trim(substr($identifier, strlen($route)), '/');  			
			$jobId = Mage::getModel('jobs/jobs')->checkIdentifier($identifier,Mage::app()->getStore()->getId());
        	if ( !$jobId ) {
            	return false;
        	}
        	$request->setModuleName('jobs')
            		->setControllerName('index')
            		->setActionName('index')
            		->setParam('id', $jobId);
            		
			$request->setAlias(
					Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
					$identifier
			);
			return true;
        }  
       
        return false;
    }
}