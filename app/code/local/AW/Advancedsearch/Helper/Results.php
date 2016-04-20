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


class AW_Advancedsearch_Helper_Results extends Mage_Core_Helper_Abstract
{
    protected $_sphinxConnection = null;
    protected $_blogAPIModel = null;

    public function getSphinxConnection()
    {
        if ($this->_sphinxConnection === null) {
            $sphinxClient = Mage::getModel('awadvancedsearch/engine_sphinx')->connect();
            if ($sphinxClient instanceof SphinxClient) {
                $sphinxClient->SetMatchMode(Mage::helper('awadvancedsearch/config')->getSphinxMatchMode());
                $sphinxClient->SetSortMode(SPH_SORT_RELEVANCE);
                $sphinxClient->SetLimits(0, 1000, 1000);
                $this->_sphinxConnection = $sphinxClient;
            }
        }
        return $this->_sphinxConnection;
    }

    public function getIndexes($store = null, $type = null)
    {
        $indexes = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
        $indexes->addStatusFilter()
            ->addStateFilter()
            ->addStoreFilter($store)
            ->setTypeOrder();
        if ($type) {
            $indexes->addTypeFilter($type);
        }
        return $indexes;
    }

    public function query($queryText, $store = null, $indexType = null)
    {
        $catalogSearchHelper = Mage::helper('catalogsearch');
        $sphinxConnection = $this->getSphinxConnection();

        $queryText = $this->applyWildcardOptions($queryText);
        if (defined('SPH_MATCH_EXTENDED')
            && defined('SPH_MATCH_EXTENDED2')
            && Mage::helper('awadvancedsearch/config')->getSphinxMatchMode() != SPH_MATCH_EXTENDED
            && Mage::helper('awadvancedsearch/config')->getSphinxMatchMode() != SPH_MATCH_EXTENDED2
        ) {
            $queryText = $sphinxConnection->EscapeString($queryText);
        }

        if (strlen($queryText) >= $catalogSearchHelper->getMinQueryLength() && $sphinxConnection) {
            $indexes = $this->getIndexes($store, $indexType);
            if ($indexes->getSize()) {
                foreach ($indexes as $index) {
                    $attributes = $index->getData('attributes');
                    $attributeWeights = array();
                    foreach ($attributes as $attribute) {
                        $attributeWeights[$attribute['attribute']] = (int)$attribute['weight'];
                    }
                    $sphinxConnection->ResetFilters();
                    $advancedFilter = $index->getIndexer()->getAdvancedFilter($store);
                    if (null !== $advancedFilter) {
                        $sphinxConnection->SetFilter($advancedFilter['key'], $advancedFilter['value']);
                    }
                    $sphinxConnection->SetFieldWeights($attributeWeights);
                    $sphinxConnection->AddQuery($queryText, $index->getIndexName());
                }
                $results = $sphinxConnection->RunQueries();
                if ($results) {
                    $i = 0;
                    foreach ($indexes as $index) {
                        $index->setData('sphinx_results', $results[$i++]);
                    }
                    return $indexes;
                }
            }
        }
        return false;
    }

    public function getMatchesCount($index)
    {
        if ($index->getData('sphinx_results') && is_array($index->getData('sphinx_results'))) {
            $sphinxResult = $index->getData('sphinx_results');
            return isset($sphinxResult['total']) ? $sphinxResult['total'] : null;
        }
        return null;
    }

    public function getMatchedIds($index)
    {
        if ($index->getData('sphinx_results') && is_array($index->getData('sphinx_results'))) {
            $sphinxResult = $index->getData('sphinx_results');
            if (isset($sphinxResult['matches'])) {
                $result = array();
                if (null !== $index->getIndexer()->getResultKey()) {
                    foreach ($sphinxResult['matches'] as $item) {
                        array_push($result, $item['attrs'][$index->getIndexer()->getResultKey()]);
                    }
                } else {
                    $result = array_keys($sphinxResult['matches']);
                }
                return $result;
            }
        }
        return array();
    }

    public function getBlogAPI()
    {
        if ($this->_blogAPIModel === null) {
            $this->_blogAPIModel = Mage::helper('awadvancedsearch')->getBlogAPIModel();
        }
        return $this->_blogAPIModel;
    }

    /**
     * Wrap query
     * @param string $queryText
     *
     * @return string
     */
    public function applyWildcardOptions($queryText)
    {
        if (!Mage::helper('awadvancedsearch/config')->isWildcardSearchEnabled()) {
            return $queryText;
        }

        $queryParts = explode(' ', $queryText);
        switch (Mage::helper('awadvancedsearch/config')->getWildcardSearchType()) {
            case AW_Advancedsearch_Model_Source_Sphinx_Wildcard::START_CODE:
                foreach ($queryParts as $index => $part) {
                    $queryParts[$index] = '*' . $part;
                }
                break;
            case AW_Advancedsearch_Model_Source_Sphinx_Wildcard::MIDDLE_CODE:
                foreach ($queryParts as $index => $part) {
                    $queryParts[$index] = '*' . $part . '*';
                }
                break;
            case AW_Advancedsearch_Model_Source_Sphinx_Wildcard::END_CODE:
                foreach ($queryParts as $index => $part) {
                    $queryParts[$index] = $part . '*';
                }
                break;
        }
        return implode(' ', $queryParts);
    }

    public function orderCollectionByRelevance($collection, $idFieldName, $sortedIds,
                                               $dir = Varien_Data_Collection::SORT_ORDER_ASC)
    {
        $idsFinsetString = implode(',', $sortedIds);
        $collection->getSelect()->order("FIND_IN_SET({$idFieldName}, '{$idsFinsetString}') {$dir}");
        return $collection;
    }
}
