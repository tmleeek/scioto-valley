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
  //DROP TABLE IF EXISTS {$this->getTable('jobs/jobs')};
    //CREATE TABLE {$this->getTable('jobs/jobs')} (
    //  `jobs_id` int(11) unsigned NOT NULL auto_increment,
    //  `description` TEXT ,
    //  `store` smallint(11) NULL,
    //  `department` smallint(11) NULL,
    //  `status` TINYINT( 5 ) NOT NULL DEFAULT '1',
    //  `create_date` date NOT NULL,
    //  PRIMARY KEY (`jobs_id`)
    //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
$installer->run("
 DROP TABLE IF EXISTS {$this->getTable('jobs/store')};
CREATE TABLE {$this->getTable('jobs/store')} (                         
                  `store_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
                  `store_name` varchar(255) NOT NULL DEFAULT '',        
                  `description` text,                                   
                  `status` tinyint(4) DEFAULT NULL,                     
                  `create_date` date NOT NULL,                          
                  PRIMARY KEY (`store_id`,`store_name`),                
                  KEY `FK_store_name` (`store_name`)                    
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
  DROP TABLE IF EXISTS {$this->getTable('jobs/department')};
    CREATE TABLE {$this->getTable('jobs/department')} (                         
           `department_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
           `department_name` varchar(255) NOT NULL DEFAULT '',        
           `description` text,                                        
           `status` tinyint(4) DEFAULT NULL,                          
           `create_date` date NOT NULL,                               
           PRIMARY KEY (`department_id`,`department_name`),           
           KEY `FK_department_name` (`department_name`)               
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
 DROP TABLE IF EXISTS {$this->getTable('jobs/jobtype')};
CREATE TABLE {$this->getTable('jobs/jobtype')} (                         
              `jobtype_id` int(11) unsigned NOT NULL AUTO_INCREMENT,  
              `jobtype_name` varchar(255) NOT NULL DEFAULT '',        
              `description` text,                                   
              `status` tinyint(4) DEFAULT NULL,                     
              `create_date` date NOT NULL,                          
              PRIMARY KEY (`jobtype_id`,`jobtype_name`),                
              KEY `FK_jobtype_name` (`jobtype_name`)                    
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
 DROP TABLE IF EXISTS {$this->getTable('jobs/job_store')};               
CREATE TABLE {$this->getTable('jobs/job_store')} (                    
             `jobs_id` int(11) unsigned NOT NULL,       
             `store_id` smallint(5) unsigned NOT NULL,  
             PRIMARY KEY (`jobs_id`,`store_id`),        
             KEY `FK_EVENTS_STORE_STORE` (`store_id`)   
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

");

$installer->setConfigData('jobs/general/enable','1');


$installer->endSetup(); 