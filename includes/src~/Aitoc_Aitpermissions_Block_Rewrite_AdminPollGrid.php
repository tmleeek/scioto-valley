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
class Aitoc_Aitpermissions_Block_Rewrite_AdminPollGrid extends Mage_Adminhtml_Block_Poll_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('poll/poll')->getCollection();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $collection->addStoreFilter($role->getAllowedStoreviewIds());
        }

        $this->setCollection($collection);
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        if (!Mage::app()->isSingleStoreMode())
        {
            $this->getCollection()->addStoreData();
        }
        
        return $this;
    }
}