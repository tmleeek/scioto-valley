<?php

class Watsons_Sync_Block_Adminhtml_Products_Copy
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup  = 'sync';
        $this->_controller  = 'adminhtml_products';
        $this->_mode        = 'copy';
        $this->_headerText  = 'Watsons Copy Website-to-Website (Products)';

        parent::__construct();

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');

       $this->_addButton('run', array(
            'label'     => Mage::helper('adminhtml')->__('Run'),
            'onclick'   => 'editForm.submit();',
            'class'     => 'submit',
        ), 1);

    }
}