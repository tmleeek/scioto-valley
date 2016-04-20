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
 * @package    AW_Onsale
 * @version    2.5.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Onsale_Model_System_Config_Backend_Percent extends Mage_Core_Model_Config_Data
{
    /*
     * Corrects wrong values
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value === null) {
            $value = 0;
        }
        if ($value && !is_numeric($value)) {
            $value = 0;
        }
        if ($value && is_numeric($value) && ($value < 0)) {
            $value = 0;
        }
        if ($value && is_numeric($value) && ($value > 100)) {
            $value = 100;
        }
        $this->setValue($value);
        return $this;
    }
}



