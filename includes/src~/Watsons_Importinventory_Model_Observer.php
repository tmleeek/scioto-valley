<?php
set_time_limit(1600);
ini_set('memory_limit', '256M');

/**
 * Observer.php
 */
class Watsons_Importinventory_Model_Observer
{
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /**
     * Updates the product inventory and prices.
     *
     * @param $observer
     */
    public static function updateProductInventory($observer = NULL) 
    {
        if( ! Mage::helper('importinventory')->isEnabled()) {
            return;
        }
        
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        Mage::helper('importinventory')->log('[CRON] [Starting updateProductInventory]');
		$local_file = Mage::getBaseDir('var') . DS . 'watsons_sync' . DS . 'as400inv.csv';
		//$server_file = 'as400inv.csv';
		if(file_exists($local_file)){
            //echo "Product Inventory has been updated";
            ini_set('auto_detect_line_endings', true);
            $csvFile    = new Varien_File_Csv();
            $syncModel  = Mage::getSingleton('importinventory/sync');
            /* @var $syncModel Watsons_Importinventory_Model_Sync */
            $productData = $csvFile->getData($local_file);
            $syncModel->updateProductInventory($productData);
            Mage::helper('importinventory')->log('Product Inventory has been updated. ['.count($productData).' products]');
            unlink($local_file);
		}
        else {
            Mage::helper('importinventory')->log('as400inv.csv Not found, aborting. ');
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
}
