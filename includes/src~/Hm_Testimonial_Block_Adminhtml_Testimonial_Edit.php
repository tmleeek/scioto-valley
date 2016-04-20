<?php

class Hm_Testimonial_Block_Adminhtml_Testimonial_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'testimonial';
        $this->_controller = 'adminhtml_testimonial';
        
        $this->_updateButton('save', 'label', Mage::helper('testimonial')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('testimonial')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('testimonial_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'testimonial_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'testimonial_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function deleteMedia(str)
            {
            	if(str == 'upload')
            	{
	            	if($('delete_media').checked)
	            	$('delete_media').value = 1;
	            	else
	            	$('delete_media').value = 0;
	            }
	            else
	            {
	            	if($('delete_media').checked)
	            	$('delete_media').value = 2;
	            	else
	            	$('delete_media').value = 0;
    			}
    		}
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('testimonial_data') && Mage::registry('testimonial_data')->getId() ) {
            return Mage::helper('testimonial')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('testimonial_data')->getClientName()));
        } else {
            return Mage::helper('testimonial')->__('Add Item');
        }
    }
}