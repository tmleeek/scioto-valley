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
class Aitoc_Aitpermissions_Block_Rewrite_AdminCatalogProductEditActionAttributeTabWebsites
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Websites
{
    public function getWebsiteCollection()
    {
        $websites = parent::getWebsiteCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
        	foreach ($websites as $key => $website)
            {
            	if (!in_array($website->getId(), $role->getAllowedWebsiteIds()))
            	{
            		unset($websites[$key]);
            	}
            }
        }
        
        return $websites;
    }
}