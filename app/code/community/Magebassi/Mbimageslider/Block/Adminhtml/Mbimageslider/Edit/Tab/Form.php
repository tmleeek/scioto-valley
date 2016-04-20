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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbimageslider_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('mbimageslider_form', array('legend'=>Mage::helper('mbimageslider')->__('Banner information')));
     
		$fieldset->addField('bannername', 'text', array(
			'label'     => Mage::helper('mbimageslider')->__('Banner Name'),
			'class'     => 'required-entry',
			'required'  => true,
			'name'      => 'banner_info[bannername]',
		));
		
		/*
		$fieldset->addField('group_id', 'select', array(
			'label'     => Mage::helper('mbimageslider')->__('Assign to Group'),
			'required'  => false,
			'name'      => 'group_assign[group_id]',
			'values'	=> Mage::getModel('mbimageslider/options_groups')->toOptionArray()
		));
		*/
		$fieldset->addField('status', 'select', array(
			'label'     => Mage::helper('mbimageslider')->__('Status'),
			'name'      => 'banner_info[status]',
			'values'    => array(
				array(
                  'value'     => 1,
                  'label'     => Mage::helper('mbimageslider')->__('Enabled'),
				),
				array(
                  'value'     => 2,
                  'label'     => Mage::helper('mbimageslider')->__('Disabled'),
				),
			),
		));	  
		
	  
		$fieldset = $form->addFieldset('types_data_fset', array(
			'legend' => $this->__('Banner settings')
		));
		
		$fieldset->setFieldsetContainerId('awf_types_settings');

		$fieldset->addField('types_data', 'note', array(
			'text' => $this->__('No representation has been selected')
		));
	  
			
     
		if ( Mage::getSingleton('adminhtml/session')->getImageSliderData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getImageSliderData();
			Mage::getSingleton('adminhtml/session')->setImageSliderData(null);
		} elseif ( Mage::registry('mbimageslider_data') ) {
			$data = Mage::registry('mbimageslider_data')->getData();
		}
		
		$data['store_id'] = explode(',',$data['stores']);
		$form->setValues($data);
	  
		return parent::_prepareForm();
	}
}