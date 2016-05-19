<?php

class MageWorx_Adminhtml_Block_Downloads_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/start'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('importexport')->__('Import Settings')));

        $fieldset->addField('downloads_csv', 'file', array(
            'name'     => 'downloads_csv',
            'label'    => Mage::helper('importexport')->__('Select File to Import'),
            'title'    => Mage::helper('importexport')->__('Select File to Import'),
            'required' => true
        ));

        $fieldset->addField('entity', 'hidden', array(
            'name' => 'entity',
            'value' => 'downlaods',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
