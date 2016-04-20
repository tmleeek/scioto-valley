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

$catalogSetup = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$catalogSetup->updateAttribute('catalog_product', 'created_by', 'is_visible', '0'); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'source_model', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_label', ''); 
$catalogSetup->updateAttribute('catalog_product', 'created_by', 'frontend_input', ''); 

$installer->endSetup();