<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Advanced Bannerslider
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('mbmanagegroupsGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('mbimageslider/mbgroups')->getCollection();	  
	  foreach($collection as $link)
	  {
			$group_id = $link->getId();		
			$storescollection = Mage::getModel('mbimageslider/mbgroupstores')->getCollection()->addFieldToFilter('group_id',$group_id);	  
			$storesArray = array();
			foreach($storescollection as $key){						
				$storesArray[] = $key['store_id'];					
			}			
			$link->setStoreId($storesArray);			
      }
	  
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
  protected function _getStore() {
	  $storeId = (int) $this->getRequest()->getParam('store', 0);
	  return Mage::app()->getStore($storeId);
  }

  protected function _prepareColumns()
  {
		$this->addColumn('id', array(
			'header'    => Mage::helper('mbimageslider')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'id',
		));

		$this->addColumn('groupname', array(
			'header'    => Mage::helper('mbimageslider')->__('Group Name'),
			'align'     =>'left',
			'index'     => 'groupname',
		));	
		  
		$this->addColumn('locationtype', array(
			'header'    => Mage::helper('mbimageslider')->__('Group Location'),
			'align'     =>'left',
			'width'     => '400px',
			'index'     => 'locationtype',
			'renderer'  => 'mbimageslider/adminhtml_renderer_grouplocation'
		));

		$this->addColumn('groupstatus', array(
			'header'    => Mage::helper('mbimageslider')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'groupstatus',
			  'type'      => 'options',
			  'options'   => array(
				  1 => 'Enabled',
				  2 => 'Disabled',
			  ),
		));
		  
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
			'header'        =>  Mage::helper('mbimageslider')->__('Store View'),
			'index'         =>  'store_id',
			'type'          =>  'store',
			'store_all'     =>  true,
			'store_view'    =>  true,
			'width'     => '150px',
			'sortable'      =>  false,
			'filter_condition_callback' =>  array($this, '_filterStoreCondition'),
			));
		}
	  
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
		));
	
		$this->addExportType('*/*/exportCsv', Mage::helper('mbimageslider')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('mbimageslider')->__('XML'));  
		return parent::_prepareColumns();
  }
  
  protected function _afterLoadCollection()
  {       
		
        //$this->getCollection()->walk('afterLoad');
        //parent::_afterLoadCollection();        
  }
  
  protected function _filterStoreCondition($collection, $column)
  {        
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }    
        $this->getCollection()->addStoreFilter($value);        
  }

  protected function _prepareMassaction()
  {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('mbimageslider');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('mbimageslider')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('mbimageslider')->__('Are you sure to delete selected group(s)?')
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