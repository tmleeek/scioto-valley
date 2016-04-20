<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ FME_Jobs extension \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Jobs                   \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 © free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class FME_Jobs_Block_Adminhtml_Department_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('departmentGrid');
      $this->setDefaultSort('department_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('jobs/department')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('department_id', array(
          'header'    => Mage::helper('jobs')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'department_id',
      ));

      $this->addColumn('department_name', array(
          'header'    => Mage::helper('jobs')->__('Name'),
          'align'     =>'left',
          'index'     => 'department_name',
      ));
      
      
      
       $this->addColumn('create_date', array(
          'header'    => Mage::helper('jobs')->__('Date Created'),
          'align'     =>'left',
          'index'     => 'create_date',
      ));
       
      $this->addColumn('status', array(
          'header'    => Mage::helper('jobs')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));
        
   
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('jobs')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('jobs')->__('View'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'departments',
                'is_system' => true,
        ));
	 $this->addColumn('delete',
            array(
                'header'    =>  Mage::helper('jobs')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('jobs')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
			'confirm'   => Mage::helper('jobs')->__('Are you sure?'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'departments',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('jobs')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('jobs')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('jobs_id');
        $this->getMassactionBlock()->setFormFieldName('jobs');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('jobs')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('jobs')->__('Are you sure?')
        ));

        //$statuses = Mage::getSingleton('jobs/status')->getOptionArray();
        
        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        //$this->getMassactionBlock()->addItem('email', array(
        //     'label'=> Mage::helper('jobs')->__('Send Email'),
        //     'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true))
        //    
        //));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}