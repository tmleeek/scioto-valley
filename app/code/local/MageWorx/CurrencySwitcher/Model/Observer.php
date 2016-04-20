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

class MageWorx_CurrencySwitcher_Model_Observer
{
    /**
     * Automatically switches currency
     *
     * @param   Varien_Event_Observer $observer
     * @return  MageWorx_CurrencySwitcher_Model_Observer
     */
    public function currencyAutoswitch(Varien_Event_Observer $observer)
    {
        $switcher = Mage::getSingleton('currencyswitcher/switcher');
        if (!$switcher->isAllowed()) {
            return $this;
        }
        
        $geoipHelper = Mage::helper('mwgeoip');
        $currencyCookie = $geoipHelper->getCookie('currency_code');
        $mageStore = Mage::app()->getStore();

        $geoipCountry = Mage::app()->getRequest()->getParam('geoip_country');
        if ($mageStore->getCurrentCurrencyCode() != $currencyCookie || $geoipCountry) {
            $currency = null;

            if ($geoipCountry && $geoipHelper->checkCountryCode($geoipCountry)) {
                $currency   = $switcher->getCurrency($geoipCountry);
            } elseif ($currencyCookie) {
                $currency = $currencyCookie;
            } else {
                $geoip      = Mage::getModel('mwgeoip/geoip')->getCurrentLocation();
                $currency   = $switcher->getCurrency($geoip->getCode());
            }
            if ($currency && ($mageStore->getCurrentCurrencyCode() != $currency)) {
                $mageStore->setCurrentCurrencyCode($currency);
                Mage::getModel('core/cookie')->set('currency', 'EUR');

                if (Mage::getSingleton('checkout/session')->hasQuote()) {
                    Mage::getSingleton('checkout/session')->getQuote()
                        ->collectTotals()
                        ->save();
                }
            } else {
                $geoipHelper->setCookie('currency_code', $mageStore->getCurrentCurrencyCode());
            }
        }

        return $this;
    }

    /**
     * Changes module's cookie "currency_code" when currency is changed manually
     *
     * @param   Varien_Event_Observer $observer
     * @return  MageWorx_CurrencySwitcher_Model_Observer
     */
    public function setCurrency(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('currencyswitcher')->isEnabled() || Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $filter = new Zend_Filter_StripTags();
        $currency = $filter->filter(Mage::app()->getFrontController()->getRequest()->getParam('currency'));
        Mage::helper('mwgeoip')->setCookie('currency_code', $currency);

        return $this;
    }

    /**
     * Adds currency-switcher configuration and "country => currency" relations to cache
     *
     * @param $observer
     * @return MageWorx_CurrencySwitcher_Model_Observer
     */
    public function refreshCurrencySwitcherCache($observer)
    {
        if (!Mage::helper('core')->isModuleEnabled('Enterprise_PageCache')) {
            return $this;
        }

        if ($observer->getType() && $observer->getType() != 'full_page') {
            return $this;
        }

        $cacheData = array();
        $relations = Mage::getModel('currencyswitcher/relations')->getCollection();
        foreach ($relations as $item) {
            $cacheData['relations'][$item->getCurrencyCode()] = explode(',', $item->getCountries());
        }

        $cacheData['config'] = array(
            'is_city_db_type' => Mage::getStoreConfig(MageWorx_GeoIP_Helper_Data::XML_GEOIP_DATABASE_TYPE) == 2,
            'db_path' => Mage::helper('mwgeoip')->getDatabasePath(),
        );

        Mage::app()->getCache()->save(serialize($cacheData), 'mageworx_currencyswitcher_config', array(), 86400 * 365);

        return $this;
    }
}
