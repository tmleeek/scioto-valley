<?php
/**
 * Advanced Permissions
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitpermissions
 * @version      2.10.1
 * @license:     Z2INqHJ2yDwAS29S2ymsavGhKUg3g8KJsjTqD848qH
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitpermissions_Block_Adminhtml_Website_Select extends Mage_Core_Block_Template
{
    protected $_websiteIds = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/website_select.phtml');
    }
    
    public function getWebsites()
    {
        $websites = Mage::app()->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) 
        {
            foreach ($websites as $websiteId => $website) 
            {
                if (!in_array($websiteId, $websiteIds)) 
                {
                    unset($websites[$websiteId]);
                }
            }
        }
        return $websites;
    }
    
    public function setCurrentWebsiteIds($websiteIds)
    {
        $this->_websiteIds = $websiteIds;
        return $this;
    }
    
    public function getCurrentWebsiteIds()
    {
        return $this->_websiteIds;
    }
}