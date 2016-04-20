<?php

 /**
 * Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Jobs
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */

class FME_Jobs_Block_Adminhtml_Jobs_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('jobsGrid');
      $this->setDefaultSort('jobs_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('jobs/jobs')->getCollection();
      
      $resource = Mage::getSingleton('core/resource');
      
      $collection->getSelect()
          ->joinLeft(array('jobsapps' => $resource->getTableName('fme_jobsapplications')), "jobsapps.job_id = main_table.jobs_id", array('applicantcount'=>'count(jobsapps.app_id)'))
          ->group('main_table.jobs_id');
      
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('jobs_id', array(
          'header'    => Mage::helper('jobs')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'jobs_id',
      ));
      
      $this->addColumn('jobtitle', array(
          'header'    => Mage::helper('jobs')->__('Job Title'),
          'align'     =>'left',
          'index'     => 'jobtitle',
      ));

       $this->addColumn('jobtype_name', array(
          'header'    => Mage::helper('jobs')->__('Job Type'),
          'align'     => 'left',
//          'width'     => '200px',
          'index'     => 'jobtype_name',
          'type'      => 'options',
          'options'   => 
	     Mage::getModel('jobs/jobs')->toOptionArray('jobtype','Asc',false,false)
           ,
      ));
       
      $this->addColumn('store_name', array(
          'header'    => Mage::helper('jobs')->__('Location'),
          'align'     => 'left',
          'width'     => '200px',
          'index'     => 'store_name',
          'type'      => 'options',
          'options'   => 
	     Mage::getModel('jobs/jobs')->toOptionArray('store','Asc',false,false)
           ,
      ));
      $this->addColumn('department_name', array(
          'header'    => Mage::helper('jobs')->__('Department'),
          'align'     => 'left',
          'width'     => '200px',
          'index'     => 'department_name',
          'type'      => 'options',
          'options'   => 
	     Mage::getModel('jobs/jobs')->toOptionArray('department','Asc',false,false)
           ,
      ));
      
      $this->addColumn('applicantcount', array(
          'header'    => Mage::helper('jobs')->__('Applicants'),
          'align'     =>'left',
          'index'     => 'applicantcount',
	  'filter'    => false,
	 
      ));
      
      $this->addColumn('create_dates', array(
          'header'    => Mage::helper('jobs')->__('Posted On'),
          'align'     =>'left',
          'width'     => '120px',
          'index'     => 'create_dates',
          'type'      => 'date',
	  
	   
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('jobs')->__('Status'),
          'align'     => 'left',
          'width'     => '50px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
             
              0 => 'Disable',
	      1 => 'Enable'
             
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
                        'caption'   => Mage::helper('jobs')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
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

        $statuses = Mage::getSingleton('jobs/status')->getOptionArray();

        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('jobs')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('jobs')->__('Status'),
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