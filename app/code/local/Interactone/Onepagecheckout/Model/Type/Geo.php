<?php

class Interactone_Onepagecheckout_Model_Type_Geo extends IWD_OnepageCheckout_Model_Type_Geo
{
    /**
     * Validate customer data and set some its data for further usage in quote
     * Will return either true or array with error messages
     *
     * @param array $data
     * @return true|array
     */
    protected function _validateCustomerData(array $data)
    {
        /** @var $customerForm Mage_Customer_Model_Form */
        $customerForm = Mage::getModel('customer/form');
        $customerForm->setFormCode('checkout_register')
            ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());

        $quote = $this->getQuote();
        if ($quote->getCustomerId()) {
            $customer = $quote->getCustomer();
            $customerForm->setEntity($customer);
            $customerData = $quote->getCustomer()->getData();
        } else {
            /* @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer');
            $customerForm->setEntity($customer);
            $customerRequest = $customerForm->prepareRequest($data);
            $customerData = $customerForm->extractData($customerRequest);
        }

        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
            );
        }

        if ($quote->getCustomerId()) {
            return true;
        }

        $customerForm->compactData($customerData);

        if ($quote->getCheckoutMethod() == self::REGISTER) {
            // set customer password
            $customer->setPassword($customerRequest->getParam('customer_password'));
            $customer->setConfirmation($customerRequest->getParam('confirm_password'));
            $customer->setPasswordConfirmation($customerRequest->getParam('confirm_password')); //<---- Added to resolve conflict with CE1.9.1 password verification
        } else {
            // spoof customer password for guest
            $password = $customer->generatePassword();
            $customer->setPassword($password);
            $customer->setConfirmation($password);
            $customer->setPasswordConfirmation($password); //<---- Added to resolve conflict with CE1.9.1 password verification
            // set NOT LOGGED IN group id explicitly,
            // otherwise copyFieldset('customer_account', 'to_quote') will fill it with default group id value
            $customer->setGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        }

        $result = $customer->validate();
        if (true !== $result && is_array($result)) {
            return array(
                'error'   => -1,
                'message' => implode(', ', $result)
            );
        }

        if ($quote->getCheckoutMethod() == self::REGISTER) {
            // save customer encrypted password in quote
            $quote->setPasswordHash($customer->encryptPassword($customer->getPassword()));
        }

        // copy customer/guest email to address
        $quote->getBillingAddress()->setEmail($customer->getEmail());

        // copy customer data to quote
        Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $quote);

        return true;
    }

    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getGender())
            $this->getQuote()->setCustomerGender($address->getGender());

        $dob = '';
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        if ($address->getTaxvat())
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());

        if ($this->getQuote()->getCheckoutMethod() == self::REGISTER)
        {
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

            $cust_data	= array(
                'email'        => 'email',
                'password'     => 'customer_password',
                'confirmation' => 'confirm_password',
                'firstname'    => 'firstname',
                'lastname'     => 'lastname',
                'gender'       => 'gender',
                'taxvat'       => 'taxvat');

            foreach ($cust_data as $key => $value)
                $customer->setData($key, $address->getData($value));

            if ($dob) {
                $customer->setDob($dob);
            }

//            $val_result = $customer->validate();
//            if ($val_result !== true && is_array($val_result)) {
//                return array('message' => implode(', ', $val_result), 'error'   => -1);
//            }
        }
        elseif($this->getQuote()->getCheckoutMethod() == self::GUEST)
        {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress'))
                return array('message' => $this->_help_obj->__('Invalid email address "%s"', $email), 'error'   => -1);
        }

        return true;
    }

}