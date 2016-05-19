<?php
// MageWorx Custom
class MageWorx_Adminhtml_Block_Downloads_Customer_Edit_Tab_Files_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('files_grid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        if( $this->getCustomerId() instanceof Mage_Customer_Model_Customer ) {
            $this->setCustomerId( $this->getCustomerId()->getId() );
        }

        $collection = Mage::getResourceModel('downloads/customer_collection')->addCustomerFilter($this->getCustomerId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('downloads')->__('Product Id'),
            'width'     => '90px',
            'index'     => 'product_id',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('downloads')->__('Product Name'),
            'index'     => 'product_name',
        ));

        $this->addColumn('file_id', array(
            'header'    => Mage::helper('downloads')->__('File Id'),
            'width'     => '90px',
            'index'     => 'file_id',
            'filter'    => false,
        ));

        $this->addColumn('file_name', array(
            'header'    => Mage::helper('downloads')->__('File Name'),
            'index'     => 'file_name',
            'filter'    => false,
        ));

        $this->addColumn('download_date', array(
            'header'    => Mage::helper('downloads')->__('Download Date'),
            'index'     => 'download_date',
            'filter'    => false,
        ));

        return parent::_prepareColumns();
    }

//    public function getGridUrl()
//    {
//        return $this->getUrl('mageworx/downloads_customer/files', array(
//            '_current' => true,
//            'id'       => $this->getCustomerId()
//        ));
//    }
}
