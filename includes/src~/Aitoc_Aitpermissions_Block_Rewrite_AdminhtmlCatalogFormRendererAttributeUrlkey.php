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
class Aitoc_Aitpermissions_Block_Rewrite_AdminhtmlCatalogFormRendererAttributeUrlkey
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey
{
    public function getElementHtml()
    {
        $html = parent::getElementHtml();
        $element = $this->getElement();
        
        if ($element && $element->getEntityAttribute() &&
            $element->getEntityAttribute()->isScopeGlobal())
        {
            $role = Mage::getSingleton('aitpermissions/role');

            if ($role->isPermissionsEnabled() && !$role->canEditGlobalAttributes() && $this->getRequest()->getActionName() != 'new')
            {
                $html = str_replace('type="text"', ' disabled="disabled" type="text"', $html);
            }
        }

        return $html;
    }
}