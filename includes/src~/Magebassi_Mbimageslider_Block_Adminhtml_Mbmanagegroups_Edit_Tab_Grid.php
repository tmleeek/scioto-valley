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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
 
	public function __construct()
	{
		parent::__construct();
		$this->setId('bgroupGrid');
		$this->setUseAjax(true); // Using ajax grid is important
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');		
		$this->setSaveParametersInSession(false);	
	}
  
  

	protected function _prepareCollection()
	{
	  $collection = Mage::getModel('mbimageslider/mbslider')->getCollection();	  
	  $tm_id = $this->getRequest()->getParam('id');
	  if(!isset($tm_id)) {
		$tm_id = 0;
	  }	  	  
	  $this->setCollection($collection);
	  return parent::_prepareCollection();
	}
  
  
	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_groups') {
			$ids = $this->_getSelectedGroups();
			if (empty($ids)) {
				$ids = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('id', array('in'=>$ids));
			} else {
				if($productIds) {
					$this->getCollection()->addFieldToFilter('id', array('nin'=>$ids));
				}
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('in_groups', array(
			'header_css_class'  => 'a-center',
			'type'              => 'checkbox',
			'name'              => 'in_groups',
			'values'            => $this->_getSelectedGroups(),
			'align'             => 'center',
			'index'             => 'id'
		));
  
		$this->addColumn('entity_id', array(
          'header'    => Mage::helper('mbimageslider')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',		  
		));
	  
		$this->addColumn('image',array(
		  'header'    => Mage::helper('mbimageslider')->__('Slider Image'),
		  'align'     =>'center',
          'index'     =>'image',
		  'filter' 	  => false,
		  'sortable'  => false,
		  'width'	  =>'120',
          'renderer'  => 'mbimageslider/adminhtml_renderer_image'	  
		));

		$this->addColumn('bannername', array(
          'header'    => Mage::helper('mbimageslider')->__('Banner Name'),
          'align'     =>'left',
          'index'     => 'bannername',
		));  	  
	  
		$this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Enter Sort Order'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
			'align'             => 'center',
			'filter' 			=> false,
			'sortable'  		=> false,
            'editable'          => true,
            'edit_only'         => true
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
	  
		return parent::_prepareColumns();
	}
  
	protected function _getSelectedGroups()   
	{
		$customers = array_keys($this->getSelectedBgroups());
		return $customers;		
	}

	
	public function getSelectedBgroups()
	{		
		$group_id = $this->getRequest()->getParam('id');
		if(!isset($group_id)) {
			$group_id = 0;
		}
		$collection = Mage::getModel('mbimageslider/mbseclist')->getCollection()->addFieldToFilter('group_id',$group_id);
		
		$custIds = array();
		foreach($collection as $obj){
			$custIds[$obj->getSelectedList()] = array('position'=>$obj->getPosition());
		}
		return $custIds;
	}
  
    public function getGridUrl()
    {
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/groupbannersgridsec', array('_current'=>true));
    }

}