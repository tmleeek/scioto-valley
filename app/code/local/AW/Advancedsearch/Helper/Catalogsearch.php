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

class AW_Advancedsearch_Helper_Catalogsearch extends Mage_CatalogSearch_Helper_Data
{
    const SPHINX_SEARCH_RESULTS = 'as_sphinx_sr';

    public function getResultUrl($query = null)
    {
        return $this->_getUrl(
            'awadvancedsearch/result',
            array(
                '_query' => array(self::QUERY_VAR_NAME => $query),
                '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
            )
        );
    }

    public function getOriginalResultUrl($query = null)
    {
        return parent::getResultUrl($query);
    }

    public static function setResults($results)
    {
        Mage::register(self::SPHINX_SEARCH_RESULTS, $results);
    }

    public static function getResults($type = null)
    {
        $results = Mage::registry(self::SPHINX_SEARCH_RESULTS);
        if ($results && $type) {
            foreach ($results as $index) {
                if ($index->getData('type') == $type) {
                    return $index;
                }
            }
            return null;
        }
        return $results;
    }

    public function addCatalogSearchQueryResults($results)
    {
        $resultsCount = 0;
        foreach ($results as $index) {
            $resultsCount += $index->getResultsCount();
        }

        /* @var $query Mage_CatalogSearch_Model_Query */
        $query = Mage::helper('catalogsearch')->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setPopularity(intval($query->getPopularity()) + 1);
                if ($resultsCount) {
                    $query->setNumResults($resultsCount);
                }
                $query->save();

                if ($query->getRedirect()){
                    Mage::app()->getResponse()->setRedirect($query->getRedirect());
                    return;
                }
            }
        }
    }

    public function getSynonymFor()
    {
        return Mage::helper('catalogsearch')->getQuery()->getSynonymFor();
    }
}