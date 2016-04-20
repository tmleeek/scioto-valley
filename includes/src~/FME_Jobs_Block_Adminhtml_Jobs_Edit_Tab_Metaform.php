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
 
class FME_Jobs_Block_Adminhtml_Jobs_Edit_Tab_Metaform extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
     $id  = $this->getRequest()->getParam('id');
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('jobs_form', array('legend'=>Mage::helper('jobs')->__('Meta Information')));
  
     
     $fieldset->addField('meta_title', 'text', array(
          'label'     => Mage::helper('jobs')->__('Meta Title'),
          'name'      => 'meta_title',
      ));
     
      
      

      
      $fieldset->addField('meta_keywords', 'textarea', array(
          'label'     => Mage::helper('jobs')->__('Meta Keywords'),
          'name'      => 'meta_keywords',
      ));
      
      $fieldset->addField('meta_desc', 'textarea', array(
          'label'     => Mage::helper('jobs')->__('Meta Description'),
          'name'      => 'meta_desc',
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