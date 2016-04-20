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
class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit_Tab_General_Locationtype extends Mage_Adminhtml_Block_Template 
{
    public function _toHtml() 
	{ 
        $_form = new Varien_Data_Form();
        $_form->setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        $_form->setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        $_form->setFieldsetElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
        );
		
		$_stype = $this->getRequest()->getParam('type');
		
		$setting_fieldset = 'Location Options';
		if($_stype == 'cmspage')
			$setting_fieldset = 'Group Location on CMS Page';			

        $_fieldset = $_form->addFieldset('location_options', array(
            'legend' => $this->__($setting_fieldset)
        ));      
	   
		if($_stype=='cmspage'){	
		
			$_fieldset->addField('cmspages', 'multiselect', array(
			  'name'      => 'cmspages',
			  'label'     => Mage::helper('mbimageslider')->__('CMS Pages'),
			  'title'     => Mage::helper('mbimageslider')->__('CMS Pages'),
			  'after_element_html' => '<span style="color:red;">Important : </span>Use \'CTRL\' key for Multi Select</span>',
			  'required'  => true,			  
			  'values' 	  => Mage::getModel('mbimageslider/options_cmspages')->toOptionArray()
			));
			
		}
		
		if($_stype=='leftcol'){
			$_fieldset->addField('types_data', 'note', array(
				'text' => $this->__('No representation Available')
			));
		}
		
		if($_stype=='rightcol'){
			$_fieldset->addField('types_data', 'note', array(
				'text' => $this->__('No representation Available')
			));
		}
		
		if($_stype==''){
			$_fieldset->addField('types_data', 'note', array(
				'text' => $this->__('No representation has been selected')
			));
		}
		
        $_data = Mage::getSingleton('adminhtml/session')->getData(Magebassi_Mbimageslider_Helper_Data::FORM_DATA_KEY);			
        $_data = isset($_data) ? $_data : null;
        if($_data) $_form->setValues($_data);
		
        return $_form->getHtml();
    }
}