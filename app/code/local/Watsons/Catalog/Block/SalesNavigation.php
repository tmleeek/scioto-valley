<?php

/**
 * Catalog navigation
 *
 * @category   Watsons
 * @package    Watsons_Catalog
 */
class Watsons_Catalog_Block_SalesNavigation extends Mage_Catalog_Block_Navigation
{
    /**
     * Modified to show only those under Sales, ID 8
     * 
     * Get categories of current store
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories()
    {
        $salesCategoryId = 8;
        $category = Mage::getModel('catalog/category'); //->load(8); // Online Sales
        /* @var $category Mage_Catalog_Model_Category */
        $recursionLevel  = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        
        return $category->getCategories($salesCategoryId, $recursionLevel);
    }


    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = Mage::getModel('catalog/category')->load(8); // Online Sales
        return $category;
    }
}
