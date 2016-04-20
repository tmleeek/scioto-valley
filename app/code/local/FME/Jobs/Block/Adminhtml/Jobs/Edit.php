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

class FME_Jobs_Block_Adminhtml_Jobs_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
     protected function _prepareLayout()
    {
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
            $block->setCanLoadTinyMce(true);
        }
		return parent::_prepareLayout();
     }
	
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'jobs';
        $this->_controller = 'adminhtml_jobs';
        
        $this->_updateButton('save', 'label', Mage::helper('jobs')->__('Save Record'));
        $this->_updateButton('delete', 'label', Mage::helper('jobs')->__('Delete Record'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('description') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'description');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'description');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('jobs_data') && Mage::registry('jobs_data')->getId() ) {
            return Mage::helper('jobs')->__("Edit '%s'", $this->htmlEscape(Mage::registry('jobs_data')->getJobtitle()));
        } else {
            return Mage::helper('jobs')->__('New Jobs');
        }
    }
}