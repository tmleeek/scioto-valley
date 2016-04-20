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
class Aitoc_Aitpermissions_Model_Rewrite_AdminhtmlConfigData extends Mage_Adminhtml_Model_Config_Data
{

    public function load()
    {
        if ($this->getSection() != Mage::app()->getRequest()->getParam('section')) {
            $this->setSection(Mage::app()->getRequest()->getParam('section'));
            $this->_configData = null;
        }
        return parent::load();
    }
}