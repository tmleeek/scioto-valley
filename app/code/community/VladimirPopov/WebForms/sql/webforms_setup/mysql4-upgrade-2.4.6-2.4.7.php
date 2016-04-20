<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$webforms_table = 'webforms';

$edition = 'CE';
$version = explode('.', Mage::getVersion());
if ($version[1] >= 9)
	$edition = 'EE';

if((float)substr(Mage::getVersion(),0,3)>1.1 || $edition == 'EE')
	$webforms_table = $this->getTable('webforms/webforms');

$installer->run("
ALTER TABLE  `{$webforms_table}` ADD  `email_result_approval` TINYINT( 1 ) NOT NULL AFTER `email_reply_template_id`;
ALTER TABLE  `{$webforms_table}` ADD  `email_result_approved_template_id` TINYINT( 1 ) NOT NULL AFTER `email_result_approval`;
ALTER TABLE  `{$webforms_table}` ADD  `email_result_notapproved_template_id` TINYINT( 1 ) NOT NULL AFTER `email_result_approved_template_id`;
");

$installer->endSetup();