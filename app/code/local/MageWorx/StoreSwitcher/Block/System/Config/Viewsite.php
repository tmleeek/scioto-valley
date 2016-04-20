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

class MageWorx_StoreSwitcher_Block_System_Config_Viewsite extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Set and return config element html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->_getAddRowButtonHtml($element->getValue());
    }

    /**
     * Returns "View site" button html
     *
     * @param string $sku
     * @return string
     */
    protected function _getAddRowButtonHtml($sku)
    {
        $countries = Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(true);
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setId('geoip_country_select')
            ->setOptions($countries)
            ->toHtml();

        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__('View site'))
            ->setOnClick("viewSite()")
            ->setStyle('margin-top: 10px; float: right; margin-right: 20px;')
            ->toHtml();

        if (Mage::app()->getRequest()->getParam('store')) {
            $storeId = Mage::app()->getRequest()->getParam('store');
        } elseif (Mage::app()->getRequest()->getParam('website')) {
            $website = Mage::app()->getRequest()->getParam('website');
            $store = Mage::app()->getWebsite($website)->getDefaultStore();
            $storeId = $store->getCode();
        } else {
            $store = Mage::app()->getAnyStoreView();
            $storeId = $store->getCode();
        }

        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl();

        $js = "<script type=\"text/javascript\">
            function viewSite(){
                window.open('" . $baseUrl . "?geoip_country=' + $('geoip_country_select').value, '_newtab');
            }
        </script>";

        $html = $select . $button . $js;

        return $html;
    }

}