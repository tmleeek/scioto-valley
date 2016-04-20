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

class MageWorx_StoreSwitcher_Model_Observer
{
    /**
     * Automatically switches store according to customer's location
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_StoreSwitcher_Model_Observer|Zend_Controller_Response_Abstract
     */
    public function geoipAutoswitcher(Varien_Event_Observer $observer)
    {
        $switcher = Mage::getSingleton('storeswitcher/switcher');
        if (!$switcher->isAllowed()) {        	
            return;
        }

        if (Mage::helper('core')->isModuleEnabled('Enterprise_PageCache') && !Mage::app()->getCache()->load('mageworx_storeswitcher_config')) {
            $this->refreshStoreSwitcherCache(false);
        }

        $helper = Mage::helper('storeswitcher');
        $currentStoreCode = Mage::app()->getStore()->getCode();
        $customerStoreCode = Mage::getSingleton('storeswitcher/switcher')->getCustomerStoreCode();

        $requestStore = Mage::app()->getRequest()->getParam('___store', false);

        if ($requestStore) {
            
            return $this->doRedirect($requestStore, true);
            

        } elseif ($helper->getForceStoreView() && $customerStoreCode && $currentStoreCode != $customerStoreCode) {

            return $this->doRedirect($customerStoreCode, !Mage::helper('mwgeoip')->getCookie('geoip_store_code'));

        } elseif ($customerStoreCode) {

            $storeCookie = Mage::helper('mwgeoip')->getCookie('geoip_store_code');
            
            if (!$storeCookie  || Mage::app()->getRequest()->getParam('geoip_country', false)) {
                return $this->doRedirect($customerStoreCode, true);
            } elseif ($storeCookie && !$requestStore) {
                return $this->doRedirect($storeCookie, false);
            }

        }

        return $this;
    }

    /**
     * Switches correct locale
     *
     * @param $observer
     * @return MageWorx_StoreSwitcher_Model_Observer
     */
    public function switchLocale($observer)
    {
        $switcher = Mage::getSingleton('storeswitcher/switcher');
        if (!$switcher->isAllowed()) {
            return;
        }

        if (!Mage::helper('mwgeoip')->getCookie('geoip_store_code')) {
            $customerStoreCode = Mage::getSingleton('storeswitcher/switcher')->getCustomerStoreCode();
            $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $customerStoreCode);
            $observer->getLocale()->setLocaleCode($locale);
        }

