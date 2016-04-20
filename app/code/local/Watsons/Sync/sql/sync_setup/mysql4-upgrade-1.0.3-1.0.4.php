<?php
$installer = $this;

$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('sales/order'), 'retail_id', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 50,
        'nullable'  => true,
        'comment'   => 'Retail Id'
    ));
$installer->endSetup();