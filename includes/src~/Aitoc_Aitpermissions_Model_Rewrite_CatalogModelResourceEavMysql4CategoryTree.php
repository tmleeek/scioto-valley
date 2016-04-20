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
/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitmanufacturers% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitmanufacturers')){
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree_Aittmp extends Aitoc_Aitmanufacturers_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree_Aittmp extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpermissions_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree extends Aitoc_Aitpermissions_Model_Rewrite_CatalogModelResourceEavMysql4CategoryTree_Aittmp
{
    protected function _updateAnchorProductCount(&$data)
    {
        foreach ($data as $key => $row)
        {
            if (isset($row['is_anchor']) && 0 === (int)$row['is_anchor'])
            {
                $data[$key]['product_count'] = $row['self_product_count'];
            }
        }
    }
}