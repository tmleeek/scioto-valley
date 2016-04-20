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
 * @author     King Kaleem Khan<kaleem.ullah@unitedsol.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
 
class FME_Jobs_FbconnectController extends Mage_Core_Controller_Front_Action
{  

    public function postAction()
    {
       
	if ($data = $this->getRequest()->getPost()) {

        $fbmsg = $data['fbmsg'];
        $jobid = $data['jobid'];
//        echo "<pre>";
//        print_r($data); 
        if(isset($jobid)){
            $coll = Mage::getModel('jobs/jobs')->load($jobid)->getData();
            $jobtype = $coll['jobtype_name'];
            $temp_desc = $coll['description'];
            $jobtitle = $coll['jobtitle'];
            $job_url = $coll['jobs_url'];
            
            $urlsuffix = Mage::getStoreConfig('jobs/general/urlsuffix');
            if(trim($urlsuffix == ""))
            {
                $urlsuffix = '.html';
            }
            
            $identifier = Mage::getStoreConfig('jobs/general/identifier');
            if(trim($identifier == ""))
            {
                $identifier = 'jobs';
            }
            
            $appid = Mage::getStoreConfig('jobs/fbsettings/appid');
            $appsecret = Mage::getStoreConfig('jobs/fbsettings/secret');
            
            $fbimage = Mage::getStoreConfig('jobs/fbsettings/fbimage');
            $fbimage_link = Mage::getBaseUrl('media').'jobs/'.$fbimage;
            
            $linkback = Mage::getBaseUrl().'/'.$identifier.'/'.$job_url.''.$urlsuffix;
            
            $temp_desc = strip_tags($temp_desc);
            if( strlen($temp_desc) > 150 )
            {
                $job_desc = substr($temp_desc, 0 , 150).'...';
            }else {
                $job_desc = $temp_desc;
            }
        
        //exit;
        $base_url = Mage::getBaseDir('media').DS.'jobs'.DS;
        require_once ($base_url.'src'.DS.'facebook.php');
        
        $facebook = new Facebook(array(
                                'appId' => $appid,
                                'secret' => $appsecret,
                            ));

        // Get User ID


                    $user = $facebook->getUser();
                    if ($user) {
                        try {
        // Get the user profile data you have permission to view
                            $user_profile = $facebook->api('/me');
                            $uid = $facebook->getUser();


                            $url = $facebook->getLoginUrl(array(
                                        'canvas' => 1,
                                        'fbconnect' => 0,
                                        'req_perms' => 'email,publish_stream,status_update,user_birthday,user_location,user_work_history'));


                            $attachment = array
                                (
                                'access_token' => $facebook->getAccessToken(),
                                'message' => $fbmsg,
                                'name' => $jobtitle,
                                'caption' => $jobtype,
                                'link' => $linkback,
                                'description' => $job_desc,
                                'picture' => $fbimage_link
                            );


                            $result = $facebook->api('/me/feed/', 'post', $attachment);


                            //$_SESSION['userID'] = $uid;
                            echo "Job has been been posted on wall successfully.";
                            
                        } catch (FacebookApiException $e) {
                            $user = null;
                        }
                    } else {
                        die('Somethign Strange just happened <script>top.location.href="' . $facebook->getLoginUrl() . '";</script>');
                    }
                    
                    
        }//if job ID is set  
        else {
            echo "Sorry, Job not found to publish, please try again later";
        }
        
        
        
        }//if post
    }
    
    
    public function tweetAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            $consumer_key = Mage::getStoreConfig('jobs/twitter/consumer_key');
            $consumer_secret = Mage::getStoreConfig('jobs/twitter/consumer_secret');
            $user_token = Mage::getStoreConfig('jobs/twitter/user_token');
            $user_secret = Mage::getStoreConfig('jobs/twitter/user_secret');
            
            $jobid = $data['jobid'];
            
              $tweet_text = $data['twmsg'];
            
              $base_url = Mage::getBaseDir('media').DS.'jobs'.DS;
              require_once ($base_url.'tmhoauth'.DS.'tmhOAuth.php');

              $connection = new tmhOAuth(array(
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'user_token' => $user_token,
                'user_secret' => $user_secret,
              )); 

              // Make the API call
              $connection->request('POST', 
                $connection->url('1/statuses/update'), 
                array('status' => $tweet_text));

              echo $connection->response['code'];
        }
    }
    
    
    

    
}