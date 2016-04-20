<?php
/**
 * NOTICE OF LICENSE
 *
 * You may not sell, sub-license, rent or lease
 * any portion of the Software or Documentation to anyone.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @category   ET
 * @package    ET_HideEmptyAttributes
 * @copyright  Copyright (c) 2012 ET Web Solutions (http://etwebsolutions.com)
 * @contacts   support@etwebsolutions.com
 * @license    http://shop.etwebsolutions.com/etws-license-free-v1/   ETWS Free License (EFL1)
 */

class ET_HideEmptyAttributes_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEmptyAttributeText()
    {
        // TODO: return default value if no value in configuration
        return Mage::getStoreConfig('ethideemptyattributes/general/emptyattrtext');
    }
}