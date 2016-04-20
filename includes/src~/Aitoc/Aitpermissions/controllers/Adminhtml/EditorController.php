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
class Aitoc_Aitpermissions_Adminhtml_EditorController extends Mage_Adminhtml_Controller_Action
{
    public function attributegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('aitpermissions/adminhtml_permissions_tab_product_editor_attribute')->toHtml()
        );
    }
}