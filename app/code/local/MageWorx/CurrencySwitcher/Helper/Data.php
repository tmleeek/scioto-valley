<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Currency Auto Switcher extension
 * Exception class
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_CurrencySwitcher_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_AUTO_SWITCH_ENABLED   = 'mageworx_customers/currency_switcher/enable';
    const XML_USER_AGENT_LIST       = 'mageworx_customers/currency_switcher/user_agent_list';
    const XML_EXCEPTION_URLS        = 'mageworx_customers/currency_switcher/exception_urls';

    /**
     * Checks if currency switcher module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_AUTO_SWITCH_ENABLED);
    }

    /**
     * Gets user agents, for which currency auto switch should be disabled
     *
     * @return array
     */
    public function getUserAgentList()
    {
        return array_filter((array)preg_split('/\r?\n/', Mage::getStoreConfig(self::XML_USER_AGENT_LIST)));
    }

    /**
     * Gets urls, for which currency auto switch should be disabled
     *
     * @return array
     */
    public function getExceptionUrls()
    {
        return array_filter((array)preg_split('/\r?\n/', Mage::getStoreConfig(self::XML_EXCEPTION_URLS)));
    }

    /**
     * Gets country-currency relations base
     *
     * @return bool|array
     */
    public function getCountryCurrency()
    {
        $path = Mage::getConfig()->getModuleDir('etc', 'MageWorx_CurrencySwitcher') . DS . 'country-currency.csv';
        if (file_exists($path)) {
            return file($path);
        } else {
            return false;
        }
    }

    /**
     * Gets currency code by country code
     *
     * @param string $countryCode
     * @return string
     */
    public function getCurrency($countryCode)
    {
        $curBase = $this->getCountryCurrency();
        if ($curBase !== false && count($curBase)) {
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            foreach ($curBase as $value) {
                $data = explode(';', $value);
                $curVal = trim($data[1]);
                if (Mage::helper('mwgeoip')->prepareCode($data[0]) == Mage::helper('mwgeoip')->prepareCode($countryCode)) {
                    if (strstr($curVal, ',')) {
                        $curCodes = explode(',', $curVal);
                        if ($curCodes) {
                            foreach ($curCodes as $code) {
                                $code = trim($code);
                                if (in_array($code, $codes)) {
                                    return $code;
                                }
                            }
                        }
                    } else {
                        if (in_array($curVal, $codes)) {
                            return $curVal;
                        }
                    }
                }
            }
        }
    }

    /**
     * Gets country codes by currency code
     *
     * @param string $currencyCode
     * @return string
     */
    public function getCountryByCurrency($currencyCode)
    {
        $curBase = $this->getCountryCurrency();
        $countries = array();
        if ($curBase !== false && count($curBase)) {
            foreach ($curBase as $value) {
                $data = explode(';', $value);
                $curVal = trim($data[1]);
                if (strpos($curVal, $currencyCode) !== false) {
                    $countries[] = trim($data[0]);
                }
            }
        }

        return implode(',', $countries);
    }
}