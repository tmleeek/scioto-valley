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
class Aitoc_Aitpermissions_Block_Rewrite_AdminPollEdit extends Mage_Adminhtml_Block_Poll_Edit
{
    protected function _preparelayout()
    {
        parent::_prepareLayout();

        $role = Mage::getSingleton('aitpermissions/role');

        if ($role->isPermissionsEnabled())
        {
            $poll = Mage::registry('poll_data');

            // checking if we have permissions to edit this poll
            $allowedStoreviewIds = $role->getAllowedStoreviewIds();

            if ($allowedStoreviewIds && 
                !array_intersect($poll->getStoreIds(), $allowedStoreviewIds)
                && $poll->getId())
            {
                Mage::app()->getResponse()->setRedirect(Mage::getUrl('*/*'));
            }

            if ($poll->getStoreIds() && is_array($poll->getStoreIds()))
            {
                foreach ($poll->getStoreIds() as $pollStoreId)
                {
                    if (!in_array($pollStoreId, $allowedStoreviewIds))
                    {
                        $this->_removeButton('delete');
                        break 1;
                    }
                }
            }
        }

        return $this;
    }
}