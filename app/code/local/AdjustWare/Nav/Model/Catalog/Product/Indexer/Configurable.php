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
class AdjustWare_Nav_Model_Catalog_Product_Indexer_Configurable extends Mage_Catalog_Model_Product_Indexer_Eav
{
    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('adjnav')->__('Configurable Product Attributes');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('adjnav')->__('Index configurable product attributes for layered navigation pro filtering');
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('adjnav/catalog_product_indexer_configurable');
    }
}