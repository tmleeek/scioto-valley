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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductHelperFormGallery
    extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery
{
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled() && !$role->isAllowedToDelete())
        {
            $html = preg_replace(
                '@cell-remove a-center last"><input([ ]+)type="checkbox"@',
                'cell-remove a-center last"><input disabled="disabled" type="checkbox"',
                $html
            );
        }

        return $html;
    }

    public function getAttributeReadonly($attribute)
    {
        if (is_object($attribute)) {
            $attribute = $attribute->getAttributeCode();
            $attrId = $attribute->getAttributeId();
        }
        else
        {
            $attribute_details = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $attribute);
            $attribute_data = $attribute_details->getData();
            $attrId = $attribute_data['attribute_id'];
        }

        $result = parent::getAttributeReadonly($attribute);

        $attributePermissionArray = Mage::helper('aitpermissions')->getAttributePermission();

        if(isset($attributePermissionArray[$attrId]))
        {
            if($attributePermissionArray[$attrId] == 0)
            {
                return true;
            }

            return false;
        }
        return $result;
    }
}