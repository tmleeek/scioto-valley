<?php
class VladimirPopov_WebForms_Block_Adminhtml_Results_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->getForm()->setData('enctype', 'multipart/form-data');
        return $this;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $result = Mage::registry('result');
        $webform = Mage::registry('webform');

        $form = new Varien_Data_Form(array
        (
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        ));
        $form->setFieldNameSuffix('result');

        $fieldset = $form->addFieldset('result_info', array('legend' => Mage::helper('webforms')->__('Result # %s', $result->getId())));

        $customer_id = $result->getCustomerId();
        $customer_name = Mage::helper('webforms')->__('Guest');
        if ($customer_id) {
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if ($customer->getId())
                $customer_name = $customer->getName();
        }

        $customer_ip = long2ip($result->getData('customer_ip'));

        $result->addData(array(
            'info_customer_name' => $customer_name,
            'info_customer_ip' => $customer_ip,
            'info_created_time' => Mage::helper('core')->formatDate($result->getCreatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true),
            'info_webform_name' => $webform->getName(),
        ));

        $fieldset->addField('info_webform_name', 'link', array(
            'id' => 'info_webform_name',
            'style' => 'font-weight:bold',
            'href' => $this->getUrl('*/adminhtml_webforms/edit', array('id' => $webform->getId())),
            'label' => Mage::helper('webforms')->__('Web-form'),
        ));

        $fieldset->addField('info_created_time', 'label', array(
            'id' => 'info_created_time',
            'bold' => true,
            'label' => Mage::helper('webforms')->__('Result Date'),
        ));

        $fieldset->addField('info_customer_name', $customer_id ? 'link' : 'label', array(
            'id' => 'info_customer_name',
            'style' => 'font-weight:bold',
            'bold' => true,
            'href' => $this->getCustomerUrl($customer_id),
            'label' => Mage::helper('webforms')->__('Customer'),
        ));

        $fieldset->addField('info_store', 'label', array(
            'id' => 'info_store',
            'bold' => true,
            'label' => Mage::helper('webforms')->__('Store'),
            'after_element_html' => '<strong>' . $this->getStoreName() . '</strong>',
        ));

        $fieldset->addField('info_customer_ip', 'label', array(
            'id' => 'info_customer_ip',
            'bold' => true,
            'label' => Mage::helper('webforms')->__('Sent from IP'),
        ));

        $editor_type = 'textarea';
        $editor_config = '';
        if ((float)substr(Mage::getVersion(), 0, 3) > 1.3 && substr(Mage::getVersion(), 0, 5) != '1.4.0' || Mage::helper('webforms')->getMageEdition() == 'EE') {

            $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array('tab_id' => $this->getTabId())
            );

            $wysiwygConfig["files_browser_window_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index');
            $wysiwygConfig["directives_url"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');
            $wysiwygConfig["directives_url_quoted"] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive');

            $wysiwygConfig["add_widgets"] = false;
            $wysiwygConfig["add_variables"] = false;
            $wysiwygConfig["widget_plugin_src"] = false;
            $wysiwygConfig->setData("plugins", array());

            $editor_type = 'editor';
            $editor_config = $wysiwygConfig;
        }

        $fields_to_fieldsets = $webform->getFieldsToFieldsets(true);

        foreach ($fields_to_fieldsets as $fs_id => $fs_data) {
            $legend = "";
            if (!empty($fs_data['name'])) $legend = $fs_data['name'];

            // check logic visibility
            $fieldset = $form->addFieldset('fs_' . $fs_id, array(
                'legend' => $legend,
                'fieldset_container_id' => 'fieldset_' . $fs_id . '_container'
            ));

            foreach ($fs_data['fields'] as $field) {
                $type = 'text';
                $config = array
                (
                    'name' => 'field[' . $field->getId() . ']',
                    'label' => $field->getName(),
                    'container_id' => 'field_' . $field->getId() . '_container'
                );

                $dateFormatIso = Mage::app()->getLocale()->getDateFormat($field->getDateType());
                $dateTimeFormatIso = Mage::app()->getLocale()->getDateTimeFormat($field->getDateType());

                switch ($field->getType()) {
                    case 'textarea':
                    case 'hidden':
                        $type = 'textarea';
                        break;
                    case 'wysiwyg':
                        $type = $editor_type;
                        $config['config'] = $editor_config;
                        break;
                    case 'date':
                        $type = 'date';
                        $config['format'] = $dateFormatIso;
                        $config['locale'] = Mage::app()->getLocale()->getLocaleCode();
                        $config['image'] = $this->getSkinUrl('images/grid-cal.gif');
                        break;

                    case 'datetime':
                        $type = 'date';
                        $config['time'] = true;
                        $config['format'] = $dateTimeFormatIso;
                        $config['image'] = $this->getSkinUrl('images/grid-cal.gif');
                        break;

                    case 'select/radio':
                        $type = 'select';
                        $config['required'] = false;
                        $config['values'] = $field->getOptionsArray();
                        break;

                    case 'select/checkbox':
                        $type = 'checkboxes';
                        $value = explode("\n", $result->getData('field_' . $field->getId()));
                        $result->setData('field_' . $field->getId(), $value);
                        $config['options'] = $field->getSelectOptions();
                        $config['name'] = 'field[' . $field->getId() . '][]';
                        break;

                    case 'select':
                        $type = 'select';
                        $config['options'] = $field->getSelectOptions();
                        break;

                    case 'subscribe':
                        $type = 'select';
                        $config['options'] = Mage::getModel('adminhtml/system_config_source_yesno')->toArray();
                        break;

                    case 'select/contact':
                        $type = 'select';
                        $config['options'] = $field->getSelectOptions(false);
                        break;

                    case 'stars':
                        $type = 'select';
                        $config['options'] = $field->getStarsOptions();
                        break;

                    case 'file':
                        $type = 'file';
                        $config['field_id'] = $field->getId();
                        $config['result_id'] = $result->getId();
                        $config['url'] = $result->getFilePath($field->getId());
                        $config['name'] = 'file_' . $field->getId();
                        $fieldset->addType('file', Mage::getConfig()->getBlockClassName('webforms/adminhtml_element_file'));
                        break;

                    case 'image':
                        $type = 'image';
                        $config['field_id'] = $field->getId();
                        $config['result_id'] = $result->getId();
                        $config['url'] = $result->getFilePath($field->getId());
                        $config['name'] = 'file_' . $field->getId();
                        $fieldset->addType('image', Mage::getConfig()->getBlockClassName('webforms/adminhtml_element_image'));
                        break;

                    case 'html':
                        $type = 'label';
                        $config['label'] = false;
                        $config['after_element_html'] = $field->getValue();
                        break;

                    case 'country':
                        $type = 'select';
                        $config['values'] = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
                        break;
                }
                // check logic visibility
                $fieldset->addField('field_' . $field->getId(), $type, $config);
            }
        }

        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getFormData());
            Mage::getSingleton('adminhtml/session')->setFormData(false);
        } elseif (Mage::registry('result')->getId()) {
            $form->addValues(Mage::registry('result')->getData());
        }

        $form->addField('result_id', 'hidden', array
        (
            'name' => 'result_id',
            'value' => $result->getId(),
        ));

        $form->addField('webform_id', 'hidden', array
        (
            'name' => 'webform_id',
            'value' => $result->getWebformId(),
        ));

        $form->addField('saveandcontinue', 'hidden', array('name' => 'saveandcontinue',));

        $form->setUseContainer(true);

        $this->setForm($form);
    }

    public function getCustomerUrl($customer_id)
    {
        return $this->getUrl('adminhtml/customer/edit', array('id' => $customer_id, '_current' => false));
    }

    public function getStoreName()
    {
        $storeId = Mage::registry('result')->getStoreId();
        if (is_null($storeId)) {
            $deleted = Mage::helper('adminhtml')->__('[deleted]');
            return $deleted;
        }
        $store = Mage::app()->getStore($storeId);
        $name = array(
            $store->getWebsite()->getName(),
            $store->getGroup()->getName(),
            $store->getName()
        );
        return implode('<br/>', $name);
    }
}

?>