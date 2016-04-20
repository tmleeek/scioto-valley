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


class AW_Advancedsearch_Model_Catalogindexes extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('awadvancedsearch/catalogindexes');
    }

    /**
     * Unserialize database fields
     * @return AW_Advancedsearch_Model_Catalogindexes
     */
    protected function _afterLoad()
    {
        if ($this->getData('attributes') && is_string($this->getData('attributes')))
            $this->setData('attributes', @unserialize($this->getData('attributes')));
        if ($this->getData('store') !== null && !is_array($this->getData('store')))
            $this->setData('store', @explode(',', $this->getData('store')));
        return parent::_afterLoad();
    }

    /**
     * Serialize fields for database storage
     * @return AW_Advancedsearch_Model_Catalogindexes
     */
    protected function _beforeSave()
    {
        if ($this->getData('attributes') && is_array($this->getData('attributes'))) {
            $this->setData('attributes', @serialize($this->getData('attributes')));
        }
        if ($this->getData('store') !== null && is_array($this->getData('store'))) {
            $storeIds = @implode(',', $this->getData('store'));
            $this->setData('store', $storeIds ? $storeIds : 0);
        }
        return parent::_beforeSave();
    }

    public function callAfterLoad()
    {
        return $this->_afterLoad();
    }

    /**
     * @return AW_Advancedsearch_Model_Indexer_Abstract|null
     */
    public function getIndexer()
    {
        switch ($this->getData('type')) {
            case AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG:
                return Mage::getModel('awadvancedsearch/indexer_catalog')->setIndex($this);
                break;
            case AW_Advancedsearch_Model_Source_Catalogindexes_Types::CMS_PAGES:
                return Mage::getModel('awadvancedsearch/indexer_cms_pages')->setIndex($this);
                break;
            case AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_BLOG:
                return Mage::getModel('awadvancedsearch/indexer_awblog')->setIndex($this);
                break;
            case AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_KBASE:
                return Mage::getModel('awadvancedsearch/indexer_awkbase')->setIndex($this);
                break;
        }
        return null;
    }

    public function getIndexName()
    {
        $key = (string)Mage::getConfig()->getNode('global/crypt/key');
        return 'awasi' . md5($key) . $this->getId();
    }

    public function setLastUpdate($time = null)
    {
        $date = date(AW_Advancedsearch_Model_Mysql4_Catalogindexes::MYSQL_DATETIME_FORMAT, $time ? $time : time());
        $this->setData('last_update', $date);
        $this->save();
        return $this;
    }

    public function getResultsCount()
    {
        $results = $this->getResults();
        if ($results) {
            return $results->getSize();
        }
        return null;
    }

    /**
     * @return AW_Advancedsearch_Helper_Results
     */
    protected function _getResultsHelper()
    {
        return Mage::helper('awadvancedsearch/results');
    }

    public function getResults()
    {
        if (!$this->getData('_results')) {
            $collection = null;
            switch ($this->getData('type')) {
                case AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_BLOG:
                    $matchedIds = $this->_getResultsHelper()->getMatchedIds($this);
                    $blogAPIModel = $this->getBlogAPI();
                    $collection = $blogAPIModel->getPosts(
                        array(AW_Blog_Model_Status::STATUS_ENABLED), array(Mage::app()->getStore()->getId())
                    );
                    $collection->addFieldToFilter('main_table.post_id', array('in' => $matchedIds));
                    $this->_getResultsHelper()
                        ->orderCollectionByRelevance($collection, 'main_table.post_id', $matchedIds);
                    break;
                case AW_Advancedsearch_Model_Source_Catalogindexes_Types::CMS_PAGES:
                    $matchedIds = $this->_getResultsHelper()->getMatchedIds($this);
                    $collection = Mage::getModel('cms/page')->getCollection();
                    $collection->addFieldToFilter(
                        AW_Advancedsearch_Model_Indexer_Cms_Pages::PRIMARY,
                        array('in' => $matchedIds)
                    );
                    $collection->addFieldToFilter('is_active', 1);
                    $collection->addStoreFilter(Mage::app()->getStore()->getId());
                    break;
                case AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG:
                    $matchedIds = $this->_getResultsHelper()->getMatchedIds($this);
                    $collection = Mage::getModel('awadvancedsearch/product_collection');
                    if ($matchedIds) {
                        $collection->addEntityIdsFilter($matchedIds);
                    } else {
                        $collection->addAttributeToFilter('entity_id', array('in' => -1));
                    }
                    break;
                case AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_KBASE:
                    if (!Mage::helper('awadvancedsearch')->canUseAWKBase()) {
                        break;
                    }
                    $matchedIds = $this->_getResultsHelper()->getMatchedIds($this);
                    $kBaseCollection = Mage::helper('awadvancedsearch')->getKbaseArticleModel();
                    $collection = $kBaseCollection->getCollection();
                    $collection->addStoreFilter(Mage::app()->getStore()->getId());
                    $collection->addFieldToFilter('main_table.article_id', array('in' => $matchedIds));
                    $this->_getResultsHelper()
                        ->orderCollectionByRelevance($collection, 'main_table.article_id', $matchedIds);
                    break;
            }
            if ($collection) {
                $this->setData('_results', $collection);
            }
        }
        return $this->getData('_results');
    }

    /**
     * @return AW_Blog_Model_Api|null
     */
    public function getBlogAPI()
    {
        if ($this->_blogAPIModel === null) {
            $this->_blogAPIModel = Mage::helper('awadvancedsearch')->getBlogAPIModel();
        }
        return $this->_blogAPIModel;
    }
}
