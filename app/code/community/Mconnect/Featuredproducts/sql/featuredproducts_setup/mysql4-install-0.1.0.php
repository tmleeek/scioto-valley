<?php
/**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 *
 * @category   Catalog
 * @package   Mconnect_Featuredproducts
 * @author      M-Connect Solutions (http://www.magentoconnect.us)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mcsfeaturedproducts')};
CREATE TABLE {$this->getTable('mcsfeaturedproducts')} (
  `featuredproducts_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `featuredstatus` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`featuredproducts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 
