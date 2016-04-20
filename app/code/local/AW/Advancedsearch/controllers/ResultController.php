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

class AW_Advancedsearch_ResultController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function indexAction()
    {
        /** @var AW_Advancedsearch_Helper_Catalogsearch $helper */
        $helper = Mage::helper('awadvancedsearch/catalogsearch');
        /** @var AW_Advancedsearch_Helper_Results $resultsHelper */
        $resultsHelper = Mage::helper('awadvancedsearch/results');
        /** @var Mage_Catalogsearch_Helper_Data $catalogSearchHelper */
        $catalogSearchHelper = Mage::helper('catalogsearch');

        $queryText = $helper->getSynonymFor();
        if (!$queryText) {
            $queryText = $catalogSearchHelper->getQueryText();
        }
        $results = $resultsHelper->query($queryText);
        if ($results) {
            $helper->setResults($results);
        } else {
            if (method_exists($catalogSearchHelper, 'getOriginalResultUrl')) {
                return $this->_redirectUrl(
                    $catalogSearchHelper->getOriginalResultUrl($catalogSearchHelper->getQueryText())
                );
            } else {
                return $this->_redirectUrl($catalogSearchHelper->getResultUrl($catalogSearchHelper->getQueryText()));
            }
        }
        $this->loadLayout();
        $this->renderLayout();

        if ($results) {
            $helper->addCatalogSearchQueryResults($results);
        }
        return $this;
    }
}