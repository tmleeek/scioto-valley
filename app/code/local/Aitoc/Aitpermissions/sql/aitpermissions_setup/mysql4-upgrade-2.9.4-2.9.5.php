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
$installer = $this;

$installer->startSetup();

$installer->run($sql = "
ALTER TABLE `{$this->getTable('aitoc_aitpermissions_advancedrole')}` ADD `manage_orders_own_products_only` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;
");
$installer->endSetup();