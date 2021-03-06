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

class AW_Advancedsearch_Model_Observer
{
    public function pageLoadBeforeFront($observer)
    {
        if (Mage::helper('awadvancedsearch')->isEnabled() && Mage::helper('awadvancedsearch')->hasActiveIndexes()) {
            $node = Mage::getConfig()->getNode('global/helpers/catalogsearch/rewrite');
            $dNode = Mage::getConfig()->getNode('global/helpers/catalogsearch/drewrite/data');
            $node->appendChild($dNode);
            $node = Mage::getConfig()->getNode('global/helpers/catalogSearch/rewrite');
            $dNode = Mage::getConfig()->getNode('global/helpers/catalogSearch/drewrite/data');
            $node->appendChild($dNode);
            /* 1.4.1.1 and lower stub begin */
            if ($observer->getControllerAction() instanceof AW_Advancedsearch_ResultController
                && version_compare(Mage::getVersion(), '1.4.1.1', '<=')) {
                Mage::register('_singleton/catalog/layer', Mage::getModel('awadvancedsearch/layer'));
            }
            /* 1.4.1.1 and lower stub end */
        }
    }

    public function afterProductSave($observer)
    {
        if (Mage::helper('awadvancedsearch/config')->getGeneralEnabled()) {
            /** @var Mage_Catalog_Model_Product $product */
            $product = $observer->getProduct();
            $indexes = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
            $indexes
                ->addStatusFilter()
                ->addTypeFilter(AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG)
                ->addStoreFilter($product->getStoreIds())
            ;
            foreach ($indexes as $index) {
                /** @var AW_Advancedsearch_Model_Catalogindexes $index */
                $indexer = $index->getIndexer();
                if ($indexer) {
                    $indexer->updateData($product->getId());
                    Mage::helper('awadvancedsearch')->addIndexToDeltaReindex($index);
                }
            }
        }
    }

    protected function _processIndexes()
    {
        $indexes = Mage::helper('awadvancedsearch')->getIndexesToDeltaReindex();
        foreach ($indexes as $index) {
            $indexer = $index->getIndexer();
            if ($indexer) {
                /** @var AW_Advancedsearch_Model_Engine_Sphinx $sphinxIndexer */
                $sphinxIndexer = Mage::getModel('awadvancedsearch/engine_sphinx');
                $result = $sphinxIndexer->reindexDelta($indexer);
                if ($result) {
                    $sphinxIndexer->mergeDeltaWithMain($indexer);
                }
                $indexer->resetUpdates();
            }
        }
    }

    public function productSavePostDispatch($observer)
    {
        $this->_processIndexes();
    }

    public function blogSavePostDispatch($observer)
    {
        if (Mage::helper('awadvancedsearch/config')->getGeneralEnabled()) {
            $controllerAction = $observer->getControllerAction();
            $blogPostId = $controllerAction->getRequest()->getParam('id');
            $blogPost = Mage::getModel('blog/post')->load($blogPostId);
            if ($blogPost->getData()) {
                $storeIds = $blogPost->getData('store_id');
                $indexes = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
                $indexes
                    ->addStatusFilter()
                    ->addTypeFilter(AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_BLOG)
                    ->addStoreFilter($storeIds)
                ;
                foreach ($indexes as $index) {
                    /** @var AW_Advancedsearch_Model_Catalogindexes $index */
                    $indexer = $index->getIndexer();
                    if ($indexer) {
                        $indexer->updateData($blogPost->getId());
                        Mage::helper('awadvancedsearch')->addIndexToDeltaReindex($index);
                    }
                }
                $this->_processIndexes();
            }
        }
    }

    public function kbaseSavePostDispatch($observer)
    {
        if (Mage::helper('awadvancedsearch/config')->getGeneralEnabled()) {
            $controllerAction = $observer->getControllerAction();
            $kbaseArticleId = $controllerAction->getRequest()->getParam('id');
            $_article = Mage::getModel('kbase/article')->load($kbaseArticleId);
            if (!is_null($_article->getId())) {
                $indexes = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
                $indexes
                    ->addStatusFilter()
                    ->addTypeFilter(AW_Advancedsearch_Model_Source_Catalogindexes_Types::AW_KBASE)
                ;
                foreach ($indexes as $index) {
                    /** @var AW_Advancedsearch_Model_Catalogindexes $index */
                    $indexer = $index->getIndexer();
                    if ($indexer) {
                        $indexer->updateData($_article->getId());
                        Mage::helper('awadvancedsearch')->addIndexToDeltaReindex($index);
                    }
                }
                $this->_processIndexes();
            }
        }
    }

    public function afterConfigurationSave($observer)
    {
        $collection = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
        $collection->addStatusFilter(AW_Advancedsearch_Model_Source_Catalogindexes_State::READY);
        foreach ($collection as $item) {
            /** @var AW_Advancedsearch_Model_Catalogindexes $item */
            $item
                ->setData('state', AW_Advancedsearch_Model_Source_Catalogindexes_State::REINDEX_REQUIRED)
                ->save()
            ;
        }
        /** @var AW_Advancedsearch_Helper_Data $helper */
        $helper = Mage::helper('awadvancedsearch');
        Mage::getModel('awadvancedsearch/engine_sphinx')->stopSearchd();
        $helper->rrmdir($helper->getVarDir());
        Mage::getSingleton('core/session')->addNotice(
            $helper->__('All Advanced Search indexes are invalidated now. Reindex it and start searchd daemon.')
        );
    }
}