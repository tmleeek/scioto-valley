<?php

$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbslider')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `bannername` varchar(255) NOT NULL default '',  
  `status` smallint(6) NOT NULL default '0',  
  `slidertype` varchar(255) NOT NULL default '', 
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbimageslider')} (
  `imageslider_id` int(11) unsigned NOT NULL auto_increment,
  `sliderid` int(11) NULL,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NULL,  
  `weblink` varchar(255) NULL,
  `linktarget` varchar(255) NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,   
  PRIMARY KEY (`imageslider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbgroups')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `groupname` varchar(655) NULL,  
  `groupstatus` int(11) NULL,  
  `effect` varchar(255) NULL,
  `slidingtime` int(11) NULL,  
  `slidingeffecttime` int(11) NULL,  
  `imagewidth` varchar(255) NULL,  
  `imageheight` varchar(255) NULL,  
  `description` int(11) NULL,  
  `thumbnails` varchar(255) NULL,  
  `loader` varchar(255) NULL,  
  `navigation` int(11) NULL,  
  `locationtype` varchar(255) NULL,   
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbseclist')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,  
  `selected_list` int(11) NOT NULL,  
  `position` int(11) NOT NULL default '0',  
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbcmspages')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `page_id` smallint(6) NOT NULL,  
  `group_id` smallint(6) NOT NULL,   
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbcategorypages')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` smallint(6) NOT NULL,  
  `category_ids` smallint(6) NOT NULL,   
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbproductpages')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` smallint(6) NOT NULL,  
  `product_ids` smallint(6) NOT NULL,   
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->run("
CREATE TABLE {$this->getTable('magebassi_mbgroupstores')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `group_id` smallint(6) NOT NULL,  
  `store_id` smallint(6) NOT NULL,   
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 