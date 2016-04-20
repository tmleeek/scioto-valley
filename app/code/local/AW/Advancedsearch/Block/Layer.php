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

class AW_Advancedsearch_Block_Layer extends Mage_Catalog_Block_Layer_View
{
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer());
    }

    public function getLayer()
    {
        return Mage::getSingleton('awadvancedsearch/layer');
    }

    protected function _toHtml()
    {
        if (($resultsBlock = $this->getLayout()->getBlock('search.result'))
            && ($resultsBlock->getCurrentType() != AW_Advancedsearch_Model_Source_Catalogindexes_Types::CATALOG)
        ) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();
        $this->_attributeFilterBlockName = 'awadvancedsearch/layer_filter_attribute';
    }
}