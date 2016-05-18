<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Pquestion2
 * @version    2.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Pquestion2_Model_Observer
{
    public function questionSaveAfter(Varien_Event_Observer $observer)
    {
        $question = $observer->getEvent()->getQuestion();
        if ($question->isObjectNew()) {
            $emailValidator = new Zend_Validate_EmailAddress;
            if (Mage::helper('aw_pq2/config')->getSendNewQuestionTo()
                && $emailValidator->isValid(Mage::helper('aw_pq2/config')->getSendNewQuestionTo())
            ) {
                try {
                    //new question to admin
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        Mage::helper('aw_pq2')->__('Administrator'),
                        Mage::helper('aw_pq2/config')->getSendNewQuestionTo(),
                        AW_Pquestion2_Model_Source_Notification_Type::NEW_QUESTION_TO_ADMIN,
                        $this->_getNewQuestionAdminVariables($question),
                        $question->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $_isCanNotify = Mage::helper('aw_pq2/notification')->isCanNotifyCustomer(
                $question->getAuthorEmail(),
                AW_Pquestion2_Model_Source_Notification_Type::QUESTION_AUTO_RESPONDER
            );
            if ($_isCanNotify) {
                try {
                    //auto responder new question to question owner
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        $question->getAuthorName(),
                        $question->getAuthorEmail(),
                        AW_Pquestion2_Model_Source_Notification_Type::QUESTION_AUTO_RESPONDER,
                        $this->_getNewQuestionCustomerVariables($question),
                        $question->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        $_isCanNotify = Mage::helper('aw_pq2/notification')->isCanNotifyCustomer(
            $question->getAuthorEmail(),
            AW_Pquestion2_Model_Source_Notification_Type::QUESTION_STATUS_CHANGE_TO_CUSTOMER
        );
        if (!$question->isObjectNew() && $question->getOrigData('status') != $question->getStatus() && $_isCanNotify) {
            try {
                //question change status notification to question owner
                Mage::getModel('aw_pq2/notification')->addToQueue(
                    $question->getAuthorName(),
                    $question->getAuthorEmail(),
                    AW_Pquestion2_Model_Source_Notification_Type::QUESTION_STATUS_CHANGE_TO_CUSTOMER,
                    $this->_getQuestionStatusChangeCustomerVariables($question),
                    $question->getStoreId()
                );
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function answerSaveAfter(Varien_Event_Observer $observer)
    {
        $answer = $observer->getEvent()->getAnswer();
        if ($answer->isObjectNew()) {
            $emailValidator = new Zend_Validate_EmailAddress;
            if (Mage::helper('aw_pq2/config')->getSendNewQuestionTo()
                && $emailValidator->isValid(Mage::helper('aw_pq2/config')->getSendNewQuestionTo())
            ) {
                try {
                    //new answer to admin
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        Mage::helper('aw_pq2')->__('Administrator'),
                        Mage::helper('aw_pq2/config')->getSendNewQuestionTo(),
                        AW_Pquestion2_Model_Source_Notification_Type::NEW_ANSWER_TO_ADMIN,
                        $this->_getNewAnswerAdminVariables($answer),
                        $answer->getQuestion()->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $_isCanNotify = Mage::helper('aw_pq2/notification')->isCanNotifyCustomer(
                $answer->getAuthorEmail(),
                AW_Pquestion2_Model_Source_Notification_Type::ANSWER_AUTO_RESPONDER
            );
            if ($_isCanNotify) {
                try {
                    //auto responder new answer to answer owner
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        $answer->getAuthorName(),
                        $answer->getAuthorEmail(),
                        AW_Pquestion2_Model_Source_Notification_Type::ANSWER_AUTO_RESPONDER,
                        $this->_getNewAnswerCustomerVariables($answer),
                        $answer->getQuestion()->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        if ($answer->getOrigData('status') != $answer->getStatus()) {
            $_isCanNotify = Mage::helper('aw_pq2/notification')->isCanNotifyCustomer(
                $answer->getQuestion()->getAuthorEmail(),
                AW_Pquestion2_Model_Source_Notification_Type::NEW_REPLY_ON_QUESTION_TO_CUSTOMER
            );
            if ($answer->getStatus() == AW_Pquestion2_Model_Source_Question_Status::APPROVED_VALUE && $_isCanNotify) {
                try {
                    //new reply on question to question owner
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        $answer->getQuestion()->getAuthorName(),
                        $answer->getQuestion()->getAuthorEmail(),
                        AW_Pquestion2_Model_Source_Notification_Type::NEW_REPLY_ON_QUESTION_TO_CUSTOMER,
                        $this->_getReplyOnQuestionCustomerVariables($answer),
                        $answer->getQuestion()->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            $_isCanNotify = Mage::helper('aw_pq2/notification')->isCanNotifyCustomer(
                $answer->getAuthorEmail(),
                AW_Pquestion2_Model_Source_Notification_Type::ANSWER_STATUS_CHANGE_TO_CUSTOMER
            );

            if ($_isCanNotify
                && (
                    ($answer->isObjectNew()
                        && $answer->getStatus() == AW_Pquestion2_Model_Source_Question_Status::APPROVED_VALUE
                    )
                    || !$answer->isObjectNew()
                )
            ) {
                try {
                    //answer status change notification to answer owner
                    Mage::getModel('aw_pq2/notification')->addToQueue(
                        $answer->getAuthorName(),
                        $answer->getAuthorEmail(),
                        AW_Pquestion2_Model_Source_Notification_Type::ANSWER_STATUS_CHANGE_TO_CUSTOMER,
                        $this->_getAnswerStatusChangeCustomerVariables($answer),
                        $answer->getQuestion()->getStoreId()
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    protected function _getNewQuestionAdminVariables(AW_Pquestion2_Model_Question $question)
    {
        return array(
            'product_url'             => $question->getProduct()->getProductUrl(),
            'product_name'            => Mage::helper('aw_pq2')->stripTags($question->getProduct()->getName()),
            'question_initiator_name' => $question->getAuthorName(),
            'question_initiator_email' => $question->getAuthorEmail(),
            'question_text'           => $question->getContent(),
            'backend_question_page'   => Mage::getSingleton('adminhtml/url')->getUrl(
                'aw_pq2_admin/adminhtml_question/edit',
                array('id' => $question->getId())
            )
        );
    }

    protected function _getNewAnswerAdminVariables(AW_Pquestion2_Model_Answer $answer)
    {
        return array_merge(
            array(
                'answer_text' => $answer->getContent()
            ),
            $this->_getNewQuestionAdminVariables($answer->getQuestion())
        );
    }

    protected function _getNewQuestionCustomerVariables(AW_Pquestion2_Model_Question $question)
    {
        return array_merge(
            array(
                'customer_name' => $question->getAuthorName(),
            ),
            $this->_getCommonVariables($question)
        );
    }

    protected function _getCommonVariables(AW_Pquestion2_Model_Question $question)
    {
        return array(
            'product_url'   => $question->getProduct()->getProductUrl(),
            'product_name'  => Mage::helper('aw_pq2')->stripTags($question->getProduct()->getName()),
            'question_text' => $question->getContent(),
        );
    }

    protected function _getNewAnswerCustomerVariables(AW_Pquestion2_Model_Answer $answer)
    {
        return array_merge(
            array(
                'answer_text'   => $answer->getContent(),
                'customer_name' => $answer->getAuthorName(),
                'moderate'      => Mage::helper('aw_pq2/config')->getRequireModerateCustomerAnswer(
                    $answer->getQuestion()->getStoreId()
                ),
            ),
            $this->_getCommonVariables($answer->getQuestion())
        );
    }

    protected function _getReplyOnQuestionCustomerVariables(AW_Pquestion2_Model_Answer $answer)
    {
        return array_merge(
            array(
                'answer_text'   => $answer->getContent(),
                'customer_name' => $answer->getQuestion()->getAuthorName(),
            ),
            $this->_getCommonVariables($answer->getQuestion())
        );
    }

    protected function _getQuestionStatusChangeCustomerVariables(AW_Pquestion2_Model_Question $question)
    {
        $statusLabel = Mage::getModel('aw_pq2/source_question_status')->getOptionByValue($question->getStatus());
        return array_merge(
            array(
                'new_question_status'   => $statusLabel
            ),
            $this->_getNewQuestionCustomerVariables($question)
        );
    }

    protected function _getAnswerStatusChangeCustomerVariables(AW_Pquestion2_Model_Answer $answer)
    {
        $statusLabel = Mage::getModel('aw_pq2/source_question_status')->getOptionByValue($answer->getStatus());
        $_variables = array_merge(
            array(
                'is_approved' => ($answer->getStatus() == AW_Pquestion2_Model_Source_Question_Status::APPROVED_VALUE),
                'is_registered'     => (bool)$answer->getCustomerId(),
                'new_answer_status' => $statusLabel,
                'customer_name' => $answer->getAuthorName(),
            ), Mage::helper('aw_pq2')->getPointsEmailVariables()
        );
        if (!$answer->getCustomerId()) {
            $_variables['points_amount'] = 0;
        }
        return array_merge($_variables, $this->_getNewAnswerCustomerVariables($answer));
    }
}