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
class Aitoc_Aitpermissions_Model_Mysql4_Editor_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('aitpermissions/editor_type');
    }

    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter('role_id', $roleId);
        $this->load();
        return $this;
    }

    public function duplicateProductTypePermissions($oldRoleId, $newRoleId)
    {
        $oldTypes = $this->loadByRoleId($oldRoleId);
        
        foreach($oldTypes as $type)
        {
            $type->setData('id', null);
            $type->setData('role_id', $newRoleId);
            $type->setData('type',$type->getType());
            $type->save();
        }        
    }
}