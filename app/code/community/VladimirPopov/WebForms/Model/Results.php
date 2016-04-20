<?php

/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */
class VladimirPopov_WebForms_Model_Results
    extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_NOTAPPROVED = -1;

    public function _construct()
    {
        parent::_construct();
        $this->_init('webforms/results');
    }

    public function addFieldArray($preserveFrontend = false, $field_types = array())
    {
        $data = $this->getData();
        $field_array = array();
        foreach ($data as $key => $value) {
            if (strstr($key, 'field_')) {
                $field_id = str_replace('field_', '', $key);
                $field = Mage::getModel('webforms/fields')->load($field_id);
                if ($field->getType() == 'select/checkbox' && !is_array($value)) $value = explode("\n", $value);
                if ($field->getType() == 'select/contact' && $preserveFrontend) {
                    $contact_array = $field->getContactArray($field->getValue());
                    for ($i = 0; $i < count($contact_array); $i++) {

                        if ($field->getContactValueById($i) == $value) {
                            $value = $i;
                            break;
                        }
                    }
                }
                if(!count($field_types) || (count($field_types) && in_array($field->getType(),$field_types)))
                    $field_array[$field_id] = $value;
            }
        }
        $this->setData('field', $field_array);
        return $this;
    }

    public function getApprovalStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_PENDING => Mage::helper('webforms')->__('Pending'),
            self::STATUS_APPROVED => Mage::helper('webforms')->__('Approved'),
            self::STATUS_NOTAPPROVED => Mage::helper('webforms')->__('Not Approved'),
        ));

        Mage::dispatchEvent('webforms_results_statuses', array('statuses' => $statuses));

        return $statuses->getData();

    }

    public function getCustomer()
    {
        if (!$this->getCustomerId()) return false;
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        return $customer;
    }

    public function sendEmail($recipient = 'admin', $contact = false)
    {
        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());
        if (!Mage::registry('webform'))
            Mage::register('webform', $webform);

        $emailSettings = $webform->getEmailSettings();

        // for admin
        $sender = Array(
            'name' => $this->getCustomerName(),
            'email' => $this->getReplyTo($recipient),
        );

        if (!$sender['name']) {
            $sender['name'] = $sender['email'];
        }

        // for customer
        if ($recipient == 'customer') {
            $sender['name'] = Mage::app()->getStore($this->getStoreId())->getFrontendName();
            $contact_array = $this->getContactArray();

            // send letter from selected contact
            if ($contact_array) {
                $sender = $contact_array;
            }
        }

        if (Mage::getStoreConfig('webforms/email/email_from')) {
            $sender['email'] = Mage::getStoreConfig('webforms/email/email_from');
        }

        $subject = $this->getEmailSubject($recipient);

        $email = $emailSettings['email'];

        //for customer
        if ($recipient == 'customer') {
            $email = $this->getCustomerEmail();
        }

        $name = Mage::app()->getStore($this->getStoreId())->getFrontendName();

        if ($recipient == 'customer') {
            $name = $this->getCustomerName();
        }

        if ($recipient == 'contact') {
            if (empty($contact['email'])) return false;
            $email = $contact['email'];
            $name = $contact['name'];
            $recipient = 'admin';
        }

        $webformObject = new Varien_Object();
        $webformObject->setData($webform->getData());

        $store_group = Mage::app()->getStore($this->getStoreId())->getFrontendName();
        $store_name = Mage::app()->getStore($this->getStoreId())->getName();

        $vars = Array(
            'webform_subject' => $subject,
            'webform_name' => $webform->getName(),
            'webform_result' => $this->toHtml($recipient),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
            'ip' => $this->getIp(),
            'store_group' => $store_group,
            'store_name' => $store_name,
            'result' => $this->getTemplateResultVar(),
            'webform' => $webformObject,
        );

        $customer = $this->getCustomer();

        if ($customer) {
            $customerObject = new Varien_Object();
            $customerObject->setData($customer->getData());
            $vars['customer'] = $customerObject;
        }

        $post = Mage::app()->getRequest()->getPost();

        if ($post) {
            $postObject = new Varien_Object();
            $postObject->setData($post);

            // set region name if found
            if (!empty($post['region_id'])) {
                $postObject->setData('region_name', $post['region_id']);
                $region_name = Mage::getModel('directory/region')->load($post['region_id'])->getName();
                if ($region_name) {
                    $postObject->setData('region_name', $region_name);
                }
            }
            $vars['data'] = $postObject;
        }

        $vars['noreply'] = Mage::helper('webforms')->__('Please, don`t reply to this e-mail!');

        $storeId = $this->getStoreId();
        $templateId = 'webforms_notification';
        if ($webform->getEmailTemplateId()) {
            $templateId = $webform->getEmailTemplateId();
        }
        if ($recipient == 'customer') {
            if ($webform->getEmailCustomerTemplateId()) {
                $templateId = $webform->getEmailCustomerTemplateId();
            }
        }
        $file_list = $this->getFiles();
        $send_multiple_admin = false;
        if(is_string($email)){
            if($recipient == 'admin' && strstr($email,','))
                $send_multiple_admin = true;
        }

        if ($send_multiple_admin) {
            $email_array = explode(',', $email);
            foreach ($email_array as $email) {

                $mail = Mage::getModel('core/email_template')
                    ->setTemplateSubject($subject)
                    ->setReplyTo($this->getReplyTo($recipient));

                //file content is attached
                if ($webform->getEmailAttachmentsAdmin())
                    foreach ($file_list as $file) {
                        $path = $file['path'];
                        $attachment = file_get_contents($path);
                        $mail->getMail()->createAttachment(
                            $attachment,
                            Zend_Mime::TYPE_OCTETSTREAM,
                            Zend_Mime::DISPOSITION_ATTACHMENT,
                            Zend_Mime::ENCODING_BASE64,
                            $file['name']
                        );
                    }

                //attach pdf version to email
                if($webform->getPrintAttachToEmail()){
                    require_once(Mage::getBaseDir('lib').'/Webforms/mpdf/mpdf.php');
                    $mpdf = new mPDF('utf-8', 'A4');
                    $mpdf->WriteHTML($this->toPrintableHtml());

                    $mail->getMail()->createAttachment(
                        $mpdf->Output('','S'),
                        Zend_Mime::TYPE_OCTETSTREAM,
                        Zend_Mime::DISPOSITION_ATTACHMENT,
                        Zend_Mime::ENCODING_BASE64,
                        'result'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s', $this->getCreatedTime()).'.pdf'
                    );
                }

                $mail->sendTransactional($templateId, $sender, trim($email), $name, $vars, $storeId);

            }
        } else {
            $mail = Mage::getModel('core/email_template')
                ->setTemplateSubject($subject)
                ->setReplyTo($this->getReplyTo($recipient));
            //file content is attached
            if (($webform->getEmailAttachmentsAdmin() && $recipient == 'admin') || ($webform->getEmailAttachmentsCustomer() && $recipient == 'customer'))
                foreach ($file_list as $file) {
                    $path = $file['path'];
                    $attachment = file_get_contents($path);
                    $mail->getMail()->createAttachment(
                        $attachment,
                        Zend_Mime::TYPE_OCTETSTREAM,
                        Zend_Mime::DISPOSITION_ATTACHMENT,
                        Zend_Mime::ENCODING_BASE64,
                        $file['name']
                    );
                }

            //attach pdf version to email
            if($webform->getPrintAttachToEmail() && $recipient == 'admin'){
                require_once(Mage::getBaseDir('lib').'/Webforms/mpdf/mpdf.php');
                $mpdf = new mPDF('utf-8', 'A4');
                $mpdf->WriteHTML($this->toPrintableHtml());

                $mail->getMail()->createAttachment(
                    $mpdf->Output('','S'),
                    Zend_Mime::TYPE_OCTETSTREAM,
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    'result'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s', $this->getCreatedTime()).'.pdf'
                );
            }
            $mail->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
        }
    }

    public function sendApprovalEmail()
    {
        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());

        // for customer
        $sender['name'] = Mage::app()->getStore($this->getStoreId())->getFrontendName();

        $sender['email'] = $this->getReplyTo('customer');

        if (Mage::getStoreConfig('webforms/email/email_from')) {
            $sender['email'] = Mage::getStoreConfig('webforms/email/email_from');
        }

        $email = $this->getCustomerEmail();

        $name = $this->getCustomerName();

        $webformObject = new Varien_Object();
        $webformObject->setData($webform->getData());

        $varResult = $this->getTemplateResultVar();

        $varResult->addData(array(
            'id' => $this->getId(),
            'subject' => $this->getEmailSubject(),
            'date' => Mage::helper('core')->formatDate($this->getCreatedTime()),
            'html' => $this->toHtml('customer'),
        ));

        $store_group = Mage::app()->getStore($this->getStoreId())->getFrontendName();
        $store_name = Mage::app()->getStore($this->getStoreId())->getName();

        $vars = Array(
            'webform_name' => $webform->getName(),
            'webform_result' => $this->toHtml('customer'),
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
            'ip' => $this->getIp(),
            'store_group' => $store_group,
            'store_name' => $store_name,
            'result' => $varResult,
            'webform' => $webformObject,
        );

        $customer = $this->getCustomer();

        if ($customer) {
            $customerObject = new Varien_Object();
            $customerObject->setData($customer->getData());
            $vars['customer'] = $customerObject;
        }

        $storeId = $this->getStoreId();

        if ($this->getApproved() == self::STATUS_APPROVED) {
            $templateId = 'webforms_result_approved';
            if ($webform->getData('email_result_approved_template_id')) {
                $templateId = $webform->getData('email_result_approved_template_id');
            }
        } else if ($this->getApproved() == self::STATUS_NOTAPPROVED) {
            $templateId = 'webforms_result_notapproved';
            if ($webform->getData('email_result_notapproved_template_id')) {
                $templateId = $webform->getData('email_result_notapproved_template_id');
            }
        } else
            return false;

        $mail = Mage::getModel('core/email_template')
            ->setReplyTo($this->getReplyTo('customer'));

        $mail->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);

    }

    public function toPrintableHtml()
    {
        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());

        $webformObject = new Varien_Object();
        $webformObject->setData($webform->getData());

        $varResult = $this->getTemplateResultVar();

        $varResult->addData(array(
            'id' => $this->getId(),
            'subject' => $this->getEmailSubject(),
            'date' => Mage::helper('core')->formatDate($this->getCreatedTime()),
            'html' => $this->toHtml('customer'),
        ));

        $store_group = Mage::app()->getStore($this->getStoreId())->getFrontendName();
        $store_name = Mage::app()->getStore($this->getStoreId())->getName();

        $vars = Array(
            'webform_name' => $webform->getName(),
            'webform_result' => $this->toHtml(),
            'result' => $varResult,
            'customer_name' => $this->getCustomerName(),
            'customer_email' => $this->getCustomerEmail(),
            'ip' => $this->getIp(),
            'store_group' => $store_group,
            'store_name' => $store_name,
            'webform' => $webformObject,
        );

        $customer = $this->getCustomer();

        if ($customer) {
            $customerObject = new Varien_Object();
            $customerObject->setData($customer->getData());
            $vars['customer'] = $customerObject;
        }

        $templateId = 'webforms_result_print';
        $template = Mage::getModel('core/email_template')->loadDefault($templateId);
        if ($webform->getPrintTemplateId()) {
            $templateId = $webform->getPrintTemplateId();
            $template->load($templateId);
        }

        $filter = Mage::helper('cms')->getPageTemplateProcessor();
        $filter->setVariables($vars);

        $html = $filter->filter($template->getTemplateText());

        return $html;
    }

    public function getFiles()
    {
        $file_list = array();
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId());
        foreach ($fields as $field) {
            if ($field->getType() == 'file' || $field->getType() == 'image') {
                foreach ($this->getData() as $key => $value) {
                    if ($key == 'field_' . $field->getId() && $value) {
                        $file_list[] = array('name' => $value, 'path' => $this->getFileFullPath($field->getId(), $value));
                    }
                }
            }
        }
        return $file_list;
    }

    public function getEmailSubject($recipient = 'admin')
    {
        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());
        $webform_name = $webform->getName();
        $store_name = Mage::app()->getStore($this->getStoreId())->getFrontendName();

        //get default subject for admin
        $subject = Mage::helper('webforms')->__("Web-form '%s' submitted", $webform_name);

        //get subject for customer
        if ($recipient == 'customer') {
            $subject = Mage::helper('webforms')->__("You have submitted '%s' form on %s website", $webform_name, $store_name);
        }

        //iterate through fields and build subject
        $subject_array = array();
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId())
            ->addFilter('email_subject', 1);
        foreach ($fields as $field) {
            foreach ($this->getData() as $key => $value) {
                if ($key == 'field_' . $field->getId() && $value) {
                    $subject_array[] = $value;
                }
            }
        }
        if (count($subject_array) > 0) {
            $subject = implode(" / ", $subject_array);
        }
        return $subject;
    }

    public function getCustomerName()
    {
        $customer_name = array();
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId());
        foreach ($fields as $field) {
            foreach ($this->getData() as $key => $value) {
                if (is_string($value)) $value = trim($value);
                if ($key == 'field_' . $field->getId() && $value) {
                    if (
                        $field->getCode() == 'name' ||
                        $field->getCode() == 'firstname' ||
                        $field->getCode() == 'lastname' ||
                        $field->getCode() == 'middlename'
                    ) $customer_name[] = $value;
                }
            }
        }

        if (count($customer_name) == 0)
            if ($this->getCustomerId()) {
                $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
                if ($customer->getId())
                    return $customer->getName();
            }

        if (count($customer_name) == 0)
            return Mage::helper('core')->__('Guest');

        return implode(' ', $customer_name);

    }

    public function getContactArray()
    {

        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId())
            ->addFilter('type', 'select/contact');

        foreach ($fields as $field) {
            foreach ($this->getData() as $key => $value) {
                if ($key == 'field_' . $field->getId() && $value) {
                    return $field->getContactArray($value);
                }
            }
        }

        return false;
    }

    public function getTemplateResultVar()
    {
        $result = new Varien_Object(array(
            'id' => $this->getId(),
            'webform_id' => $this->getWebformId(),
        ));
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId());
        foreach ($fields as $field) {
            foreach ($this->getData() as $key => $value) {
                if (is_string($value)) $value = trim($value);
                if ($key == 'field_' . $field->getId() && $value) {
                    switch ($field->getType()) {
                        case 'date':
                        case 'datetime':
                            $data_value = $field->formatDate($value);
                            break;
                        case 'image':
                            $data_value = '<a style="text-decoration:none" href="' . $this->getDownloadLink($field->getId(), $value) . '"><img src="' . $this->getThumbnail($field->getId(), $value, 200) . '"/></a>';
                            break;
                        case 'file':
                            $data_value = '<a href="' . $this->getDownloadLink($field->getId(), $value) . '">' . $value . '</a>';
                            break;
                        case 'stars':
                            $data_value = $value . ' / ' . $field->getStarsCount();
                            break;
                        case 'select/contact':
                            $contact = $field->getContactArray($value);
                            !empty($contact["name"]) ? $data_value = $contact["name"] : $data_value = $value;
                            break;
                        default:
                            $data_value = nl2br($value);
                            break;
                    }
                    $data = new Varien_Object(array(
                        'value' => $data_value,
                        'name' => $field->getName(),
                        'result_label' => $field->getResultLabel(),
                    ));
                    $result->setData($field->getId(), $data);
                    if ($field->getCode()) {
                        $result->setData($field->getCode(), $data);
                    }
                }
            }
        }
        return $result;
    }

    public function getReplyTo($recipient = 'admin')
    {
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId())
            ->addFilter('type', 'email');

        $webform = Mage::getModel('webforms/webforms')->setStoreId($this->getStoreId())->load($this->getWebformId());

        $reply_to = false;

        foreach ($this->getData() as $key => $value) {
            if ($key == 'field_' . $fields->getFirstItem()->getId()) {
                $reply_to = $value;
            }
        }
        if (!$reply_to) {
            if (Mage::helper('customer')->isLoggedIn()) {
                $reply_to = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
            } else {
                $reply_to = Mage::getStoreConfig('trans_email/ident_general/email', $this->getStoreId());
            }
        }
        if ($recipient == 'customer') {
            if ($webform->getEmailReplyTo())
                $reply_to = $webform->getEmailReplyTo();
            elseif (Mage::getStoreConfig('webforms/email/email_reply_to', $this->getStoreId()))
                $reply_to = Mage::getStoreConfig('webforms/email/email_reply_to', $this->getStoreId());
            else
                $reply_to = Mage::getStoreConfig('trans_email/ident_general/email', $this->getStoreId());
        }
        return $reply_to;
    }

    public function getCustomerEmail()
    {
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getWebformId())
            ->addFilter('type', 'email');

        $customer_email = array();
        foreach ($this->getData() as $key => $value) {
            foreach ($fields as $field)
                if ($key == 'field_' . $field->getId()) {
                    if (strlen(trim($value)) > 0) $customer_email [] = $value;
                }
        }

        if (!count($customer_email)) {
            // try to get email by customer id
            if ($this->getCustomerId())
                $customer_email [] = Mage::getModel('customer/customer')->load($this->getCustomerId())->getEmail();
        }

        if (!count($customer_email)) {
            if (Mage::helper('customer')->isLoggedIn()) {
                $customer_email [] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
            }
        }

        if (!count($customer_email)) {
            // try to get $_POST['email'] variable
            if (Mage::app()->getRequest()->getPost('email'))
                $customer_email [] = Mage::app()->getRequest()->getParam('email');
        }

        return $customer_email;
    }

    public function getFileSizeText($field_id, $filename)
    {
        $filename = Varien_File_Uploader::getCorrectFileName($filename);
        $loc = $this->getFileFullPath($field_id, $filename);
        $size = @filesize($loc);
        $sizes = array(" bytes", " kb", " mb", " gb", " tb", " pb", " eb", " zb", " yb");
        if ($size == 0) {
            return ('n/a');
        } else {
            return (round($size / pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i]);
        }
    }

    public function getFileFullPath($field_id, $filename)
    {
        return $this->getFilePath($field_id) . $filename;
    }

    public function getFilePath($field_id)
    {
        $path = Mage::getBaseDir('media') . DS . 'webforms' . DS . $this->getId() . DS . $field_id . DS;

        if ($this->getData('key_' . $field_id)) {
            $path .= $this->getData('key_' . $field_id) . DS;
        }

        return $path;
    }

    // path relative to Media folder
    public function getRelativePath($field_id, $filename)
    {
        $path = 'webforms/' . $this->getId() . '/' . $field_id . '/';
        if ($this->getData('key_' . $field_id)) {
            $path .= $this->getData('key_' . $field_id) . '/';
        }
        $path .= rawurlencode(Varien_File_Uploader::getCorrectFileName($filename));
        return $path;
    }

    public function getThumbnail($field_id, $filename, $width, $height = null)
    {

        $filename = Varien_File_Uploader::getCorrectFileName($filename);

        $imageUrl = $this->getFileFullPath($field_id, $filename);

        $file_info = @getimagesize($imageUrl);

        if (!$file_info)
            return false;

        if (strstr($file_info["mime"], "bmp"))
            return false;

        if (file_exists($imageUrl))
            $imageObj = new Varien_Image($imageUrl);

        if (!$height && (float)substr(Mage::getVersion(), 0, 3) > 1)
            $height = round($imageObj->getOriginalHeight() * ($width / $imageObj->getOriginalWidth()));

        $imageResized = $this->getFilePath($field_id) . "thumb" . DS . $width . 'x' . $height . '_' . $filename;

        if (!file_exists($imageResized) && file_exists($imageUrl) || Mage::getStoreConfig('webforms/images/cache') == 0) {
            if ((float)substr(Mage::getVersion(), 0, 3) > 1) {
                $imageObj->keepAspectRatio(true);
                $imageObj->keepTransparency(true);
            }
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "webforms/" . $this->getId() . "/" . $field_id;
        if ($this->getData('key_' . $field_id)) {
            $url .= "/" . $this->getData('key_' . $field_id);
        }
        $url .= "/thumb/" . $width . 'x' . $height . '_' . urlencode($filename);
        return $url;
    }

    public function getDownloadLink($field_id, $filename)
    {
        $filename = Varien_File_Uploader::getCorrectFileName($filename);
        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'webforms/' . $this->getId() . '/' . $field_id . '/';
        if ($this->getData('key_' . $field_id)) {
            $path .= $this->getData('key_' . $field_id) . '/';
        }
        $path .= rawurlencode($filename);
        return $path;
    }

    public function toHtml($recipient = 'admin', $options = array())
    {
        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());

        $this->addFieldArray(true);

        if (!isset($options['header'])) {
            $options['header'] = $webform->getAddHeader();
        }
        if (!isset($options['skip_fields'])) {
            $options['skip_fields'] = array();
        }

        $html = "";
        $store_group = Mage::app()->getStore($this->getStoreId())->getFrontendName();
        $store_name = Mage::app()->getStore($this->getStoreId())->getName();
        if ($recipient == 'admin') {
            if ($store_group)
                $html .= Mage::helper('webforms')->__('Store group') . ": " . $store_group . "<br />";
            if ($store_name)
                $html .= Mage::helper('webforms')->__('Store name') . ": " . $store_name . "<br />";
            $html .= Mage::helper('webforms')->__('Customer') . ": " . $this->getCustomerName() . "<br />";
            $html .= Mage::helper('webforms')->__('IP') . ": " . $this->getIp() . "<br />";
        }
        $html .= Mage::helper('webforms')->__('Date') . ": " . Mage::helper('core')->formatDate($this->getCreatedTime(), 'medium', true) . "<br />";
        $html .= "<br />";

        $head_html = "";
        if ($options['header']) $head_html = $html;

        $html = "";

        $logic_rules = $webform->getLogic();

        $fields_to_fieldsets = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId())
            ->getFieldsToFieldsets(true);

        foreach ($fields_to_fieldsets as $fieldset_id => $fieldset) {

            $k = false;
            $field_html = "";

            $target_fieldset = array("id" => 'fieldset_' . $fieldset_id, 'logic_visibility' => $fieldset['logic_visibility']);
            $fieldset_visibility = $webform->getLogicTargetVisibility($target_fieldset, $logic_rules, $this->getData('field'));

            if ($fieldset_visibility) {
                foreach ($fieldset['fields'] as $field) {
                    $target_field = array("id" => 'field_' . $field->getId(), 'logic_visibility' => $field->getData('logic_visibility'));
                    $field_visibility = $webform->getLogicTargetVisibility($target_field, $logic_rules, $this->getData('field'));
                    $value = $this->getData('field_' . $field->getId());
                    if ($field->getType() == 'html')
                        $value = $field->getValue();

                    if (strlen(trim($value)) && $field_visibility) {
                        if (!in_array($field->getType(), $options['skip_fields']) && $field->getResultDisplay() != 'off') {
                            $field_name = $field->getName();
                            if (strlen(trim($field->getResultLabel())) > 0)
                                $field_name = $field->getResultLabel();
                            if ($field->getResultDisplay() != 'value') $field_html .= '<b>' . $field_name . '</b><br/>';
                            $filename = $value;
                            switch ($field->getType()) {
                                case 'date':
                                case 'datetime':
                                    $value = $field->formatDate($value);
                                    break;
                                case 'stars':
                                    $value = $value . ' / ' . $field->getStarsCount();
                                    break;
                                case 'file':
                                    if (strlen($value) > 1)
                                        $value = '<a href="' . $this->getDownloadLink($field->getId(), $filename) . '">' . $filename . '</a> <small>[' . $this->getFileSizeText($field->getId(), $filename) . ']</small>';
                                    break;
                                case 'image':
                                    if (strlen($value) > 1)
                                        $value = '<a style="text-decoration:none" href="' . $this->getDownloadLink($field->getId(), $filename) . '"><img src="' . $this->getThumbnail($field->getId(), $filename, Mage::getStoreConfig('webforms/images/email_thumbnail_width'), Mage::getStoreConfig('webforms/images/email_thumbnail_height')) . '"/></a>';
                                    break;
                                case 'select/contact':
                                    $contact = $field->getContactArray($value);
                                    if (!empty($contact["name"])) $value = $contact["name"];
                                    break;
                                case 'html':
                                    $value = trim($field->getValue());
                                    break;
                                case 'country':
                                    $country_name = Mage::app()->getLocale()->getCountryTranslation($value);
                                    if ($country_name) $value = $country_name;
                                    break;
                                case 'subscribe':
                                    if ($value) $value = Mage::helper('core')->__('Yes');
                                    else $value = Mage::helper('core')->__('No');
                                    break;
                                default :
                                    $value = nl2br(htmlspecialchars($value));
                                    break;
                            }
                            $k = true;
                            $field_html .= $value . "<br /><br />";
                        }
                    }

                }
            }
            if (!empty($fieldset['name']) && $k && $fieldset['result_display'] == 'on')
                $field_html = '<h2>' . $fieldset['name'] . '</h2>' . $field_html;
            $html .= $field_html;
        }
        return $head_html . $html;

    }

    public function toXml(array $arrAttributes = array(), $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {

        $webform = Mage::getModel('webforms/webforms')
            ->setStoreId($this->getStoreId())
            ->load($this->getWebformId());

        if ($webform->getCode())
            $this->setData('webform_code', $webform->getCode());

        foreach ($this->getData() as $key => $value) {
            if (strstr($key, 'field_')) {
                $field = Mage::getModel('webforms/fields')
                    ->setStoreId($this->getStoreId())
                    ->load(str_replace('field_', '', $key));
                if (!empty($field) && $field->getCode()) {
                    $this->setData($field->getCode(), $value);
                }
            }
        }
        return parent::toXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
    }
}

