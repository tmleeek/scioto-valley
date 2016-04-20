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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbimageslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('mbimageslider_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('mbimageslider')->__('Banner Information'));
	}

	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
          'label'     => Mage::helper('mbimageslider')->__('Banner Information'),
          'title'     => Mage::helper('mbimageslider')->__('Banner Information'),
          'content'   => $this->getLayout()->createBlock('mbimageslider/adminhtml_mbimageslider_edit_tab_form')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}