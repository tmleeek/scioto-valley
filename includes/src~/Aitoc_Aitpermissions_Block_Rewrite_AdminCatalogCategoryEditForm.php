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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogCategoryEditForm
    extends Mage_Adminhtml_Block_Catalog_Category_Edit_Form
{
    public function _prepareLayout()
    {
        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
        {
            $category = $this->getCategory()->setIsDeleteable(false);
            Mage::unregister('category');
            Mage::register('category', $category);
        }
        
        return parent::_prepareLayout();
    }
}