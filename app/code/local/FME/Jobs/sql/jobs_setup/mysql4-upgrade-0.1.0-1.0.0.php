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

-- DROP TABLE IF EXISTS {$this->getTable('jobs/jobs')};
CREATE TABLE {$this->getTable('jobs/jobs')} (                                                                                                                                          
            `jobs_id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                                                                                              
            `jobtitle` varchar(255) DEFAULT NULL,                                                                                                                            
            `description` text,                                                                                                                                              
            `jobtype_name` varchar(255) DEFAULT NULL,                                                                                                                        
            `store_name` varchar(255) DEFAULT NULL,                                                                                                                          
            `department_name` varchar(255) DEFAULT NULL,                                                                                                                     
            `gender` tinyint(4) DEFAULT NULL,                                                                                                                                
            `positions_jobs` tinyint(5) DEFAULT NULL,                                                                                                                        
            `jobs_url` varchar(255) DEFAULT NULL,                                                                                                                            
            `career_level` varchar(255) DEFAULT NULL,                                                                                                                        
            `min_qual` varchar(255) DEFAULT NULL,                                                                                                                            
            `min_exp` varchar(255) DEFAULT NULL,                                                                                                                             
            `travel` tinyint(4) DEFAULT NULL,                                                                                                                                
            `apply_by` date DEFAULT NULL,                                                                                                                                    
            `skills` text,                                                                                                                                                   
            `status` tinyint(5) NOT NULL DEFAULT '1',                                                                                                                        
            `meta_title` varchar(255) DEFAULT NULL,                                                                                                                          
            `meta_keywords` text,                                                                                                                                            
            `meta_desc` text,                                                                                                                                                
            `create_dates` date NOT NULL,                                                                                                                                     
            PRIMARY KEY (`jobs_id`),                                                                                                                                                                                                                               
            UNIQUE KEY `jobs_url` (`jobs_url`),                                                                                                                              
            UNIQUE KEY `JOBS_TITLE` (`jobtitle`),                                                                                                                            
            KEY `FK_store_name` (`store_name`),                  
            KEY `FK_department_name` (`department_name`),        
            KEY `jobs_jobtype_1` (`jobtype_name`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 ");

$installer->setConfigData('jobs/general/enable','1');


$installer->endSetup(); 