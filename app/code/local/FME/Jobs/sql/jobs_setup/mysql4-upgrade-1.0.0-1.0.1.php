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
 
$installer = $this;

$installer->startSetup();

$installer->run("
       
-- DROP TABLE IF EXISTS {$this->getTable('fme_jobsapplications')};
CREATE TABLE {$this->getTable('fme_jobsapplications')} (                    
                        `app_id` bigint(20) NOT NULL AUTO_INCREMENT,           
                        `job_id` bigint(20) NOT NULL,                          
                        `fullname` varchar(255) DEFAULT NULL,                  
                        `email` varchar(255) DEFAULT NULL,                     
                        `dob` date DEFAULT NULL,                               
                        `nationality` varchar(255) DEFAULT NULL,               
                        `telephone` varchar(25) DEFAULT NULL,                  
                        `address` text,                                        
                        `zipcode` varchar(25) DEFAULT NULL,                    
                        `cvfile` varchar(255) DEFAULT NULL,                    
                        `comments` text,                                       
                        `create_date` date DEFAULT NULL,                       
                        PRIMARY KEY (`app_id`)                                 
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


 ");

$installer->endSetup(); 