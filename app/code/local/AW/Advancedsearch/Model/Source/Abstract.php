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

abstract class AW_Advancedsearch_Model_Source_Abstract
{
    protected $_optionArray = null;

    public function toOptionArray()
    {
        if ($this->_optionArray === null) {
            $this->_optionArray = $this->_toOptionArray();
        }
        return $this->_optionArray;
    }

    /**
     * Returns array(value => ..., label => ...) for option with given value
     * @param string $value
     * @return array
     */
    public function getOption($value)
    {
        foreach ($this->toOptionArray() as $_option) {
            if ($_option['value'] == $value) {
                return $_option;
            }
        }
        return FALSE;
    }

    /**
     * Get label for option with given value
     * @param string $value
     * @return string
     */
    public function getOptionLabel($value)
    {
        $_option = $this->getOption($value);
        if (!$_option) {
            return FALSE;
        }
        return $_option['label'];
    }

    /**
     * Returns associative array $value => $label
     * @return array
     */
    public function toShortOptionArray()
    {
        $_options = array();
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    protected function _getHelper($ext = '')
    {
        return Mage::helper('awadvancedsearch' . ($ext ? '/' . $ext : ''));
    }

    abstract protected function _toOptionArray();
}