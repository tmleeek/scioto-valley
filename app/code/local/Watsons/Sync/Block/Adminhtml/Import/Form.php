<?php

class Watsons_Sync_Block_Adminhtml_Import_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array(
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);


        $fieldset = $form->addFieldset('upload_form', array(
            'legend'=>Mage::helper('sync')->__('Upload file for processing...')
        ));

        $fieldset->addField('type', 'hidden', array(
            'required'  => false,
            'name'      => 'type',
        ));

        $fieldset->addField('type_label', 'Label', array(
            'label'     => Mage::helper('sync')->__('Type'),
            'required'  => false,
            'name'      => 'type_label',
        ));

        $fieldset->addField('file', 'file', array(
            'label'     => Mage::helper('sync')->__('Import File'),
            'required'  => false,
            'name'      => 'file',
        ));

        $data = array();
        if ( Mage::registry('sync_import_data') ) {
            $data = Mage::registry('sync_import_data');
        }
        $form->setValues($data);

        return parent::_prepareForm();
    }
}