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

class AW_Advancedsearch_Model_Api
{
    public function rawQuery($queryText, $store = null, $indexType = null)
    {
        return Mage::helper('awadvancedsearch')->isEnabled()
            ? Mage::helper('awadvancedsearch/results')->query($queryText, $store, $indexType)
            : false;
    }

    /**
     * Runs query on catalog
     * example - Mage::getModel('awadvancedsearch/api')->catalogQuery('car')
     * @param string $query
     * @param int $store
     * @param bool|string $sortByRelevance
     * @return  false - when nothing founded
     *          null - when some error occurs
     *          product collection - when something has been found
     */
    public function catalogQuery($query, $store = null, $sortByRelevance = Varien_Data_Collection::SORT_ORDER_ASC)
    {
        $results = $this->rawQuery($query, $store, AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG);
        if ($results) {
            foreach ($results as $index) {
                if ($index->getResultsCount()) {
                    $productCollection = $index->getResults();
                    if ($store) {
                        $productCollection->addStoreFilter($store);
                    }
                    if ($sortByRelevance) {
                        $productCollection->addAttributeToSort('relevance', $sortByRelevance);
                    }
                    $productCollection
                        ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ;

                    return $productCollection;
                }
            }
            return false;
        }
        return null;
    }
}