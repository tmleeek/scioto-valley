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

class AW_Advancedsearch_Model_Source_Catalogindexes_Catalog_Product_Attributes
    extends AW_Advancedsearch_Model_Source_Abstract
{
    protected function _toOptionArray()
    {
        $attributes = array();
        $attributesCollection = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()
            ->getAttributesByCode()
        ;
        foreach ($attributesCollection as $attribute) {
            $_frontendLabel = $attribute->getFrontendLabel();
            $_attrCode = $attribute->getAttributeCode();
            if (strpos($_attrCode, 'quote_') === 0) {
                continue;
            }
            if ($_frontendLabel) {
                $attributes[$_attrCode] = $_frontendLabel;
            }
        }
        $_unsetAttributes = array(
            'attribute_set_id',
            'category_ids'
        );
        foreach ($_unsetAttributes as $attribute) {
            if (isset($attributes[$attribute])) {
                unset($attributes[$attribute]);
            }
        }
        asort($attributes);
        return $attributes;
    }
}