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

$installer = $this;
$installer->startSetup();

try {
    $collection = Mage::getModel('awadvancedsearch/catalogindexes')->getCollection();
    $collection->addStatusFilter(AW_Advancedsearch_Model_Source_Catalogindexes_State::READY);
    if ($collection->getSize() > 0) {
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
    }
} catch (Exception $ex) {
    Mage::logException($ex);
}

$installer->endSetup();