<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ Jobs extension \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Jobs                 \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 © free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class FME_Jobs_Block_Adminhtml_Applications_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  public function __construct()
    {
      parent::__construct();
	
      $this->setUseAjax(true);
     
    }
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('applications_form', array('legend'=>Mage::helper('jobs')->__('Applicant\'s Detail')));
      
      $fieldset->addField('fullname', 'label', array(
          'label'     => Mage::helper('jobs')->__('Name'),
          'name'      => 'fullname',
          'required'  => true,
      ));
      
      $fieldset->addField('email', 'label', array(
          'label'     => Mage::helper('jobs')->__('Email Address'),
          'name'      => 'email',
          'required'  => true,
      ));
      
      $fieldset->addField('dob', 'label', array(
          'label'     => Mage::helper('jobs')->__('Date of Birth'),
          'name'      => 'dob',
          'tabindex'  => 1,
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
      
      $fieldset->addField('nationality', 'label', array(
          'label'     => Mage::helper('jobs')->__('Nationality'),
          'name'      => 'nationality',
          'required'  => true,
      ));
      
      $fieldset->addField('telephone', 'label', array(
          'label'     => Mage::helper('jobs')->__('Telephone'),
          'name'      => 'telephone',
          'required'  => true,
      ));
      
      $fieldset->addField('address', 'label', array(
          'label'     => Mage::helper('jobs')->__('Address'),
          'name'      => 'address',
          'rows'      => '5',
          'cols'      => '20',
      ));
      
      $fieldset->addField('zipcode', 'label', array(
          'label'     => Mage::helper('jobs')->__('Zip Code'),
          'name'      => 'zipcode',
          'editable'  => false,
          'required'  => true,
      ));
      
      
      $fieldset->addType('cvfile','FME_Jobs_Lib_Cvfile');

        $fieldset->addField('cvfile_name', 'cvfile', array(
            'label'         => 'CV File',
            'name'          => 'cvfile_name',
            'required'      => false,
//            'value'     => $this->getCvFile(),
            'bold'      =>  true,
            'label_style'   =>  'font-weight: bold;color:red;',
        ));

        
      
      
      $fieldset->addField('create_date', 'label', array(
          'label'     => Mage::helper('jobs')->__('Applied on'),
          'name'      => 'create_date',
          'editable'  => false,
          'required'  => true,
      ));
      
      $fieldset->addField('comments', 'textarea', array(
          'label'     => Mage::helper('jobs')->__('Comments'),
          'name'      => 'comments',
          'rows'      => '5',
          'cols'      => '20',
      ));

     
  
      if ( Mage::getSingleton('adminhtml/session')->getJobsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getApplicationsData());
          Mage::getSingleton('adminhtml/session')->setApplicationsData(null);
      } elseif ( Mage::registry('applications_data') ) {
          $form->setValues(Mage::registry('applications_data')->getData());
      }
      return parent::_prepareForm();
  }
  
  protected function getCvFile()
  {
      $data = Mage::registry('applications_data')->getData();
//      echo "<pre>";
//      print_r($data); exit;
      return '<a href="#" >'.$data["cvfile"].'</a>';
  }
}