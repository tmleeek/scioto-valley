<?php
set_time_limit(1600);
ini_set('memory_limit', '256M');

class Watsons_Sync_Model_Observer
{
    const SCOPE = 'default';

    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /***
     * Exports all orders that do not have a retail number.
     */
    public function exportOrders($observer) {
        $coreConfig = Mage::getModel('core/config');

        $last_run   = (int) Mage::getConfig()->getNode(
            self::EXPORT_ORDERS_LAST_RUN_PATH
            , self::SCOPE
        );

        $syncModel = Mage::getSingleton('sync/sync');
        $fileSplit = $syncModel->exportOrders();
		$fs = explode('**',$fileSplit);
		$local_file = $fs[0];
		$server_file = 'var/watsons_sync/orders/'.$fs[1];

        Mage::getConfig()->saveConfig(self::EXPORT_ORDERS_LAST_RUN_PATH, time());

    }

    public function exportSkus($observer) {
        $coreConfig = Mage::getModel('core/config');

        $exportPath  = Mage::getBaseDir('var') . DS . 'watsons_sync'
            . DS . 'mage-skus.csv';
        $ioFile     = new Varien_Io_File();

        $syncModel = Mage::getSingleton('sync/sync');
        $syncModel->exportSkus($exportPath);
	   }

    /**
     * Updates the product inventory and prices.
     *
     * @param $observer
     */
    public static function updateProductInventory($observer = NULL) 
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        Mage::log('Cron: Starting updateProductInventory', null, '', TRUE);
        
		$local_file = Mage::getBaseDir('var') . DS . 'watsons_sync' . DS . 'as400inv.csv';
		//$server_file = 'as400inv.csv';
		if(file_exists($local_file)){
            //echo "Product Inventory has been updated";
            ini_set('auto_detect_line_endings', true);
            $csvFile    = new Varien_File_Csv();
            $syncModel  = Mage::getSingleton('sync/sync');
            $syncModel->updateProductInventory($csvFile->getData($local_file));
            Mage::log('Product Inventory has been updated.', null, '', TRUE);
            unlink($local_file);
		}
        else {
            Mage::log('as400inv.csv Not found, aborting. ', null, '', TRUE);
        }

		/*if(ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
         $csvFilePath  = Mage::getBaseDir('var') . DS . 'watsons_sync'
             . DS . 'as400inv.csv';
         
         $ioFile     = new Varien_Io_File();
         
         if ($ioFile->fileExists($csvFilePath) ){
             ini_set('auto_detect_line_endings', true);
             $csvFile    = new Varien_File_Csv();
         
             $syncModel  = Mage::getSingleton('sync/sync');
             $syncModel->updateProductInventory($csvFile->getData($csvFilePath));
	     Mage::log('Product Inventory has been updated.');
         }
		}*/
    }

    public function updateRetailOrderNumbers($observer) {
        
        return;
        
        $csvFilePath  = Mage::getBaseDir('var') . DS . 'watsons_sync'
            . DS . 'orders' . DS . 'inv2web.csv';
		$server_file = 'var/watsons_sync/orders/inv2web.csv';
		if(file_exists($csvFilePath)){
		unlink($csvFilePath);
		}
		
		if (ftp_get($conn_id, $csvFilePath, $server_file, FTP_BINARY)) {
        $ioFile     = new Varien_Io_File();

         if ($ioFile->fileExists($csvFilePath) ){
             ini_set('auto_detect_line_endings', true);
             $csvFile    = new Varien_File_Csv();
         
             $syncModel  = Mage::getSingleton('sync/sync');
             $syncModel->updateRetailOrderNumbers($csvFile->getData($csvFilePath));
	     Mage::log('Retail Order Numbers have been updated');
         }
		}
    }
}
