<?php
/**
 * @category    AM
 * @package     AM_RevSlider
 * @copyright   Copyright (C) 2008-2013 ArexMage.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      ArexMage.com
 * @email       support@arexmage.com
 */
class AM_RevSlider_Block_Adminhtml_Slider_Edit_Tab_Styles extends Mage_Adminhtml_Block_Widget_Form{
    protected $_slider;

    protected function _getSlider(){
        if (!$this->_slider){
            $slider = Mage::getModel('revslider/slider');
            $id = $this->getRequest()->getParam('id', null);
            if (is_numeric($id)){
                $slider->load($id);
            }
            $this->_slider = $slider;
        }
        return $this->_slider;
    }

    public function _prepareForm(){
        /* @var $slider AM_RevSlider_Model_Slider */
        $slider = $this->_getSlider();

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('trouble_fieldset', array(
            'legend'    => Mage::helper('revslider')->__('Static Styles')
        ));
        $fieldset->addField('styles', 'text', array(
            'name'      => 'styles',
            'label'     => Mage::helper('revslider')->__('Slider Static Styles')
        ));
        $form->getElement('styles')->setRenderer(
            $this->getLayout()->createBlock('revslider/adminhtml_widget_form_editor')
        );
        $this->setForm($form);
        if ($slider->getId()){
            $form->setValues($slider->getData());
        }

        return parent::_prepareForm();
    }
}