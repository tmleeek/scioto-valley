<?php
/**
 * @category Interactone
 * @package Interactone_CoreMods
 * @author Alexey Poletaev (alexey.poletaev@cyberhull.com)
 */
class Interactone_CoreMods_Block_Adminhtml_Catalog_Helper_Form_Wysiwyg_Content
    extends Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg_Content
{
    /**
     * 'add_variables' set to true
     * @see Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg_Content::_prepareForm()
     * @author Alexey Poletaev (alexey.poletaev@cyberhull.com)
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $config['document_base_url']     = $this->getData('store_media_url');
        $config['store_id']              = $this->getData('store_id');
        $config['add_variables']         = true;
        $config['add_widgets']           = false;
        $config['add_directives']        = true;
        $config['use_container']         = true;
        $config['container_class']       = 'hor-scroll';

        $form->addField($this->getData('editor_element_id'), 'editor', array(
            'name'      => 'content',
            'style'     => 'width:725px;height:460px',
            'required'  => true,
            'force_load' => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig($config)
        ));
        $this->setForm($form);

        return $this;
    }
}