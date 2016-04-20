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
class Aitoc_Aitpermissions_Block_Rewrite_DownloadableAdminhtmlCatalogProductEditTabDownloadableLinks
    extends Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links
{
    public function getPurchasedSeparatelySelect()
    {        
        $html = parent::getPurchasedSeparatelySelect();

        $role = Mage::getSingleton('aitpermissions/role');

        if (!Mage::app()->isSingleStoreMode() && 
            $role->isPermissionsEnabled() &&
            !$role->canEditGlobalAttributes())
        {
            $html = str_replace('<select', '<select disabled="disabled"', $html);         
        }

        return $html;
    }
}