<?php
class Watsons_Sync_Block_Adminhtml_Products_Copy_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/copy', array(
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);


        $fieldset = $form->addFieldset('website', array(
            'legend'=>Mage::helper('sync')->__('Website Selection')
        ));

        $collection = Mage::getModel('core/website')->getCollection();

        $websites = array_merge(array(0 => 'Select One.'), $collection->toOptionArray());

        $fieldset->addField('from_website_id', 'select', array(
            'label'     => Mage::helper('sync')->__('From Website'),
            'required'  => true,
            'name'      => 'from_website_id',
            'values'    => $websites
        ));

        $fieldset->addField('to_website_id', 'select', array(
            'label'     => Mage::helper('sync')->__('To Website'),
            'required'  => true,
            'name'      => 'to_website_id',
            'values'    => $websites
        ));


        $data = array();
        if ( Mage::registry('sync_import_data') ) {
            $data = Mage::registry('sync_import_data');
        }
        $form->setValues($data);

        return parent::_prepareForm();
    }
}