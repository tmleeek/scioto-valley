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


class AW_Pquestion2_Model_Notification_Cron
{
    const LIMIT = 25;

    public function sendQueue()
    {
        $queueCollection = Mage::getModel('aw_pq2/notification_queue')->getCollection();
        $queueCollection
            ->addFilterByPending()
            ->setPageSize(self::LIMIT)
        ;
        foreach ($queueCollection as $queue) {
            try {
                $queue->send();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function removeOldStoredEmails()
    {
        Mage::getResourceModel('aw_pq2/notification_queue')->removeOldStoredEmails();
        return $this;
    }
}