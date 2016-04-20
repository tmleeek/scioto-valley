<?php
/**
 * Cron.php
 * 
 * Class to enable us to import via cron.
 */
class Watsons_Sync_Model_Cron 
{
    /**
     * Import CSV
     */
    public static function import()
    {
        $location = BP . DIRECTORY_SEPARATOR . 'var/import';
        $filePath   = $location . 'import_products.csv';
        
        if( ! file_exists($filePath)) {
            Mage::log("Cant find import file {$filePath}");
            exit;
        }

        ini_set('auto_detect_line_endings', true);

        $csvData= array();
        $fh     = fopen($filePath, 'r');
        while ($rowData = fgetcsv($fh)) { 
            $csvData[] = $rowData;    
        }
        fclose($fh);
        
        /**
         * Do we hard code this or have separate ones???
         */
        $type = 'prices';
        
        try {
            // handles the file upload and processing
            $syncModel = Mage::getModel('sync/sync');
            
            switch ($type) {
                case 'prices':
                    $syncModel->updateProductPrices($csvData);
                    break;
                case 'inventory':
                    $syncModel->updateProductInventory($csvData);
                    break;
                case 'ordernumbers':
                    $syncModel->updateRetailOrderNumbers($csvData);
                    break;
                default:
                    throw Mage::exception('Watsons_Sync', 'Invalid Type!');
                    break;
            }

            Mage::log(
                Mage::helper('sync')->__(
                    'File was successfully processed!'
                )
            );
        } 
        catch(Exception $e) {
            Mage::log($e, $e->getMessage());
        }
    }
}