<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.10.1
 * @license:     Z2INqHJ2yDwAS29S2ymsavGhKUg3g8KJsjTqD848qH
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD `storeview_ids` text NOT NULL AFTER `store_id` ;

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` DROP INDEX `role_id` ;

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` DROP INDEX `store_id` ;

ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD INDEX ( `store_id` ) ;

");

$RoleCollection = Mage::getModel('aitpermissions/advancedrole')->getCollection();

foreach ($RoleCollection as $Role)
{
    if ($Role->getStoreId()) 
    {
    	$StoreId = Mage::getModel('core/store')->load($Role->getStoreId())->getGroupId();

    	$Role->setData('storeview_ids', $Role->getStoreId());
    	$Role->setData('store_id', $StoreId);
    	
    	$Role->save();
    }
}

$installer->endSetup();