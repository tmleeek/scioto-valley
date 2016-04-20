<?php

class Watsons_Sync_Block_Adminhtml_Order_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('ordersGrid');
        $this->setDefaultSort('time', 'desc');
    }

    /**
     * Init syncs collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sync/fs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('sync')->__('Name'),
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('time', array(
            'header'    => Mage::helper('sync')->__('Time'),
            'index'     => 'date_object',
            'type'      => 'datetime',
        ));

        $this->addColumn('size', array(
            'header'    => Mage::helper('sync')->__('Size, byte'),
            'index'     => 'size',
            'type'      => 'number',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('download', array(
            'header'    => Mage::helper('sync')->__('Download'),
            'format'    => '<a href="' . $this->getUrl('*/*/download', array('name' => '$name')) .'">CSV</a>',
            'index'     => 'type',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('sync')->__('Action'),
            'type'      => 'action',
            'width'     => '80px',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(array(
                'url'       => $this->getUrl('*/*/delete', array('name' => '$name')),
                'caption'   => Mage::helper('adminhtml')->__('Delete'),
                'confirm'   => Mage::helper('adminhtml')->__('Are you sure you want to do this?')
            )),
            'index'     => 'type',
            'sortable'  => false
        ));

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('name');
        $this->getMassactionBlock()->setFormFieldName('order');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('sync')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('sync')->__('Are you sure?')
        ));

        return $this;
    }

}
