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
 * @package    AW_Searchautocomplete
 * @version    3.4.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Searchautocomplete_Helper_Data extends Mage_Core_Helper_Abstract
{

    private function isAdvancedSearchInstalled()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        return array_key_exists('AW_Advancedsearch', $modules)
            && 'true' == (string)$modules['AW_Advancedsearch']->active;
    }

    public function canUseADVSearch()
    {
        if(!$this->isAdvancedSearchInstalled()) return false;
        return (bool) Mage::helper('searchautocomplete/config')->getInterfaceUseAdvancedSearch() && Mage::helper('awadvancedsearch')->isEnabled();
    }

    public function isFulltext($attributeId)
    {
        $attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
        if (($attribute->getData('is_searchable') == 1) && ($attribute->getData('frontend_input') == 'textarea')) {
            return true;
        }
        return false;
    }

    public function getUsedAttributes()
    {
        $usedAttributes = array();
        $itemPattern = Mage::helper('searchautocomplete/config')->getInterfaceItemTemplate();
        $pattern = '/{([^}]*)}/si';
        preg_match_all($pattern, $itemPattern, $match);

        $attributeModel = Mage::getSingleton('searchautocomplete/source_product_attribute');
        $attributesArray = $attributeModel->toArray();
        foreach($match[1] as $attributeCode) {
            if (array_key_exists($attributeCode, $attributesArray)) {
                $usedAttributes[] = $attributeCode;
            }
        }
        return $usedAttributes;
    }

    public function getSearchedQuery()
    {
        $searchQuery =  Mage::app()->getRequest()->getParam('q');
        if (is_null($searchQuery)) {
            $searchQuery = '';
        }
        return htmlspecialchars_decode(Mage::helper('core')->escapeHtml($searchQuery));
    }

    public function getSearchedWords()
    {
        $searchedQuery = $this->getSearchedQuery();
        $searchedWords = explode(' ', trim($searchedQuery));
        for ($i = 0; $i < count($searchedWords); $i++) {
            if (strlen($searchedWords[$i]) < 2 || preg_match('(:)', $searchedWords[$i])) {
                unset($searchedWords[$i]);
            }
        }
        return $searchedWords;
    }

    public function getEntityTypeId()
    {
        return Mage::getSingleton('searchautocomplete/source_product_attribute')->getEntityTypeId();
    }
}
