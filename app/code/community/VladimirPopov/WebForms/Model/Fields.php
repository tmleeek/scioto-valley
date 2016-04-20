<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Fields extends VladimirPopov_WebForms_Model_Abstract
{

    protected $img_regex            = '/{{img ([\w\/\.-]+)}}/';
    protected $val_regex            = '/{{val (.*?)}}/';
    protected $php_regex            = '/<\?php(.*?)\?>/';
    protected $tooltip_regex        = "/{{tooltip}}(.*?){{\\/tooltip}}/si";
    protected $tooltip_option_regex = "/{{tooltip\s*val=\"(.*?)\"}}(.*?){{\\/tooltip}}/si";
    protected $tooltip_clean_regex  = "/{{tooltip(.*?)}}(.*?){{\\/tooltip}}/si";

    public function _construct()
    {
        parent::_construct();
        $this->_init('webforms/fields');
    }

    public function getFieldTypes()
    {
        $types = new Varien_Object(array(
            "text"            => Mage::helper('webforms')->__('Text'),
            "email"           => Mage::helper('webforms')->__('Text / E-mail'),
            "number"          => Mage::helper('webforms')->__('Text / Number'),
            "url"             => Mage::helper('webforms')->__('Text / URL'),
            "textarea"        => Mage::helper('webforms')->__('Textarea'),
            "wysiwyg"         => Mage::helper('webforms')->__('HTML Editor'),
            "select"          => Mage::helper('webforms')->__('Select'),
            "select/radio"    => Mage::helper('webforms')->__('Select / Radio'),
            "select/checkbox" => Mage::helper('webforms')->__('Select / Checkbox'),
            "select/contact"  => Mage::helper('webforms')->__('Select / Contact'),
            "country"         => Mage::helper('webforms')->__('Select / Country'),
            "subscribe"       => Mage::helper('webforms')->__('Newsletter Subscription / Checkbox'),
            "date"            => Mage::helper('webforms')->__('Date'),
            "datetime"        => Mage::helper('webforms')->__('Date / Time'),
            "stars"           => Mage::helper('webforms')->__('Star Rating'),
            "file"            => Mage::helper('webforms')->__('File Upload'),
            "image"           => Mage::helper('webforms')->__('Image Upload'),
            "html"            => Mage::helper('webforms')->__('HTML Block'),
            "hidden"          => Mage::helper('webforms')->__('Hidden'),
        ));

        // add more field types
        Mage::dispatchEvent('webforms_fields_types', array('types' => $types));

        return $types->getData();

    }

    public function getComment()
    {
        $comment = $this->getData('comment');
        return trim(preg_replace($this->tooltip_clean_regex, "", $comment));
    }

    public function getTooltip($option = false)
    {
        $matches = array();
        $pattern = $this->tooltip_regex;
        $comment = $this->getData('comment');
        
        if($option)
        {
            $pattern = $this->tooltip_option_regex;
            preg_match_all($pattern, $comment, $matches);
            if(!empty($matches[1]))
            foreach($matches[1] as $i=>$match){
                if(trim($match) == trim($option))
                    return $matches[2][$i]; 
            }
            return false;
        }

        preg_match($pattern, $comment, $matches);

        if(!empty($matches[1]))
            return trim($matches[1]);
        
        return false;
    }

    public function getName()
    {
        if (Mage::getStoreConfig('webforms/general/use_translation')) {
            return Mage::helper('webforms')->__($this->getData('name'));
        }

        return $this->getData('name');
    }

    public function getSelectOptions($clean = true)
    {
        $field_value = $this->getValue();
        $options = explode("\n", $field_value);
        $options = array_map('trim', $options);
        $select_options = array();
        foreach ($options as $o) {
            if ($this->getType() == 'select/contact') {
                if ($clean) {
                    $contact = $this->getContactArray($o);
                    $o = $contact['name'];
                }
            }
            $value = $this->getCheckedOptionValue($o);
            $label = $value;
            $matches = array();
            preg_match($this->val_regex, $value, $matches);
            if (!empty($matches[1])) {
                $value = trim($matches[1]);
                $label = preg_replace($this->val_regex, "", $label);
            }
            $select_options[$value] = trim($label);
        }
        return $select_options;
    }

    public function getResultsOptions()
    {
        $query = $this->getResource()->getReadConnection()
            ->select('value')
            ->from($this->getResource()->getTable('webforms/results_values'), array('value'))
            ->where('field_id = ' . $this->getId())
            ->order('value asc')
            ->distinct();
        $results = $this->getResource()->getReadConnection()->fetchAll($query);
        $options = array();
        foreach ($results as $result) {
            $options[$result['value']] = $result['value'];
        }
        return $options;
    }

    public function getSizeTypes()
    {
        $types = new Varien_Object(array(
            "standard" => Mage::helper('webforms')->__('Standard'),
            "wide" => Mage::helper('webforms')->__('Wide'),
        ));

        // add more size types
        Mage::dispatchEvent('webforms_fields_size_types', array('types' => $types));

        return $types->getData();

    }

    public function getAllowedExtensions()
    {
        if ($this->getType() == 'image')
            return array('jpg', 'jpeg', 'gif', 'png');
        if ($this->getType() == 'file') {
            $allowed_extensions = explode("\n", trim($this->getValue()));
            $allowed_extensions = array_map('trim', $allowed_extensions);
            $allowed_extensions = array_map('strtolower', $allowed_extensions);
            $filtered_result = array();
            foreach ($allowed_extensions as $ext) {
                if (strlen($ext) > 0) {
                    $filtered_result[] = $ext;
                }
            }
            return $filtered_result;
        }
        return;
    }

    public function getStarsCount()
    {
        //return default value
        $options = $this->getOptionsArray();
        $value = 0;
        if (!empty($options[0])) $value = (int)$options[0]['value'];
        if ($value > 0) return $value;
        return 5;
    }

    public function getStarsOptions()
    {
        $count = $this->getStarsCount();
        $options = array();
        for ($i = 0; $i <= $count; $i++) {
            $options[$i] = $i;
        }
        return $options;
    }

    public function getDateType()
    {
        $type = "medium";
        $allowed_types = array('short', 'medium', 'long', 'full');
        $value = trim($this->getValue());
        if (in_array($value, $allowed_types)) {
            $type = $value;
        }
        return $type;
    }

    public function getDateFormat()
    {
        $format = Mage::app()->getLocale()->getDateFormat($this->getDateType());
        if ($this->getType() == 'datetime') {
            $format = Mage::app()->getLocale()->getDateTimeFormat($this->getDateType());
        }
        return $format;
    }

    public function getDateStrFormat()
    {
        $str_format = Varien_Date::convertZendToStrftime($this->getDateFormat(), true, true);
        return $str_format;
    }

    public function getDbDateFormat()
    {
        $format = "Y-m-d";
        if ($this->getType() == 'datetime') {
            $format = "Y-m-d H:i:s";
        }
        return $format;
    }

    public function formatDate($value)
    {
        if (strlen($value) > 0) {
            $format = $this->getDateStrFormat();
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
            }
            return strftime($format, strtotime($value));
        }
        return;
    }

    public function isCheckedOption($value)
    {
        $customer_value = $this->getData('customer_value');
        if ($customer_value) {
            $customer_values_array = explode("\n", $customer_value);
            foreach ($customer_values_array as $val) {
                if (trim($val) == $this->getRealCheckedOptionValue($value)) {
                    return true;
                }
            }
            return false;
        }
        if (substr($value, 0, 1) == '^')
            return true;
        return false;
    }

    public function isNullOption($value)
    {
        if (substr($value, 0, 2) == '^^')
            return true;
        if (stristr($value, '{{null}}'))
            return true;
        return false;
    }

    public function getCheckedOptionValue($value)
    {
        $value = preg_replace($this->img_regex, "", $value);
        $value = str_replace('{{null}}', '', $value);

        if ($this->isNullOption($value) && substr($value, 0, 2) == '^^')
            return trim(substr($value, 2));
        if (substr($value, 0, 1) == '^')
            return trim(substr($value, 1));
        return trim($value);
    }

    public function getRealCheckedOptionValue($value)
    {
        $value = preg_replace($this->img_regex, "", $value);
        $matches = array();
        preg_match($this->val_regex, $value, $matches);
        if (!empty($matches[1])) {
            $value = trim($matches[1]);
        }

        if ($this->isNullOption($value))
            return trim(substr($value, 2));
        if (substr($value, 0, 1) == '^')
            return trim(substr($value, 1));
        return trim($value);
    }

    public function getOptionsArray()
    {
        $options = array();
        $values = explode("\n", $this->getValue());
        foreach ($values as $val) {
            $image_src = false;

            $matches = array();
            preg_match($this->img_regex, $val, $matches);
            if (!empty($matches[1])) {
                $image_src = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $matches[1];
            }

            if (strlen(trim($val)) > 0) {
                $value = $this->getCheckedOptionValue($val);
                $label = $value;

                if (Mage::getStoreConfig('webforms/general/use_translation')) $label = Mage::helper('webforms')->__($value);

                $matches = array();
                preg_match($this->val_regex, $value, $matches);
                if (!empty($matches[1])) {
                    $value = trim($matches[1]);
                }

                $options[] = array(
                    'value' => $this->getFilter()->filter($value),
                    'label' => trim($this->getFilter()->filter($label)),
                    'null' => $this->isNullOption($val),
                    'checked' => $this->isCheckedOption($val),
                    'image_src' => $image_src,
                );
            }
        }
        return $options;
    }

    public function getContactArray($value)
    {
        preg_match('/(\w.+) <([^<]+?)>/', $value, $matches);
        if (!empty($matches[1]) && !empty($matches[2]))
            return array("name" => trim($matches[1]), "email" => trim($matches[2]));
        return array("name" => trim($value), "email" => "");
    }

    public function getContactValueById($id)
    {
        $options = $this->getOptionsArray();
        if (!empty($options[$id]['value']))
            return $options[$id]['value'];
        return false;
    }

    public function getHiddenFieldValue()
    {
        $result = $this->getData('result');
        $customer_value = $result ? $result->getData('field_' . $this->getId()) : false;
        if ($customer_value) return $customer_value;

        preg_match($this->php_regex, $this->getValue(), $matches);
        if (!empty($matches[1])) {
            ob_start();
            try {
                eval($matches[1]);
            } catch (Exception $e) {

            }
            return ob_get_clean();
        }

        return $this->getValue();
    }

    public function getFilter()
    {
        $filter = new Varien_Filter_Template_Simple();

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getDefaultBillingAddress()) {
            foreach ($customer->getDefaultBillingAddress()->getData() as $key => $value)
                $filter->setData($key, $value);
        }

        $customer_data = $customer->getData();
        foreach ($customer_data as $key => $value) {
            $filter->setData($key, $value);
        }

        return $filter;
    }

    public function toHtml()
    {
        $filter = $this->getFilter();

        // apply custom filter
        Mage::dispatchEvent('webforms_fields_tohtml_filter', array('filter' => $filter));

        $field_id = "field" . $this->getId();
        $field_name = "field[" . $this->getId() . "]";
        $field_value = $filter->filter($this->getValue());
        $result = $this->getData('result');
        $customer_value = $result ? $result->getData('field_' . $this->getId()) : false;
        $this->setData('customer_value', $customer_value);
        $field_type = $this->getType();
        $field_class = "input-text";
        $field_style = "";

        if ($field_type == 'file' || $field_type == 'image') {
            $field_class = "input-file";
        }
        if ($this->getRequired())
            $field_class .= " required-entry";
        if ($field_type == "email")
            $field_class .= " validate-email";
        if ($field_type == "number")
            $field_class .= " validate-number";
        if ($field_type == "url")
            $field_class .= " validate-url";
        if ($this->getCssClass()) {
            $field_class .= ' ' . $this->getCssClass();
        }
        if ($this->getData('validate_length_min') || $this->getData('validate_length_max')) {
            $field_class .= ' validate-length';
        }
        if ($this->getData('validate_length_min')) {
            $field_class .= ' minimum-length-' . $this->getData('validate_length_min');
        }
        if ($this->getData('validate_length_max')) {
            $field_class .= ' maximum-length-' . $this->getData('validate_length_max');
        }
        if ($this->getData('validate_regex')) {
            $field_class .= ' validate-field-' . $this->getId();
        }
        if ($this->getRequired() && $this->getHint()) {
            $field_class .= ' validate-field-hint-'. $this->getId();
        }
        if ($this->getCssStyle()) {
            $field_style = $this->getCssStyle();
        }
        $tinyMCE = false;
        $showTime = false;
        $calendar = false;
        $config = array(
            'field' => $this,
            'field_id' => $field_id,
            'field_name' => $field_name,
            'field_class' => $field_class,
            'field_style' => $field_style,
            'field_value' => $field_value,
            'result' => $result,
            'show_time' => 'false',
            'customer_value' => $customer_value,
            'template' => 'webforms/fields/text.phtml'
        );

        switch ($field_type) {
            case 'textarea':
                if ($customer_value) {
                    $config['field_value'] = $customer_value;
                }
                $config['template'] = 'webforms/fields/textarea.phtml';
                break;
            case 'wysiwyg':
                $tinyMCE = true;
                if ($customer_value) {
                    $config['field_value'] = $customer_value;
                }
                $config['template'] = 'webforms/fields/wysiwyg.phtml';
                break;
            case 'select':
                $config['field_options'] = $this->getOptionsArray();
                $config['template'] = 'webforms/fields/select.phtml';
                break;
            case 'select/contact':
                $config['field_options'] = $this->getOptionsArray();
                $config['template'] = 'webforms/fields/select_contact.phtml';
                break;
            case 'select/radio':
                $config['field_class'] = $this->getCssClass();
                $config['field_options'] = $this->getOptionsArray();
                $config['template'] = 'webforms/fields/select_radio.phtml';
                break;
            case 'select/checkbox':
                $config['field_class'] = $this->getCssClass();
                $config['field_options'] = $this->getOptionsArray();
                $config['template'] = 'webforms/fields/select_checkbox.phtml';
                break;
            case 'subscribe':
                $config['field_class'] = $this->getCssClass();
                $config['template'] = 'webforms/fields/subscribe.phtml';
                break;
            case 'stars':
                $config['field_options'] = $this->getOptionsArray();
                $config['template'] = 'webforms/fields/stars.phtml';
                break;
            case 'image':
            case 'file':
                $config['field_id'] = 'file_' . $this->getId();
                $config['field_name'] = $config['field_id'];
                $config['template'] = 'webforms/fields/file.phtml';
                break;
            case 'html':
                $config['template'] = 'webforms/fields/html.phtml';
                break;
            case 'datetime':
                $config['show_time'] = 'true';
                $showTime = true;
            case 'date':
            case 'datetime':
                $calendar = true;
                if ($customer_value) {
                    // format customer value
                    $config['customer_value'] = Mage::helper('core')->formatDate($customer_value, $this->getDateType(), $showTime);
                }
                $config['template'] = 'webforms/fields/date.phtml';
                break;
            case 'hidden':
                $config['template'] = 'webforms/fields/hidden.phtml';
                break;
            case 'country':
                $config['template'] = 'webforms/fields/country.phtml';
                break;
            default:
                if ($customer_value) {
                    $config['field_value'] = $customer_value;
                }
                $config['template'] = 'webforms/fields/text.phtml';
                break;
        }
        $layout = Mage::app()->getLayout();


        $html = $layout->createBlock('core/template', $field_name, $config)->toHtml();

        if ($this->getData('validate_regex')) {
            $flags = array();

            $regexp = trim($this->getData('validate_regex'));

            preg_match('/\/([igmy]{1,4})$/',$regexp, $flags);

            if(!empty($flags[1])){
                $flags = $flags[1];
                $regexp = substr($regexp,0, strlen($regexp) - strlen($flags));
            }

            if(substr($regexp,0,1) == '/' && substr($regexp,strlen($regexp)-1,strlen($regexp)) == '/')
                $regexp = substr($regexp,1,-1);

            $validate_message = trim(str_replace("'", "\'", $this->getData('validate_message')));
            $html .= "<script>Validation.add('validate-field-{$this->getId()}','{$validate_message}',function(v){var r = new RegExp('{$regexp}','{$flags}');return Validation.get('IsEmpty').test(v) || r.test(v);})</script>";
        }

        if ($this->getRequired() && $this->getHint()) {
            $validate_message = Mage::helper('core')->__('This is a required field.');
            $hint = trim(str_replace("'", "\'",$this->getHint()));
            $html .= "<script>Validation.add('validate-field-hint-{$this->getId()}','{$validate_message}',function(v){return v.trim() != '{$hint}';})</script>";
        }

        // activate tinyMCE
        if ($tinyMCE && !Mage::registry('tinyMCE')) {
            Mage::register('tinyMCE', true);
            $tiny_mce = $layout->createBlock('core/template', 'tinyMCE', array('template' => 'webforms/scripts/tiny_mce.phtml'));
            $html .= $tiny_mce->toHtml();
        }

        // activate calendar
        if ($calendar && !Mage::registry('calendar')) {
            Mage::register('calendar', true);
            $calendar_block = $layout->createBlock('core/html_calendar', 'calendar_block', array
            (
                'as' => 'calendar',
                'template' => 'page/js/calendar.phtml'
            ));
            $html .= $calendar_block->toHtml();
        }

        // apply custom field type
        $html_object = new Varien_Object(array('html' => $html));
        Mage::dispatchEvent('webforms_fields_tohtml_html', array('field' => $this, 'html_object' => $html_object));

        return $html_object->getHtml();
    }

    public function duplicate()
    {
        // duplicate field
        $field = Mage::getModel('webforms/fields')
            ->setData($this->getData())
            ->setId(null)
            ->setName($this->getName() . ' ' . Mage::helper('webforms')->__('(new copy)'))
            ->setIsActive(false)
            ->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())
            ->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
            ->save();

        // duplicate store data
        $stores = Mage::getModel('webforms/store')
            ->getCollection()
            ->addFilter('entity_id', $this->getId())
            ->addFilter('entity_type', $this->getEntityType());

        foreach ($stores as $store) {
            $duplicate = Mage::getModel('webforms/store')
                ->setData($store->getData())
                ->setId(null)
                ->setEntityId($field->getId())
                ->save();
        }

        return $field;
    }

    public function getLogic()
    {
        $collection = Mage::getModel('webforms/logic')->setStoreId($this->getStoreId())->getCollection()->addFilter('field_id', $this->getId());
        return $collection;
    }

    public function getLogicTargetOptionsArray()
    {
        $options = array();
        $webform = Mage::getModel('webforms/webforms')->setStoreId($this->getStoreId())->load($this->getWebformId());
        $fields_to_fieldsets = $webform->getFieldsToFieldsets(true);

        foreach ($fields_to_fieldsets as $fieldset_id => $fieldset) {
            $field_options = array();
            foreach ($fieldset['fields'] as $field) {
                if ($field->getId() != $this->getId() && $field->getType() != 'hidden')
                    $field_options[] = array('value' => 'field_' . $field->getId(), 'label' => $field->getName());
            }

            if ($fieldset_id) {
                if ($this->getFieldsetId() != $fieldset_id)
                    $options[] = array('value' => 'fieldset_' . $fieldset_id, 'label' => $fieldset['name'] . ' [' . Mage::helper('webforms')->__('Field Set') . ']');
                if (count($field_options)) {
                    $options[] = array('value' => $field_options, 'label' => $fieldset['name']);
                }
            } else {
                foreach ($field_options as $opt) {
                    $options[] = $opt;
                }
            }
        }

        return $options;
    }
}