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

class AW_Advancedsearch_Model_Source_Catalogindexes_Types extends AW_Advancedsearch_Model_Source_Abstract
{
    const CATALOG = 1;
    const CMS_PAGES = 2;
    const AW_BLOG = 3;
    const AW_KBASE = 4;

    const CATALOG_LABEL = 'Catalog';
    const CMS_PAGES_LABEL = 'CMS Pages';
    const AW_BLOG_LABEL = 'AW Blog';
    const AW_KBASE_LABEL = 'AW KBase';

    protected function _toOptionArray()
    {
        /** @var AW_Advancedsearch_Helper_Data $helper */
        $helper = $this->_getHelper();
        $result = array(
            array('value' => self::CATALOG, 'label' => $helper->__(self::CATALOG_LABEL)),
            array('value' => self::CMS_PAGES, 'label' => $helper->__(self::CMS_PAGES_LABEL))
        );
        if ($helper->canUseAWBlog()) {
            $result[] = array('value' => self::AW_BLOG, 'label' => $helper->__(self::AW_BLOG_LABEL));
        }
        if ($helper->canUseAWKBase()) {
            $result[] = array('value' => self::AW_KBASE, 'label' => $helper->__(self::AW_KBASE_LABEL));
        }
        return $result;
    }
}