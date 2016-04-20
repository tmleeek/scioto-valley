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

class FME_Jobs_Block_Adminhtml_Department_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
      $fieldset = $form->addFieldset('department_form', array('legend'=>Mage::helper('jobs')->__('Detail')));
      
      $fieldset->addField('department_name', 'text', array(
          'label'     => Mage::helper('jobs')->__('Name'),
          'name'      => 'department_name',
          'required'  => true,
      ));
      
      $fieldset->addField('description', 'textarea', array(
          'label'     => Mage::helper('jobs')->__('Description'),
          'name'      => 'description',
          'rows'      => '5',
          'cols'      => '20',
      ));
      
      $fieldset->addField('status', 'select', array(
          'name'      => 'status',
          'label'     => Mage::helper('core')->__('Status'),
          'options'   => array(
              0 => Mage::helper('adminhtml')->__('Disabled'),
              1 => Mage::helper('adminhtml')->__('Enabled')),
          'required'  => true,
      ));
     
  
      if ( Mage::getSingleton('adminhtml/session')->getJobsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDepartmentData());
          Mage::getSingleton('adminhtml/session')->setDepartmentData(null);
      } elseif ( Mage::registry('department_data') ) {
          $form->setValues(Mage::registry('department_data')->getData());
      }
      return parent::_prepareForm();
  }
}