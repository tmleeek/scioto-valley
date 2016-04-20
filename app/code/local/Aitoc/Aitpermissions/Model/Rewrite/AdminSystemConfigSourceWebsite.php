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
class Aitoc_Aitpermissions_Model_Rewrite_AdminSystemConfigSourceWebsite extends Mage_Adminhtml_Model_System_Config_Source_Website
{
    public function toOptionArray()
    {
        $this->_options = parent::toOptionArray();
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            foreach ($this->_options as $id => $website)
            {
                if (!in_array($website['value'], $role->getAllowedWebsiteIds()))
                {
                    unset($this->_options[$id]);
                }
            }
        }
        return $this->_options;

    }
}