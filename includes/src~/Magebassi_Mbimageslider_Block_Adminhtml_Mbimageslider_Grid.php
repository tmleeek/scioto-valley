<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Bannerslider
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Block_Adminhtml_Mbimageslider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('mbimagesliderGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('mbimageslider/mbslider')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
          'header'    => Mage::helper('mbimageslider')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
		));
	  
		$this->addColumn('image',array(
		  'header'    => Mage::helper('mbimageslider')->__('Slider Image'),
		  'align'     =>'center',
          'index'     => 'image',
		  'filter'    => false,
		  'sortable'  => false,
		  'width'	  =>'120',
          'renderer'  => 'mbimageslider/adminhtml_renderer_image'	  
		)); 

		$this->addColumn('bannername', array(
          'header'    => Mage::helper('mbimageslider')->__('Banner Name'),
          'align'     =>'left',
          'index'     => 'bannername',
		));

		$this->addColumn('status', array(
          'header'    => Mage::helper('mbimageslider')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
		));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mbimageslider')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mbimageslider')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
			)
		);
		
		$this->addExportType('*/*/exportCsv', Mage::helper('mbimageslider')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('mbimageslider')->__('XML'));
	  
		return parent::_prepareColumns();
	}

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('mbimageslider');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mbimageslider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mbimageslider')->__('Are you sure to delete selected banner(s)?')
        ));

        $statuses = Mage::getSingleton('mbimageslider/options_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('mbimageslider')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('mbimageslider')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}