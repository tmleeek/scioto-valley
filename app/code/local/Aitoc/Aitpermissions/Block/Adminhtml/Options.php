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
class Aitoc_Aitpermissions_Block_Adminhtml_Options extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitpermissions/options.phtml');
    }

    public function canEditGlobalAttributes()
    {
        return Mage::getModel('aitpermissions/advancedrole')->canEditGlobalAttributes($this->_getRoleId());
    }

    public function canEditOwnProductsOnly()
    {
        return Mage::getModel('aitpermissions/advancedrole')->canEditOwnProductsOnly($this->_getRoleId());
    }

    public function manageOrdersOwnProductsOnly()
    {
        return Mage::getModel('aitpermissions/permissions_order')->canManageOrdersOwnProductsOnly($this->_getRoleId());
    }

    private function _getRoleId()
    {
        return Mage::app()->getRequest()->getParam('rid');
    }
}