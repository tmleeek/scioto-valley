<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedsearch
 * @version    1.4.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedsearch_Model_Layer extends Mage_Catalog_Model_Layer
{
    public function getProductCollection()
    {
        $categoryId = $this->getCurrentCategory()->getId();
        if (isset($this->_productCollections[$categoryId])) {
            $collection = $this->_productCollections[$categoryId];
        } else {
            $collection = $this->prepareProductCollection(null);
            $this->_productCollections[$categoryId] = $collection;
        }
        return $collection;
    }

    protected function _getShowOutOfStock()
    {
        $_show = true;
        if (($_ciHelper = Mage::helper('cataloginventory')) && method_exists($_ciHelper, 'isShowOutOfStock')) {
            $_show = $_ciHelper->isShowOutOfStock();
        }
        return $_show;
    }

    public function prepareProductCollection($collection)
    {
        $index = Mage::helper('awadvancedsearch/catalogsearch')->getResults(
            AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG
        );
        if ($index) {
            $collection = $index->getResults();
            parent::prepareProductCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
            if (!$this->_getShowOutOfStock()) {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            }
        } else {
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addAttributeToFilter('entity_id', array('in' => -1));
        }
        return $collection;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()->addVisibleFilter();
        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Mage_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
}
