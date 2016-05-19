<?php

class MageWorx_Adminhtml_Block_Downloads_Customer_Edit_Tab_Files extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'mageworx';
        $this->_controller = 'downloads_customer_edit_tab_files';

        parent::__construct();

        $this->_removeButton('add');
    }
}