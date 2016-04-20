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
/* $meta=%default,AdjustWare_Deliverydate,AdjustWare_Orderproducts,Aitoc_Aitcheckoutfields% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields')){
    class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid_Aittmp extends Aitoc_Aitcheckoutfields_Block_Rewrite_AdminhtmlSalesOrderGrid {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Orderproducts')){
    class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid_Aittmp extends AdjustWare_Orderproducts_Block_Rewrite_AdminSalesOrderGrid {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Deliverydate')){
    class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid_Aittmp extends AdjustWare_Deliverydate_Block_Rewrite_AdminhtmlSalesOrderGrid {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid_Aittmp extends Mage_Adminhtml_Block_Sales_Order_Grid {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid extends Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderGrid_Aittmp
{
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

        $role = Mage::getSingleton('aitpermissions/role');

		if ($role->isPermissionsEnabled())
		{
			$allowedStoreviews = $role->getAllowedStoreviewIds();
    		if (count($allowedStoreviews) <= 1 && isset($this->_columns['store_id']))
            {
                unset($this->_columns['store_id']);
            }
		}
        
		return $this;
	}
}