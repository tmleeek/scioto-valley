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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit_Tab_Location extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		// Field set for Group Location on CMS pages
		$cmsfieldset = $form->addFieldset('location_type', array(
			'legend'=>Mage::helper('mbimageslider')->__('Group Location Setting')
		)); 
		
		$cmsfieldset->addField('locationtype', 'select', array(
		  'label'     => Mage::helper('mbimageslider')->__('Group Location'),
		  'name'      => 'locationtype',          
		  'values'    => array(				  
				  array(
					  'value'     => '',
					  'label'     => Mage::helper('mbimageslider')->__('Please Select...'),
				  ),
				  array(
					  'value'     => 'cmspage',
					  'label'     => Mage::helper('mbimageslider')->__('CMS Pages'),
				  ),				 
				  
				  array(
					  'value'     => 'leftcol',
					  'label'     => Mage::helper('mbimageslider')->__('Left Column'),
				  ),
				  
				  array(
					  'value'     => 'rightcol',
					  'label'     => Mage::helper('mbimageslider')->__('Right Column'),
				  ),
			),
		));
		
		$fieldset = $form->addFieldset('location_options', array(
			'legend' => $this->__('Location Options')
		));
		
		$fieldset->setFieldsetContainerId('awf_types_settings');

		$fieldset->addField('types_data', 'note', array(
			'text' => $this->__('No representation has been selected')
		));
	  
		if ( Mage::getSingleton('adminhtml/session')->getMbgroupsData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getMbgroupsData();
			Mage::getSingleton('adminhtml/session')->setMbgroupsData(null);
		} elseif ( Mage::registry('mbimageslider_data') ) {
			$data = Mage::registry('mbimageslider_data')->getData();
		}
		
		$data['store_id'] = explode(',',$data['stores']);
		$form->setValues($data);
	  
		return parent::_prepareForm();	  
	  
	}
}