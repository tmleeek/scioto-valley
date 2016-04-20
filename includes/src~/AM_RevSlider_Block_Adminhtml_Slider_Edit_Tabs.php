<?php
/**
 * @category    AM
 * @package     AM_RevSlider
 * @copyright   Copyright (C) 2008-2013 ArexMage.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      ArexMage.com
 * @email       support@arexmage.com
 */
class AM_RevSlider_Block_Adminhtml_Slider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
    public function __construct(){
        parent::__construct();
        $this->setDestElementId('edit_form');
        $this->setId('slider');
        if ($tab = $this->getRequest()->getParam('activeTab')){
            $this->_activeTab = $tab;
        }else{
            $this->_activeTab = 'info_section';
        }
        $this->setTitle(Mage::helper('revslider')->__('Revolution Slider'));
    }

    public function _prepareLayout(){
        $this->addTabAfter('styles_section', array(
            'label' => '<i class="eg-icon-palette"></i>'.Mage::helper('revslider')->__('Static Styles'),
            'title' => Mage::helper('revslider')->__('Static Styles'),
            'url'   => $this->getUrl('*/*/styles', array('_current' => true)),
            'class' => 'ajax'
        ), 'trouble_section');

        $this->addTabAfter('slide_section', array(
            'label' => '<i class="eg-icon-picture"></i>'.Mage::helper('revslider')->__('Slides'),
            'title' => Mage::helper('revslider')->__('Slides'),
            'url'   => $this->getUrl('*/*/slide', array('_current' => true)),
            'class' => 'ajax'
        ), 'trouble_section');

        return parent::_prepareLayout();
    }
}