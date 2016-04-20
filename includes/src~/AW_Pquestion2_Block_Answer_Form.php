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


class AW_Pquestion2_Block_Answer_Form extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getAddAnswerUrl()
    {
        return Mage::getUrl('aw_pq2/answer/add', array('_secure' => true));
    }

    /**
     * @return bool
     */
    public function isCanShowEmailAddress()
    {
        return !Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * @return array
     */
    public function getAllInfoMessages()
    {
        $list = array();
        if (Mage::helper('aw_pq2/config')->getRequireModerateCustomerAnswer()) {
            $list[] = $this->__('All answers will be displayed after moderation.');
        }
        return $list;
    }

    /**
     * @return int
     */
    public function getPointsForAnswer()
    {
        if (!Mage::helper('aw_pq2')->isModuleEnabled('AW_Points')
            || !Mage::getSingleton('customer/session')->isLoggedIn()
        ) {
            return 0;
        }
        return Mage::helper('points/config')->getPointsForAnsweringProductQuestion();
    }

}