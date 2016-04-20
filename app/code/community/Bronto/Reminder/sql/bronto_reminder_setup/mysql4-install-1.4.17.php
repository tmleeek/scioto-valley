<?php

$installer = $this;

$installer->startSetup();

try {
    $installer->createTables();
} catch (Exception $e) {
    Mage::helper('bronto_reminder')->writeError('Failed to create tables: ' . $e->getMessage());
}

$installer->endSetup();
