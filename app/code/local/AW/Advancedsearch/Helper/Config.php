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

class AW_Advancedsearch_Helper_Config extends Mage_Core_Helper_Abstract
{
    /* General Section from System Configuration */
    const GENERAL_ENABLE = 'awadvancedsearch/general/enable';

    /* Shpinx Section from System Configuration */
    const SPHINX_SERVER_PATH = 'awadvancedsearch/sphinx/server_path';
    const SPHINX_SERVER_ADDR = 'awadvancedsearch/sphinx/server_addr';
    const SPHINX_SERVER_PORT = 'awadvancedsearch/sphinx/server_port';
    const SPHINX_MATCH_MODE  = 'awadvancedsearch/sphinx/match_mode';

    /* Search Options Section from System Configuration */
    const SEARCH_OPTIONS_WILDCARD_ENABLED           = 'awadvancedsearch/search_options/enable_wildcard';
    const SEARCH_OPTIONS_WILDCARD_TYPE              = 'awadvancedsearch/search_options/wildcard_type';
    const SEARCH_OPTIONS_MORPHOLOGY_ENABLED         = 'awadvancedsearch/search_options/enable_morphology';
    const SEARCH_OPTIONS_MORPHOLOGY_DICTIONARY_LIST = 'awadvancedsearch/search_options/morphology_dictionary_list';

    /* General Section config getters */
    public function getGeneralEnabled($store = null)
    {
        return Mage::getStoreConfig(self::GENERAL_ENABLE, $store);
    }

    /* Shpinx Section config getters */
    public static function getSphinxServerPath()
    {
        $path = Mage::getStoreConfig(self::SPHINX_SERVER_PATH);
        if ($path) {
            $path .= DS;
        }
        return $path;
    }

    public static function getSphinxServerAddr()
    {
        return Mage::getStoreConfig(self::SPHINX_SERVER_ADDR);
    }

    public static function getSphinxServerPort()
    {
        return Mage::getStoreConfig(self::SPHINX_SERVER_PORT);
    }

    public static function getSphinxMatchMode()
    {
        return Mage::getStoreConfig(self::SPHINX_MATCH_MODE);
    }

    public function getSphinxConfig()
    {
        return array(
            'addr' => self::getSphinxServerAddr(),
            'port' => self::getSphinxServerPort(),
        );
    }

    /* Search Options Section config getters */
    public function isWildcardSearchEnabled()
    {
        return Mage::getStoreConfigFlag(self::SEARCH_OPTIONS_WILDCARD_ENABLED);
    }

    public function getWildcardSearchType()
    {
        return Mage::getStoreConfig(self::SEARCH_OPTIONS_WILDCARD_TYPE);
    }

    public function isMorphologyEnabled()
    {
        return Mage::getStoreConfigFlag(self::SEARCH_OPTIONS_MORPHOLOGY_ENABLED);
    }

    public function getMorphologyDictionaryList()
    {
        return Mage::getStoreConfig(self::SEARCH_OPTIONS_MORPHOLOGY_DICTIONARY_LIST);
    }
}