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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mbmanagegroups_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mbimageslider')->__('Group Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section_group_info', array(
          'label'     => Mage::helper('mbimageslider')->__('Group Information'),
          'title'     => Mage::helper('mbimageslider')->__('Group Information'),
          'content'   => $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_form')->toHtml(),
      ));
	  
	  $this->addTab('form_section_group_banners', array(
          'label'     => Mage::helper('mbimageslider')->__('Group Banners'),
          'title'     => Mage::helper('mbimageslider')->__('Group Banners'),
          'url'       => $this->getUrl('*/*/groupbannersgrid', array('_current' => true)),
          'class'     => 'ajax',
      ));
	  
	  $this->addTab('form_section_group_location',array(
		  'label'     => Mage::helper('mbimageslider')->__('Group Location (CMS, Left & Right)'),
          'title'     => Mage::helper('mbimageslider')->__('Group Location'),
          'content'   => $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_location')->toHtml(),
	  ));
	  
	  $this->addTab('form_section_group_category',array(
		  'label'     => Mage::helper('mbimageslider')->__('Display on categories'),
          'title'     => Mage::helper('mbimageslider')->__('Display on categories'),
          'content'   => $this->getLayout()->createBlock('mbimageslider/adminhtml_mbmanagegroups_edit_tab_categories')->toHtml(),
	  ));
	  
	  $this->addTab('form_section_group_products', array(
          'label'     => Mage::helper('mbimageslider')->__('Display on products'),
          'title'     => Mage::helper('mbimageslider')->__('Display on products'),
          'url'       => $this->getUrl('*/*/productsgrid', array('_current' => true)),
          'class'     => 'ajax',
      ));
	  
      return parent::_beforeToHtml();
  }
}