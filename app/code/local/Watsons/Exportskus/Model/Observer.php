<?php
set_time_limit(1600);
ini_set('memory_limit', '256M');

/**
 * Observer.php
 */
class Watsons_Exportskus_Model_Observer
{
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /**
     * 
     * @param type $observer
     */
    public function exportSkus($observer)
    {
        if(!Mage::helper('exportskus')->isEnabled()) {
            return;
        }
        $syncModel = Mage::getSingleton('exportskus/sync');
		$syncModel->exportSkusSync();
	}
}
