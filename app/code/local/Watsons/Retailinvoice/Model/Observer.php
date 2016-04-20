<?php
set_time_limit(1600);
ini_set('memory_limit', '256M');

/**
 * Observer.php
 */
class Watsons_Retailinvoice_Model_Observer
{
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /**
     * 
     * @param type $observer
     * @return type
     */
    public function updateRetailOrderNumbers($observer) 
    {
        if( ! Mage::helper('retailinvoice')->isEnabled()) {
            return;
        }
        
        Mage::helper('retailinvoice')->log('[CRON] [Starting updateRetailOrderNumbers]');
        
        $csvFilePath = Mage::getBaseDir('var') . DS . 'watsons_sync'
                . DS . 'orders' . DS . 'inv2web.csv';
        
        $ioFile = new Varien_Io_File();
        if ($ioFile->fileExists($csvFilePath)) {
            ini_set('auto_detect_line_endings', true);
            $csvFile = new Varien_File_Csv();
            $syncModel = Mage::getSingleton('retailinvoice/sync');
            $dataArray = $csvFile->getData($csvFilePath);
            $syncModel->updateRetailOrderNumbers($dataArray);
            Mage::helper('retailinvoice')->log('['.count($dataArray).'] Retail Order Numbers have been updated');
        }
        else {
            Mage::helper('retailinvoice')->log('File NOT exists ['.$csvFilePath.']');
        }
        
        Mage::helper('retailinvoice')->log('[CRON] [END updateRetailOrderNumbers]');
    }
}
