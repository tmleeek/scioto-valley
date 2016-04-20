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
class Magebassi_Mbimageslider_Block_Adminhtml_Mbimageslider_Edit_Tab_General_Typeoptions extends Mage_Adminhtml_Block_Template 
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
		
		$setting_fieldset = 'Banner settings';
		if($_stype == 'imageslider')
			$setting_fieldset = 'Image Banner Settings';		

        $_fieldset = $_form->addFieldset('types_data_fset', array(
            'legend' => $this->__($setting_fieldset)
        ));		

		if($_stype=='imageslider')
		{
			$_fieldset->addField('title', 'text', array(
				'label'     => Mage::helper('mbimageslider')->__('Image Title'),
				'required'  => false,
				'name'      => 'type_data[title]',
			));	

			$_fieldset->addField('filename', 'image', array(
				'label'     => Mage::helper('mbimageslider')->__('Image'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'filename',
			));	
					
			$_fieldset->addField('weblink', 'text', array(
				  'label'     => Mage::helper('mbimageslider')->__('Image Link'),
				  'class'     => 'validate-url',
				  'required'  => false,
				  'after_element_html' => "<small>Image Link URL</small>",
				  'name'      => 'type_data[weblink]',
			));
			  
			$_fieldset->addField('linktarget', 'select', array(
				  'label'     => Mage::helper('mbimageslider')->__('Link Target'),
				  'name'      => 'type_data[linktarget]',
				  'after_element_html' => "<small>New Tab: To open in new tab, Same Tab: To open in same tab</small>",
				  'values'    => array(
					  array(
						  'value'     => '_self',
						  'label'     => Mage::helper('mbimageslider')->__('Same Tab'),
					  ),
				  
					  array(
						  'value'     => '_blank',
						  'label'     => Mage::helper('mbimageslider')->__('New Tab'),
					  )
				  ),
			));
					
			$_fieldset->addField('content', 'editor', array(
				  'name'      => 'type_data[content]',
				  'label'     => Mage::helper('mbimageslider')->__('Content'),
				  'title'     => Mage::helper('mbimageslider')->__('Content'),
				  'style'     => 'width:280px; height:100px;',
				  'wysiwyg'   => false,
				  'required'  => false,
			));
		}		  
		
        $_data = Mage::getSingleton('adminhtml/session')->getData(Magebassi_Mbimageslider_Helper_Data::FORM_DATA_KEY);			
        $_data = isset($_data) ? $_data : null;
        if($_data) $_form->setValues($_data);        

        return $_form->getHtml();
    }
}