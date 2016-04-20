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
 * @package    MageWorx_StoreSwitcher
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Store Auto Switcher extension
 * Exception class
 *
 * @category   MageWorx
 * @package    MageWorx_StoreSwitcher
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_StoreSwitcher_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_GEOIP_ENABLE_STORE_SWITCHER   = 'mageworx_customers/storeswitcher/enable_store_switcher';
    const XML_GEOIP_FORCE_STORE_VIEW        = 'mageworx_customers/storeswitcher/force_store_view';
    const XML_GEOIP_SWITCHER_SCOPE          = 'mageworx_customers/storeswitcher/store_switcher_scope';
    const XML_GEOIP_DISABLE_KEY             = 'mageworx_customers/storeswitcher/disable_store_switcher_key';
    const XML_GEOIP_EXCEPTION_URLS          = 'mageworx_customers/storeswitcher/store_switcher_exception_urls';
    const XML_GEOIP_ENABLE_BILLING_COUNTRY  = 'mageworx_customers/storeswitcher/enable_billing_country';
    const XML_GEOIP_ENABLE_SHIPPING_COUNTRY = 'mageworx_customers/storeswitcher/enable_shipping_country';
    const XML_GEOIP_ENABLE_ADDRESS_COUNTRY  = 'mageworx_customers/storeswitcher/enable_address_country';
    const XML_GEOIP_ENABLE_REPLACE_SWITCHER = 'mageworx_customers/storeswitcher/replace_switcher';
    const XML_GEOIP_SWITCHER_DISPLAY_MODE   = 'mageworx_customers/storeswitcher/switcher_mode';
    const XML_GEOIP_SWITCHABLE_COUNTRIES    = 'mageworx_customers/storeswitcher/switchable_countries';
    const XML_GEOIP_IP_LIST                 = 'mageworx_customers/storeswitcher/ip_list';
    const XML_GEOIP_USER_AGENT_LIST         = 'mageworx_customers/storeswitcher/user_agent_list';

    /**
     * @return bool
     */
    public function isStoreSwitcherEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_GEOIP_ENABLE_STORE_SWITCHER);
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function getForceStoreView()
    {
        return Mage::getStoreConfigFlag(self::XML_GEOIP_FORCE_STORE_VIEW);
    }

    /**
     * @return bool
     */
    public function isWebsiteScope()
    {
        return Mage::getStoreConfigFlag(self::XML_GEOIP_SWITCHER_SCOPE);
    }

    /**
     * @return mixed
     */
    public function getDisableKey()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_DISABLE_KEY);
    }

    /**
     * @return mixed
     */
    public function getExceptionUrls()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_EXCEPTION_URLS);
    }

    /**
     * @return mixed
     */
    public function isEnableBillingCountry()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_ENABLE_BILLING_COUNTRY);
    }

    /**
     * @return mixed
     */
    public function isEnableShippingCountry()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_ENABLE_SHIPPING_COUNTRY);
    }

    /**
     * @return mixed
     */
    public function isEnableAddressCountry()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_ENABLE_ADDRESS_COUNTRY);
    }

    /**
     * @return mixed
     */
    public function isEnableReplaceSwitcher()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_ENABLE_REPLACE_SWITCHER);
    }

    /**
     * @return mixed
     */
    public function getSwitcherDisplayMode()
    {
        return Mage::getStoreConfig(self::XML_GEOIP_SWITCHER_DISPLAY_MODE);
    }

    /**
     * returns countries, which can be switched on the frontend
     *
     * @return array
     */
    public function getSwitchableCountries()
    {
        $result = array();
        $allCountries = Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(true);
        $selected = explode(',', Mage::getStoreConfig(self::XML_GEOIP_SWITCHABLE_COUNTRIES));

        foreach ($allCountries as $country) {
            if (in_array($country['value'], $selected)) {
                $result[] = $country;
            }
        }

        return $result;
    }

    /**
     * get IPs, for which switcher should be disabled
     *
     * @return array
     */
    public function getIpList()
    {
        $result = array();
        $ipList = array_filter((array)preg_split('/\r?\n/', Mage::getStoreConfig(self::XML_GEOIP_IP_LIST)));
        foreach ($ipList as $ip) {
            $ipParts = explode('//', $ip);
            $result[] = trim($ipParts[0]);
        }
        return $result;
    }

    /**
     * get user agent's for wich switcher should be disabled
     *
     * @return array
     */
    public function getUserAgentList()
    {
        return array_filter((array)preg_split('/\r?\n/', Mage::getStoreConfig(self::XML_GEOIP_USER_AGENT_LIST)));
    }

    /**
     * Returns url, which will emulate site view from custom country
     *
     * @return mixed|string
     */
    public function getCountryUrl()
    {
        $current = Mage::helper('core/url')->getCurrentUrl();
        $requestUrl = $current;

        $currentCode = Mage::app()->getRequest()->getParam('geoip_country', false);
        if (strpos($requestUrl, 'geoip_country=') && $currentCode) {
            if(strpos($requestUrl, 'geoip_country='.$currentCode) > 0){
                return str_replace('geoip_country=' . html_entity_decode(urldecode($currentCode)), 'geoip_country=%geoip_code%', $requestUrl);
            } else {
                return Mage::app()->getStore()->getBaseUrl() . '?geoip_country=%geoip_code%';
            }
        }

        if (strpos($current, '?')) {
            $requestUrl .= '&';
        } else {
            $requestUrl .= '?';
        }
        $requestUrl .= 'geoip_country=%geoip_code%';

        return str_replace("'", "\"", $requestUrl);
    }

    /**
     * Converts country code to upper case
     *
     * @param string $countryCode
     * @return string
     */
    public function prepareCode($countryCode)
    {
        return strtoupper(trim($countryCode));
    }

    /**
     * Convert country codes from sql database to simple array
     *
     * @param string $countryCode
     * @return mixed
     */
    public function prepareCountryCode($countryCode)
    {
        if(is_array($countryCode)){
            $countryCode = current($countryCode);
        }
        return unserialize($countryCode);
    }
}