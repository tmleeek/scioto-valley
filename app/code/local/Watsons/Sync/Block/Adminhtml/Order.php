<?php
class Watsons_Sync_Block_Adminhtml_Order
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'sync';
        $this->_headerText = 'Watsons Sync';
        $this->_addButtonLabel = 'Export New Orders';
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/create');
    }
}