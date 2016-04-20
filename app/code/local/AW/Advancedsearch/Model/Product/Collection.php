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


class AW_Advancedsearch_Model_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * This flag shows is url rewrites has been
     * already added to collection or not.
     * @var bool
     */
    protected $_isUrlRewritesAdded = false;
    protected $_sphinxMatchedIds = array();

    public function addUrlRewrites()
    {
        if (!$this->_isUrlRewritesAdded) {
            $this->getSelect()->joinLeft(
                array('urwr' => $this->getTable('core/url_rewrite')),
                '(urwr.product_id=e.entity_id) AND (urwr.store_id=' . $this->getStoreId() . ')',
                array('request_path')
            );
            $this->groupByAttribute('entity_id');
            $this->_isUrlRewritesAdded = true;
        }
        return $this;
    }

    /**
     * Selecting products from multiple categories
     * @param string $categories categories list separated by commas
     * @param bool $includeSubCategories = false
     * @return AW_Advancedsearch_Model_Product_Collection
     */
    public function addCategoriesFilter($categories, $includeSubCategories = false)
    {
        if (!is_array($categories)) {
            $categories = @explode(',', $categories);
        }
        $sqlCategories = array();
        if ($includeSubCategories) {
            foreach ($categories as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $sqlCategories = array_merge($sqlCategories, $category->getAllChildren(true));
            }
        } else {
            $sqlCategories = $categories;
        }
        $sqlCategories = array_unique($sqlCategories);
        if (is_array($sqlCategories)) {
            $categories = @implode(',', $sqlCategories);
        }
        $alias = 'cat_index';
        $categoryCondition = $this->getConnection()->quoteInto(
            $alias . '.product_id=e.entity_id'
            . ($includeSubCategories ? '' : ' AND ' . $alias . '.is_parent=1')
            . ' AND ' . $alias . '.store_id=? AND ',
            $this->getStoreId()
        );
        $categoryCondition .= $alias . '.category_id IN (' . $categories . ')';
        $this->getSelect()->joinInner(
            array($alias => $this->getTable('catalog/category_product_index')),
            $categoryCondition,
            array('position' => 'position')
        );
        $this->_categoryIndexJoined = true;
        $this->_joinFields['position'] = array('table' => $alias, 'field' => 'position');

        return $this;
    }

    public function addFilterByIds($ids)
    {
        if ($ids) {
            $whereString = '(e.entity_id IN (';
            $whereString .= implode(',', $ids);
            $whereString .= '))';
            $this->getSelect()->where($whereString);
        }
        return $this;
    }

    /**
     * Covers bug in Magento function
     * @return Varien_Db_Select
     */
    //TODO wrong getSize method
    public function getSelectCountSql()
    {
        $catalogProductFlatHelper = Mage::helper('catalog/product_flat');
        if ($catalogProductFlatHelper && $catalogProductFlatHelper->isEnabled()) {
            return parent::getSelectCountSql();
        }
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }

    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute === 'relevance') {
            Mage::helper('awadvancedsearch/results')
                ->orderCollectionByRelevance($this, 'e.entity_id', $this->_sphinxMatchedIds, $dir);
            return $this;
        }
        return parent::addAttributeToSort($attribute, $dir);
    }

    public function setSizeTo($size)
    {
        $this->_totalRecords = $size;
        return $this;
    }

    public function addEntityIdsFilter($ids)
    {
        $this->_sphinxMatchedIds = $ids;
        $this->addAttributeToFilter('entity_id', array('in' => $ids));
        return $this;
    }
}
