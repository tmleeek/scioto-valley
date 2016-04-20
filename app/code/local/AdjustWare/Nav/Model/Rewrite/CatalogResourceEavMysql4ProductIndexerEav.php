<?php
/**
 * Layered Navigation Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Nav
 * @version      2.5.4
 * @license:     yc4tx3fdyujjEs5czyndvhoc8zpLrKl3OCuGehtGvM
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Nav_Model_Rewrite_CatalogResourceEavMysql4ProductIndexerEav extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
{
    protected function _construct()
    {
        parent::_construct();

        $this->getIndexers();
        $this->_types['configurable'] = Mage::getResourceModel('adjnav/catalog_product_indexer_configurable');
    }
}