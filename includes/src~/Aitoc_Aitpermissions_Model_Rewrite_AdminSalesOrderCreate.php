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
/* $meta=%default,AdjustWare_Deliverydate,Aitoc_Aitcheckoutfields% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitcheckoutfields')){
    class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate_Aittmp extends Aitoc_Aitcheckoutfields_Model_Rewrite_AdminSalesOrderCreate {} 
 }elseif(Mage::helper('core')->isModuleEnabled('AdjustWare_Deliverydate')){
    class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate_Aittmp extends AdjustWare_Deliverydate_Model_Rewrite_AdminhtmlSalesOrderCreate {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate_Aittmp extends Mage_Adminhtml_Model_Sales_Order_Create {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate extends Aitoc_Aitpermissions_Model_Rewrite_AdminSalesOrderCreate_Aittmp
{
    public function initFromOrder(Mage_Sales_Model_Order $order)
    {
        try
        {
            parent::initFromOrder($order);
        }
        catch (Exception $e)
        {
            return Mage::app()->getFrontController()->getResponse()->setRedirect(getenv("HTTP_REFERER"));
        }
        
        return $this;
    }
}