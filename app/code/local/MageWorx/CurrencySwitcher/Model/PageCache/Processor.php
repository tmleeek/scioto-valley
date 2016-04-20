<?php

class MageWorx_CurrencySwitcher_Model_PageCache_Processor extends MageWorx_CurrencySwitcher_Model_PageCache_Processor_Abstract
{
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

        $switcherConfig = Mage::app()->getCache()->load('mageworx_currencyswitcher_config');
        $switcherConfig = unserialize($switcherConfig);

        if (isset($_COOKIE['currency_code'])) {
            return parent::_createRequestIds();
        }

        $customerIp = MageWorx_GeoIP_Helper_Data::getCustomerIp();
        $location = MageWorx_GeoIP_Model_Geoip::getGeoIpLocation($customerIp, $switcherConfig['config']);

        $customerCurrency = false;
        foreach ($switcherConfig['relations'] as $currencyCode => $countries){
            if (in_array($location['code'], $countries)) {
                $customerCurrency = $currencyCode;
            }
        }

        if ($customerCurrency) {
            $_COOKIE['currency'] = $customerCurrency;
            setcookie('currency', $customerCurrency, time() + (86400 * 365));
            setcookie('currency_code', $customerCurrency, time() + (86400 * 365));
        }

        return parent::_createRequestIds();
    }
}