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

class Magebassi_Mbimageslider_Block_Adminhtml_Mbmanagegroups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mbimageslider';
        $this->_controller = 'adminhtml_mbmanagegroups';
        
        $this->_updateButton('save', 'label', Mage::helper('mbimageslider')->__('Save Group'));
        $this->_updateButton('delete', 'label', Mage::helper('mbimageslider')->__('Delete Group'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		if( Mage::registry('mbimageslider_data') && Mage::registry('mbimageslider_data')->getId() ) {
            $type = Mage::registry('mbimageslider_data')->getLocationtype();
        }
		
		$this->_formScripts[] = "
			var ajaxUrl = '".Mage::helper('adminhtml')->getUrl('mbimageslider/adminhtml_mbmanagegroups/locationtype')."';
			var type = '".$type."';
			if(type!=''){
				document.observe('dom:loaded', function () {
					Slidertype.init(ajaxUrl,type);
				});	
			}
				
			Event.observe('locationtype','change', function(event) {
				Slidertype.stype(ajaxUrl,this.value);	  	
			});	
					
			";
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('mbimageslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'mbimageslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'mbimageslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function updateAjaxUrl(ajaxurl){
				alert(ajaxurl);
			}
			
			function initialize(ajaxUrl) { 
				
				Event.observe(document, 'dom:loaded', this.initAction.bind(this));
			}
			
			function updateAjaxUrl(ajaxUrl) {
				this.ajaxUrl = this._url(ajaxUrl);
			}			
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mbimageslider_data') && Mage::registry('mbimageslider_data')->getId() ) {
            return Mage::helper('mbimageslider')->__("Edit group '%s'", $this->htmlEscape(Mage::registry('mbimageslider_data')->getGroupname()));
        } else {
            return Mage::helper('mbimageslider')->__('Add Group');
        }
    }
}