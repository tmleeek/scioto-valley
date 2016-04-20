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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$data = Mage::getSingleton('adminhtml/session')->getData(Magebassi_Mbimageslider_Helper_Data::FORM_DATA_KEY);
        if(!is_object($data))
            $data = new Varien_Object($data);
		
		$fieldset = $form->addFieldset('mbimageslider_form', array('legend'=>Mage::helper('mbimageslider')->__('Group information')));
     
		$fieldset->addField('groupname', 'text', array(
		  'label'     => Mage::helper('mbimageslider')->__('Group Name'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'groupname',
		));		  
		 
	  
		$fieldset->addField('groupstatus', 'select', array(
		  'label'     => Mage::helper('mbimageslider')->__('Status'),
		  'name'      => 'groupstatus',
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
		
		
		if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store', 'multiselect', array(
                'name'      => 'store[]',
                'label'     => $this->__('Store View'),
                'required'  => TRUE,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(FALSE, TRUE),
            ));
        } else {
            if($data->getStore() && is_array($data->getStore())) {
                $stores = $data->getStore();
                if (isset($stores[0]) && $stores[0] != '') $stores = $stores[0];
                else $stores = 0;
                $data->setStore($stores);
            }
            $fieldset->addField('store', 'hidden', array(
                'name'      => 'store[]'
            ));
        }

		if(!$data->getStore()) $data->setStore(0);
		
		// Field set for Banner Group Settings
		$fieldset = $form->addFieldset('banner_group_settings',array(
			'legend' => $this->__('Group Settings')
		));		
	  
		$fieldset->addField('effect','select',array(
			'label'					=> Mage::helper('mbimageslider')->__('Effect'),
			'name'      			=> 'effect',
			'values' 				=> Mage::getModel('mbimageslider/options_style')->toOptionArray()
		));
		
		$fieldset->addField('slidingtime','text',array(
			'label'					=> Mage::helper('mbimageslider')->__('Sliding Time'),
			'name'      			=> 'slidingtime',
			'value'  				=> '2',
			'after_element_html' 	=> "<small>Seconds between the end of the sliding effect and the start of the next one</small>"
		));
		
		$fieldset->addField('slidingeffecttime','text',array(
			'label'					=> Mage::helper('mbimageslider')->__('Sliding Effect Time'),
			'name'      			=> 'slidingeffecttime',
			'value'  				=> '1',
			'after_element_html' 	=> '<br>Length of the sliding effect in seconds'
		));
		
		$fieldset->addField('imagewidth','text',array(
			'label'					=> Mage::helper('mbimageslider')->__('Image width'),
			'name'      			=> 'imagewidth',			
			'value'  				=> '684',			
			'after_element_html' 	=> '<br>In pixels. <br><span style="color:red;">Important : </span> Leave blank if your store use responsive theme. 
										It will adjust width and height automatically.'
		));
		
		$fieldset->addField('imageheight','text',array(
			'label'					=> Mage::helper('mbimageslider')->__('Image height'),
			'name'      			=> 'imageheight',
			'value' 		 		=> '342',			
			'after_element_html' 	=> '<br>In pixels. <br><span style="color:red;">Important : </span> Leave blank if your store use responsive theme. 
										It will adjust width and height automatically.'
		));
		
		$fieldset->addField('description','select',array(
			'label'					=> Mage::helper('mbimageslider')->__('Description'),
			'name'      			=> 'description',
			'after_element_html' 	=> '<br>Show description in front of image',
			'values'    => array(			
				  array(
					  'value'     => 0,
					  'label'     => Mage::helper('mbimageslider')->__('Enabled'),
				  ),
				  array(
					  'value'     => 1,
					  'label'     => Mage::helper('mbimageslider')->__('Disabled'),
				  ),
			)
		));
		
		$fieldset->addField('thumbnails','select',array(
			'label'		=> Mage::helper('mbimageslider')->__('Image Thumbnails'),
			'name'      => 'thumbnails',
			'values'	=> Mage::getModel('mbimageslider/options_imagethumbnails')->toOptionArray()
		));
		
		$fieldset->addField('loader','select',array(
			'label'		=> Mage::helper('mbimageslider')->__('Loader'),
			'name'      => 'loader',
			'values'	=> Mage::getModel('mbimageslider/options_loader')->toOptionArray()
		));
		
		$fieldset->addField('navigation','select',array(
			'label'					=> Mage::helper('mbimageslider')->__('Navigation'),
			'name'      			=> 'navigation',
			'after_element_html' 	=> '<br>Navigation button (prev, next and play/stop buttons)',
			'values'    => array(
				  array(
					  'value'     => 1,
					  'label'     => Mage::helper('mbimageslider')->__('Enabled'),
				  ),

				  array(
					  'value'     => 2,
					  'label'     => Mage::helper('mbimageslider')->__('Disabled'),
				  ),
			)
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