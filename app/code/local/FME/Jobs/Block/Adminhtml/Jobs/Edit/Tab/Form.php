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
 
class FME_Jobs_Block_Adminhtml_Jobs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
     $id  = $this->getRequest()->getParam('id');
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('jobs_form', array('legend'=>Mage::helper('jobs')->__('Jobs Information')));
  
     
     $fieldset->addField('jobtitle', 'text', array(
          'label'     => Mage::helper('jobs')->__('Job Title'),
          'name'      => 'jobtitle',
	  'required'  => true,
      ));
     
     $fieldset->addField('positions_jobs', 'select', array(
          'label'     => Mage::helper('jobs')->__('Positions Open'),
          'name'      => 'positions_jobs',
	  'values'    => array(
                               '1' => '1',
                               '2' => '2',
                               '3' => '3',
                               '4' => '4',
                               '5' => '5',
                               '6' => '6',
                               '7' => '7',
                               '8' => '8',
                               '9' => '9',
                               '10' => '10'
                            ),
          'required'  => true,
          'index'     => 'positions_jobs',
      ));
     
     $fieldset->addField('jobs_url', 'text', array(
          'label'     => Mage::helper('jobs')->__('Job Page Url'),
          'name'      => 'jobs_url',
          'note'      => 'Must be unique. If empty, job title will be used',
//	  'required'  => true,
      ));
     

     
     $fieldset->addField('department_name', 'select', array(
	       'label'     => Mage::helper('jobs')->__('Select Department'),
	       'name'      => 'department_name',
	       'required'  => true,
	       'values'    => 
		  Mage::getModel('jobs/jobs')->toOptionArray('department','Asc',true)
		,
	   ));
     
     $fieldset->addField('jobtype_name', 'select', array(
          'label'     => Mage::helper('jobs')->__('Select Job Type'),
          'name'      => 'jobtype_name',
	  'required'  => true,
	  'values'    => 
             Mage::getModel('jobs/jobs')->toOptionArray('jobtype','Asc',true)
	    ,
      ));
     
     $fieldset->addField('store_name', 'select', array(
          'label'     => Mage::helper('jobs')->__('Select Location'),
          'name'      => 'store_name',
	  'required'  => true,
	  'values'    => 
             Mage::getModel('jobs/jobs')->toOptionArray('store','Asc',true)
	    ,
      ));
     
     $fieldset->addField('gender', 'select', array(
          'label'     => Mage::helper('jobs')->__('Gender'),
          'name'      => 'gender',
	  'values'    => array(
             array(
                  'value'     => '0',
                  'label'     => Mage::helper('jobs')->__('Doesn\'t Matter'),
              ),
	       array(
                  'value'     => '1',
                  'label'     => Mage::helper('jobs')->__('Male'),
              ),
               array(
                  'value'     => '2',
                  'label'     => Mage::helper('jobs')->__('Female'),
              ),
	    ),
         'required'  => true,
      ));
     
     $fieldset->addField('career_level', 'text', array(
          'label'     => Mage::helper('jobs')->__('Career Level'),
          'name'      => 'career_level',
      ));
     
     $fieldset->addField('min_qual', 'text', array(
          'label'     => Mage::helper('jobs')->__('Minimum Qualification'),
          'name'      => 'min_qual',
      ));
     
      $fieldset->addField('min_exp', 'text', array(
          'label'     => Mage::helper('jobs')->__('Minimum Experiance'),
          'name'      => 'min_exp',
      ));
      
      $fieldset->addField('travel', 'select', array(
          'label'     => Mage::helper('jobs')->__('Require Travel'),
          'name'      => 'travel',
	  'values'    => array(
             array(
                  'value'     => '0',
                  'label'     => Mage::helper('jobs')->__('No'),
              ),
	       array(
                  'value'     => '1',
                  'label'     => Mage::helper('jobs')->__('Yes'),
              ),
	    ),
      ));
      
      $fieldset->addField('apply_by', 'date', array(
          'label'     => Mage::helper('jobs')->__('Apply By'),
          'name'      => 'apply_by',
          'tabindex'  => 1,
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
      $fieldset->addField('stores','multiselect',array(
            'name'      => 'stores[]',
            'label'     => Mage::helper('jobs')->__('Store View'),
            'title'     => Mage::helper('jobs')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
    ));
      
      /* display the overview of this rfq */
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('jobs')->__('Description'),
          'title'     => Mage::helper('jobs')->__('Brief Description'),
          'style'     => 'width:600px; height:500px;',
          'wysiwyg'   => true,
          'required'  => true,
      ));
      
      $fieldset->addField('skills', 'textarea', array(
          'label'     => Mage::helper('jobs')->__('Required Skills'),
          'name'      => 'skills',
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('jobs')->__('Change Status'),
          'name'      => 'status',
	  'values'    => array(
             array(
                  'value'     => '1',
                  'label'     => Mage::helper('jobs')->__('Enable'),
              ),
	       array(
                  'value'     => '0',
                  'label'     => Mage::helper('jobs')->__('Disable'),
              ),
	    ),
      ));
      
     
      if ( Mage::getSingleton('adminhtml/session')->getJobsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getJobsData());
          Mage::getSingleton('adminhtml/session')->setJobsData(null);
      } elseif ( Mage::registry('jobs_data') ) {
          $form->setValues(Mage::registry('jobs_data')->getData());
      }
      return parent::_prepareForm();
  }
}