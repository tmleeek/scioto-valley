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
/*
 * @refactor
 * move to common controller
 */

class Aitoc_Aitpermissions_Adminhtml_CategoriesController extends Mage_Adminhtml_Controller_Action
{
/*
 * @refactor
 * $storeCategories is not really store categories
 * make getCategoryIds method
 */
	protected function _init()
	{
        $id = $this->getRequest()->getParam('rid');
		$storeCategories = Mage::getResourceModel('aitpermissions/advancedrole_collection')->loadByRoleId($id);
		Mage::register('store_categories', $storeCategories);
	}

/*
 * @refactor
 * using block "adminhtml_store_switcher" is not right
 * use smth like "adminhtml_roleedit_categories"
 */
    public function categoriesJsonAction()
    {
        $this->_init();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_store_switcher')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'), $this->getRequest()->getParam('store')));
    }

/*
 * @refactor
 * seems not used, remove
 */
    public function categoriesAction()
    {
        $this->_init();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_advanced')->toHtml()
        );
    }
}