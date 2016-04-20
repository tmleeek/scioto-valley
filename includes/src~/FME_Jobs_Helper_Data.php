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
 
class FME_Jobs_Helper_Data extends Mage_Core_Helper_Abstract
{
	const EXT_STATUS_ENABLE 							   = 'jobs/general/enable';
    const XML_PATH_LIST_IDENTIFIER                         = 'jobs/general/identifier';
    const XML_PATH_SEO_URL_SUFFIX                          = 'jobs/general/urlsuffix';
    
    public function getextStatus()
    {
    	return Mage::getStoreConfig(self::EXT_STATUS_ENABLE);
    }
    public function getscopeid()
		{

			 $resource = Mage::getSingleton('core/resource');
             $read= $resource->getConnection('core_read');
			 $jobstore = $resource->getTableName('job_store');	
			 $qry = "select store_id FROM ".$jobstore." "; 
             $rest = $read->fetchAll($qry);
             foreach ($rest as $value) {
             	$scope_ids[] = $value['store_id'];
             }
             return $scope_ids;
		}		
    public function getStoreConfig($path)
    {
        if($label=Mage::getStoreConfig('jobs/general/' . $path)){
            return $label;
        }
        return;
    }
 	public function deleteshit($articlesid)
	{
			
		    $resource = Mage::getSingleton('core/resource');
		$write = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('jobs/job_store');
     $write->query("delete from ".$tableName." where jobs_id =".$articlesid."");
     return 0;
		 
	}
    public function getjobsLabel()
    {
        if($label=$this->getStoreConfig('label')){
            return $label;
        }
        return 'Jobs';
    }

    public function getjobsUrl()
    {
       $identifier = $this->getListIdentifier();
       $url = Mage::getUrl($identifier.'/');
        return $url;
    }
    public function manageUrl($new)
    {
        $crnturl=Mage::helper('core/url')->getCurrentUrl();
        $pos=strripos($crnturl,$new);
        $_pos=strripos($crnturl,'?');
        if($pos){$url_link='';
        $total=count($_GET);$i=0;
            foreach ($_GET as $key => $value)
            {$i++;
               $key = htmlspecialchars( $key );
               $value = htmlspecialchars( $value );
               if($key != $new ){
                 $url_link .= "$key=$value&";
               }
               
            }
            if(!empty($url_link)){$newurl=Mage::getBaseUrl().'jobs/?'.$url_link;}else{$newurl=Mage::getBaseUrl() . 'jobs/?';}
            return $newurl;
        }
        else{
           if($_pos){
            return Mage::helper('core/url')->getCurrentUrl() . '&';
           }else{
             return Mage::helper('core/url')->getCurrentUrl() . '?';
           }
        }
    }
    
    public function getListIdentifier()
    {
        $identifier = Mage::getStoreConfig(self::XML_PATH_LIST_IDENTIFIER);
        if ( !$identifier ) {
                $identifier = 'jobs';
        }
        return $identifier;
    }
    
    public function getSeoUrlSuffix()
    {
        $suffix = Mage::getStoreConfig(self::XML_PATH_SEO_URL_SUFFIX);
        if ($suffix == ''):
        
            $suffix = '.html';
        endif;
        
        return $suffix;
    }
    
    public function getModuleUrlPrefix()
    {
        $prefix = Mage::getStoreConfig(self::XML_PATH_LIST_IDENTIFIER);
        if ($prefix == ''):
        
            $prefix = 'jobs';
        endif;
        
        return $prefix;
    }
    
    
    
	/***************************************************************
     this function draws the path of FME_Faqs folder on local
     and returns the path to the frontend from where it is called
    ***************************************************************/
      public function getSecureImageUrl() {
		  
		$path = Mage::getBaseUrl('media');
		$pos =strripos($path,'media');
		$apppath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . 'fmejobs/fme_captcha' . '/'. 'captcha/';
		return $apppath;
       
    }
    /***************************************************************
     this function gets a new unique value by sending request to the
     assign_rand_value() function which returns a character and it
     adds the character in its variable and returns to the form at
     frontend
    ***************************************************************/
    
    function getNewrandCode($length) {
		
		if($length>0) { 
			$rand_id="";
			for($i=1; $i<=$length; $i++) {
				mt_srand((double)microtime() * 1000000);
				$num = mt_rand(1,36);
				$rand_id .= $this->assign_rand_value($num);
			}
		}
		return $rand_id;
	}
	
	function assign_rand_value($num)
	{
		//accepts 1 - 36
		switch($num) {
			case "1":
			 $rand_value = "a";
			break;
			case "2":
			 $rand_value = "b";
			break;
			case "3":
			 $rand_value = "c";
			break;
			case "4":
			 $rand_value = "d";
			break;
			case "5":
			 $rand_value = "e";
			break;
			case "6":
			 $rand_value = "f";
			break;
			case "7":
			 $rand_value = "g";
			break;
			case "8":
			 $rand_value = "h";
			break;
			case "9":
			 $rand_value = "i";
			break;
			case "10":
			 $rand_value = "j";
			break;
			case "11":
			 $rand_value = "k";
			break;
			case "12":
			 $rand_value = "z";
			break;
			case "13":
			 $rand_value = "m";
			break;
			case "14":
			 $rand_value = "n";
			break;
			case "15":
			 $rand_value = "o";
			break;
			case "16":
			 $rand_value = "p";
			break;
			case "17":
			 $rand_value = "q";
			break;
			case "18":
			 $rand_value = "r";
			break;
			case "19":
			 $rand_value = "s";
			break;
			case "20":
			 $rand_value = "t";
			break;
			case "21":
			 $rand_value = "u";
			break;
			case "22":
			 $rand_value = "v";
			break;
			case "23":
			 $rand_value = "w";
			break;
			case "24":
			 $rand_value = "x";
			break;
			case "25":
			 $rand_value = "y";
			break;
			case "26":
			 $rand_value = "z";
			break;
			case "27":
			 $rand_value = "0";
			break;
			case "28":
			 $rand_value = "1";
			break;
			case "29":
			 $rand_value = "2";
			break;
			case "30":
			 $rand_value = "3";
			break;
			case "31":
			 $rand_value = "4";
			break;
			case "32":
			 $rand_value = "5";
			break;
			case "33":
			 $rand_value = "6";
			break;
			case "34":
			 $rand_value = "7";
			break;
			case "35":
			 $rand_value = "8";
			break;
			case "36":
			 $rand_value = "9";
			break;
		}
		return $rand_value;
	}
	
	public function getUserName()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return trim($customer->getName());
    }

    public function getUserEmail()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return '';
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer->getEmail();
    }
	
	/**
	 * Splits images Path and Name
	 *
	 * Path=custom/module/images/
	 * Name=example.jpg
	 *
	 * @param string $imageValue
	 * @param string $attr
	 * @return string
	 */
	public function splitImageValue($imageValue,$attr="name"){
		$imArray=explode("/",$imageValue);

		$name=$imArray[count($imArray)-1];
		$path=implode("/",array_diff($imArray,array($name)));
		if($attr=="path"){
			return $path;
		}
		else
			return $name;

	}
    
    
}