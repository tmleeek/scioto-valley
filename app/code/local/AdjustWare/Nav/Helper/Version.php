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
/**
 * 
 * @author ksenevich@aitoc.com
 */
class AdjustWare_Nav_Helper_Version extends Mage_Core_Helper_Abstract
{
    public function getProductIdChildColumn()
    {
        return 'child_id';
    }

    public function getProductRelationTable()
    {
        return 'catalog/product_relation';
    }
    
    public function getBaseIndexTable()
    {
        return 'catalog_product_index_eav';
    }

    /**
     * 
     * @return boolean
     */
    public function isNewReindexAllMethod()
    {
        return (boolean)(version_compare(Mage::getVersion(), '1.4.1') >= 0);
    }

}