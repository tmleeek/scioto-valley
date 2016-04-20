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
/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitcg,Aitoc_Aitdownloadablefiles,Aitoc_Aitoptionstemplate% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitoptionstemplate')){
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitoptionstemplate_Model_Rewrite_FrontCatalogProduct {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitdownloadablefiles')){
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitdownloadablefiles_Model_Rewrite_FrontCatalogProduct {} 
 }elseif(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcg')){
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct_Aittmp extends Aitoc_Aitcg_Model_Rewrite_Catalog_Product {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct_Aittmp extends Mage_Catalog_Model_Product {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct extends Aitoc_Aitpermissions_Model_Rewrite_CatalogProduct_Aittmp
{
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled()
            && Mage::getStoreConfig('admin/su/enable')
            && !$this->getCreatedAt())
        {
            $this->setStatus(Aitoc_Aitpermissions_Model_Rewrite_CatalogProductStatus::STATUS_AWAITING);
            Mage::getModel('aitpermissions/notification')->send($this);
        }
        
        if ($this->getId() && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getId(), $this->getStatus());
        }

        $request = Mage::app()->getRequest();

        if (($request->getPost('simple_product') &&
            $request->getQuery('isAjax') &&
            $role->isScopeStore()) ||
            Mage::helper('aitpermissions')->isQuickCreate())
        {
            $this->_setParentCategoryIds($request->getParam('product'));
        }
        
        return $this;
    }
    
    private function _setParentCategoryIds($parentId)
    {
        $configurableProduct = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->load($parentId);

        if ($configurableProduct->isConfigurable())
        {
            if (!$this->getData('category_ids'))
            {
                $categoryIds = (array)$configurableProduct->getCategoryIds();
                if ($categoryIds)
                {
                    $this->setCategoryIds($categoryIds);
                }
            }
        }
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        
        if ($this->getData('entity_id') && Mage::getStoreConfig('admin/su/enable') && $this->getStatus())
        {
            Mage::getModel('aitpermissions/approve')->approve($this->getData('entity_id'), $this->getStatus());
        }
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            if (($role->canEditOwnProductsOnly() && !$role->isOwnProduct($this)) ||
                !$role->isAllowedToEditProduct($this))
            {
                Mage::throwException(
                    Mage::helper('aitpermissions')->__(
                        'Sorry, you have no permissions to delete this product. For more details please contact site administrator.'
                    )
                );
            }
        }

        return $this;
    }
}