<?php
$installer = $this;
$installer->startSetup();
$this->removeAttribute('catalog_category', 'meta_cate');
$installer->endSetup();