<?php
set_time_limit(1600);
ini_set('memory_limit', '256M');

/**
 * Observer.php
 */
class Watsons_Exportorders_Model_Observer
{
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /***
     * Exports all orders that do not have a retail number.
     */
    public function exportOrders($observer) 
    {
        if( ! Mage::helper('exportorders')->isEnabled()) {
            return;
        }
        
        $coreConfig = Mage::getModel('core/config');
        $last_run   = (int) Mage::getConfig()->getNode(
            self::EXPORT_ORDERS_LAST_RUN_PATH
            , self::SCOPE
        );

        $syncModel = Mage::getSingleton('exportorders/sync');
        /* @var $syncModel Watsons_Exportorders_Model_Sync */
        $fileSplit = $syncModel->exportOrders();
		$fs = explode('**',$fileSplit);
		$local_file = isset($fs[0]) ? $fs[0] : NULL;
		$server_file = 'var/watsons_sync/orders/'.(isset($fs[1]) ? $fs[1] : ''); /** @todo check key eists.. **/

        Mage::getConfig()->saveConfig(self::EXPORT_ORDERS_LAST_RUN_PATH, time());

    }
}
