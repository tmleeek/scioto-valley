<?php
class Watsons_Sync_Block_Adminhtml_Import
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup  = 'sync';
        $this->_controller  = 'adminhtml';
        $this->_mode        = 'import';
        $this->_headerText  = 'Watsons Sync Import';

        parent::__construct();

        $this->removeButton('back');
        $this->removeButton('reset');
    }
}