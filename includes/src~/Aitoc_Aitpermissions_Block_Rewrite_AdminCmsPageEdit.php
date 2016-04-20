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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCmsPageEdit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $page = Mage::registry('cms_page');
            
            // if page is assigned to store views of allowed website only, will allow to delete it
            if ($page->getStoreId() && is_array($page->getStoreId()))
            {
                foreach ($page->getStoreId() as $storeId)
                {
                    if (!in_array($storeId, $role->getAllowedStoreviewIds()))
                    {
                        $this->_removeButton('delete');
                        break 1;
                    }
                }
            }
        }
        
        return $this;
    }
}