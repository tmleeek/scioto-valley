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

class MageWorx_StoreSwitcher_Model_PageCache_Processor extends MageWorx_StoreSwitcher_Model_PageCache_Processor_Abstract
{
    /**
     * Check if cache should be loaded
     *
     * @return bool
     */
    public function isAllowed()
    {
        if (isset($_GET['geoip_country'])) {
            return false;
        }

        return parent::isAllowed();
    }

    /**
     * Populate request ids
     *
     * @return Enterprise_PageCache_Model_Processor
     */
    protected function _createRequestIds()
    {
        if (!$this->isAllowed()) {
            return parent::_createRequestIds();
        }

        $switcherConfig = Mage::app()->getCache()->load('mageworx_storeswitcher_config');
        $switcherConfig = unserialize($switcherConfig);

        if (empty($switcherConfig['config']['is_enabled'])) {
            return parent::_createRequestIds();
        }

        if (isset($_COOKIE['geoip_store_code'])) {
            if ($switcherConfig['config']['force_store_view']) {
                $_COOKIE['store'] = $_COOKIE['geoip_store_code'];
            }
            return parent::_createRequestIds();
        }

        $customerIp = MageWorx_GeoIP_Helper_Data::getCustomerIp();
        $location = MageWorx_GeoIP_Model_Geoip::getGeoIpLocation($customerIp, $switcherConfig['config']);

        $currentWebsite = false;
        if ($switcherConfig['config']['is_website_scope']) {
            $currentWebsite = $this->getCurrentWebsite($switcherConfig['websites']);
        }

        $customerStoreCode = false;
        foreach ($switcherConfig['relations'] as $storeCode => $countries) {
            if ($currentWebsite && !in_array($storeCode, $switcherConfig['websites'][$currentWebsite])) {
                continue;
            }

            if (in_array($location['code'], $countries)) {
                $customerStoreCode = $storeCode;
                break;
            }
        }

        if ($customerStoreCode) {
            $_COOKIE['store'] = $customerStoreCode;
            setcookie('store', $customerStoreCode, time() + (86400 * 365));
            setcookie('geoip_store_code', $customerStoreCode, time() + (86400 * 365));
        }

        return parent::_createRequestIds();
    }

    public function getCurrentWebsite($websites)
    {
        $params =  Mage::registry('application_params');
        if ($params['scope_type'] == 'website') {
            $currentWebsite = $params['scope_code'];
        } elseif ($params['scope_type'] == 'store') {
            if (empty($params['scope_code'])) {
                $currentWebsite = key($websites);
            } else {
                foreach ($websites as $code => $ws) {
                    if (in_array($params['scope_code'], $ws)) {
                        $currentWebsite = $code;
                        break;
                    }
                }
            }
        }

        return $currentWebsite;
    }

    /**
     * @param string $content
     * @return bool|false|string
     */
    public function extractContent($content)
    {
        if(!version_compare(Mage::getVersion(), '1.10.0', '>=')){
            return false;
        }
        return parent::extractContent($content);
    }
}