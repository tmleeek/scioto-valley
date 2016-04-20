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

class MageWorx_StoreSwitcher_Block_Adminhtml_Store_Edit_Tab_Locations extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    protected $_element = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setTemplate('storeswitcher/locations.phtml');
    }

    /**
     * Renderer
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     *  Set form element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return MageWorx_StoreSwitcher_Block_Adminhtml_Store_Edit_Tab_Locations
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Returns form element
     *
     * @return mixed
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Returns all available countries and regions
     *
     * @return array
     */
    public function getLocations()
    {
        include_once Mage::getBaseDir() . DS . 'lib' . DS . 'GeoIP' . DS . 'geoipregionvars.php';
        if(!isset($GEOIP_REGION_NAME)){
            $GEOIP_REGION_NAME = array();
        }

        $countries = Mage::getModel('directory/country')->getCollection()->loadByStore();
        $sort = array();
        $countryRegions = array();
        foreach ($countries as $country) {
            $sort[$country->getName()] = $country->getId();
            $regions = isset($GEOIP_REGION_NAME[$country->getCountryId()]) ? $GEOIP_REGION_NAME[$country->getCountryId()] : array();
            if (count($regions)) {
                foreach ($regions as $id => $region) {
                    $countryRegions[$country->getId()][$region] = $region;
                    asort($countryRegions[$country->getId()]);
                }
            }
            /*$regions = $country->getRegions();
            if (count($regions)) {
                foreach ($regions as $region) {
                    $countryRegions[$country->getId()][$region->getCode()] = $region->getName();
                    asort($countryRegions[$country->getId()]);
                }
            }*/
        }

        ksort($sort);
        $countries = array();
        foreach ($sort as $name => $id) {
            $countries[] = array(
                'value'   => $id,
                'label'   => $name,
                'regions' => !empty($countryRegions[$id]) ? $countryRegions[$id] : array()
            );
        }
        return $countries;
    }

    /**
     * returns countries and regions, assigned to current store
     *
     * @return mixed
     */
    public function getSavedLocations()
    {
        $store = Mage::registry('store_data');
        $locations = Mage::helper('storeswitcher')->prepareCountryCode($store->getGeoipCountryCode());
        return $locations;
    }
}