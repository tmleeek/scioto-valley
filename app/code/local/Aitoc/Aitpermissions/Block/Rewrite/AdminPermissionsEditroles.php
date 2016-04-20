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
class Aitoc_Aitpermissions_Block_Rewrite_AdminPermissionsEditroles extends Mage_Adminhtml_Block_Permissions_Editroles
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $id = $this->getRequest()->getParam('rid');
		$storeCategories = Mage::getResourceModel('aitpermissions/advancedrole_collection')->loadByRoleId($id);
		Mage::register('store_categories', $storeCategories);
        
        $this->addTab('advanced', array(
            'label'     => Mage::helper('aitpermissions')->__('Advanced Permissions'),
            'content' => $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_advanced', 'adminhtml.permissions.tab.advanced')->toHtml()
        ));
        
        $this->addTab('product_editor', array(
            'label'     => Mage::helper('aitpermissions')->__('Product Edit Permission'),
            'content' => $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_editor', 'adminhtml.permissions.tab.product.editor')->toHtml()
        ));

        $this->addTab('product_create', array(
            'label'     => Mage::helper('aitpermissions')->__('Product Create Permission'),
            'content' => $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_create')->toHtml()           
        ));
        
        return $this;
    }
}