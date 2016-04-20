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
class Aitoc_Aitpermissions_Block_Rewrite_AdminSalesOrderCreateStoreSelect
    extends Mage_Adminhtml_Block_Sales_Order_Create_Store_Select
{
    public function getStoreCollection($group)
    {
        $stores = parent::getStoreCollection($group);

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
        	$stores->addIdFilter($role->getAllowedStoreviewIds());
        }

        return $stores;
    }
}