        return $this;
    }

    /**
     * Redirect to customer store
     *
     * @param $customerStoreCode
     * @return bool|Zend_Controller_Response_Abstract
     */
    public function doRedirect($customerStoreCode, $changeCookie = false)
    {
        Mage::app()->getCookie()->set('store', $customerStoreCode);
        if ($changeCookie) {
            Mage::helper('mwgeoip')->setCookie('geoip_store_code', $customerStoreCode);
        }

        Mage::app()->setCurrentStore($customerStoreCode);
        
        // Search fix - 3/14/2016 - Sean
        if (!$_GET['q']) {
        ////

            if ($redirectUrl = Mage::getSingleton('storeswitcher/switcher')->getRedirectUrl($customerStoreCode)) {
                return Mage::app()->getResponse()->setRedirect($redirectUrl);
            }

        //
        }
        ////

        return true;
    }

    /**
     * Prepares 'geoip_country_code' field for save
     *
     * @param Varien_Event_Observer $observer
     * @return bool|MageWorx_StoreSwitcher_Model_Observer
     */
    public function saveStoreCountries(Varien_Event_Observer $observer)
    {
        if (!($storeModel = $observer->getEvent()->getDataObject())) {
            return false;
        }

        if (($storeModel instanceof Mage_Core_Model_Store)) {
            if (is_array($storeModel->getGeoipCountryCode())) {
                $codes = $storeModel->getGeoipCountryCode();
                foreach ($codes as $k => $code){
                    if(!is_array($code) && $k !== $code){
                        $codes[$code] = $code;
                        unset($codes[$k]);
                    }
                }
                $storeModel->setData('geoip_country_code', serialize($codes));
            }
        }

        return $this;
    }

    /**
     * Prepares store data after load
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_StoreSwitcher_Model_Observer
     */
    public function loadStoreCountries(Varien_Event_Observer $observer)
    {
        if (!($storeModel = $observer->getEvent()->getObject())) {
            return $this;
        }

        if (($storeModel instanceof Mage_Core_Model_Store)) {
            if (!is_array($storeModel->getGeoipCountryCode())) {
                $storeModel->setGeoipCountryCode(unserialize($storeModel->getGeoipCountryCode()));
            }
        }

        return $this;
    }

    /**
     * Automatically selects country in address forms
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_StoreSwitcher_Model_Observer
     */
    public function autoSelectCountry(Varien_Event_Observer $observer)
    {
        if (!($block = $observer->getEvent()->getBlock())) {
            return $this;
        }

        $helper = Mage::helper('storeswitcher');

        if ($block instanceof Mage_Core_Block_Html_Select) {
            if (($helper->isEnableBillingCountry() && $block->getId() == 'billing:country_id')
                || ($helper->isEnableShippingCountry() && $block->getId() == 'shipping:country_id')
                || ($helper->isEnableAddressCountry() && $block->getId() == 'country')
            ) {
                $geoip = Mage::getSingleton('mwgeoip/geoip')->getCurrentLocation();
                if ($geoip->getCode()) {
                    $block->setValue($geoip->getCode());
                }
            }
        }

        return $this;
    }

    /**
     * Adds form element on store-edit page
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_StoreSwitcher_Model_Observer
     */
    public function storeEditForm(Varien_Event_Observer $observer)
    {
        if (!($block = $observer->getEvent()->getBlock())) {
            return $this;
        }

        if ($block instanceof Mage_Adminhtml_Block_System_Store_Edit_Form) {

            if (Mage::registry('store_type') == 'store') {
                $storeModel = Mage::registry('store_data');

                $form = $block->getForm();
                $fieldset = $form->addFieldset('store_countries', array(
                    'legend' => Mage::helper('storeswitcher')->__('Store Auto Switcher')
                ));

                $storeCountries = Mage::helper('storeswitcher')->prepareCountryCode($storeModel->getGeoipCountryCode());
                $value = is_array($storeCountries) ? array_keys($storeCountries) : array();
                if(!Mage::helper('mwgeoip')->isCityDbType()){
                    $fieldset->addField('geoip_country_code', 'multiselect', array(
                            'label' => Mage::helper('storeswitcher')->__('Countries'),
                            'name' => 'store[geoip_country_code][]',
                            'required' => false,
                            'value' => $value,
                            'values' => Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(true)
                        ),
                        'store_code'
                    );
                } else {
                    $fieldset->addField('geoip_country_code', 'text', array(
                        'label'     => Mage::helper('storeswitcher')->__('Locations'),
                        'name'      => 'store[geoip_country_code][]',
                        'class'     => 'requried-entry',
                    ));

                    $locationsBlock = $block->getLayout()->createBlock('storeswitcher/adminhtml_store_edit_tab_locations');
                    $form->getElement('geoip_country_code')->setRenderer($locationsBlock);
                }

                $fieldset->addField('geoip_zip_range', 'textarea', array(
                    'label'     => Mage::helper('storeswitcher')->__('ZIP code range'),
                    'name'      => 'store[geoip_zip_range]',
                    'value'     => $storeModel->getGeoipZipRange(),
                ));

                $fieldset->addField('geoip_ip_range', 'textarea', array(
                    'label'     => Mage::helper('storeswitcher')->__('IP address range'),
                    'name'      => 'store[geoip_ip_range]',
                    'value'     => $storeModel->getGeoipIpRange(),
                ));
            }
        }

        return $this;
    }

    /**
     * Prepares tax rate on catalog acording to customer's location
     *
     * @param Varien_Event_Observer $observer
     * @return bool|MageWorx_StoreSwitcher_Model_Observer
     */
    public function prepareDefaultTaxRate(Varien_Event_Observer $observer)
    {
        if (!($request = $observer->getEvent()->getRequest())) {
            return false;
        }

        $changeRate = false;

        if(Mage::getSingleton('checkout/session')->hasQuote()){
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $basedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON);

            $shippingAddress = ($quote->getShippingAddress() && $quote->getShippingAddress()->getCountryId());
            $billingAddress = ($quote->getBillingAddress() && $quote->getBillingAddress()->getCountryId());


            if ((!$shippingAddress && $basedOn == 'shipping') || (!$billingAddress && $basedOn == 'billing')) {
                $changeRate = true;
            }
        }

        if (!$changeRate) {
            return $this;
        }

        $geoip = Mage::getSingleton('mwgeoip/geoip')->getLocation();
        if ($geoip->getCode()) {
            if ($geoip->getRegion()) {
                $region = Mage::getModel('directory/region')->loadByCode($geoip->getRegion(), $geoip->getCode());
                $regionId = $region->getRegionId();
            } else {
                $regionId = '0';
            }
            $request->setCountryId($geoip->getCode())->setRegionId($regionId);
        }

        return $this;
    }

    /**
     * Adds store-switcher configuration and "store=>country" relations to cache
     *
     * @param $observer
     * @return MageWorx_StoreSwitcher_Model_Observer
     */
    public function refreshStoreSwitcherCache($observer)
    {
        if (!Mage::helper('core')->isModuleEnabled('Enterprise_PageCache')) {
            return $this;
        }

        if ($observer && $observer->getType() && $observer->getType() != 'full_page') {
            return $this;
        }

        $switcherConfig = array();

        $allStores = Mage::app()->getStores();
        foreach ($allStores as $store) {
            $switcherConfig['websites'][$store->getWebsite()->getCode()][$store->getCode()] = $store->getCode();
            $switcherConfig['cookie_path'][$store->getCode()] = Mage::getModel('core/cookie')->setStore($store->getCode())->getPath();
            $switcherConfig['relations'][$store->getCode()] = unserialize($store->getGeoipCountryCode());
        }

        $switcherConfig['config'] = array(
            'is_enabled' => Mage::helper('storeswitcher')->isStoreSwitcherEnabled(),
            'is_city_db_type' => Mage::getStoreConfig(MageWorx_GeoIP_Helper_Data::XML_GEOIP_DATABASE_TYPE) == 2,
            'db_path' => Mage::helper('mwgeoip')->getDatabasePath(),
            'force_store_view' => Mage::helper('storeswitcher')->getForceStoreView(),
            'is_website_scope' => Mage::helper('storeswitcher')->isWebsiteScope(),
        );

        Mage::app()->getCache()->save(serialize($switcherConfig), 'mageworx_storeswitcher_config', array(), 86400 * 365);

        return $this;
    }
}
