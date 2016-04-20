<?php
/**
 * Sync.php
 */
class Watsons_Exportskus_Model_Sync extends Mage_Core_Model_Abstract
{
    const NUM_DECIMALS = 2;
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /**
     * Export Skus
     * 
     * @param type $exportPath
     * @return \Watsons_Exportskus_Model_Sync
     */
    public function exportSkus($exportPath) 
    {
        Mage::helper('exportskus')->log('[STARTED] [exportSkus]');
        
        $fp = fopen($exportPath, 'w+'); // trucnate too
        $productModel   = Mage::getModel('catalog/product');
        $collection = $productModel->getCollection();
        $collection->addFieldToFilter('is_online_product', 1);
        $count = 0;
        foreach($collection as $_product) {
            //if (preg_match('/^\d{4,6}$/', $_product->getSku()) > 0) {
            if (preg_match('/^[0-9]{4,7}$/', $_product->getSku()) > 0) {
                fputcsv($fp, array($_product->getSku()));
                $count++;
            }
        }
        fclose($fp);

        Mage::helper('exportskus')->log('['.$count.'] Skus Exported');
        Mage::helper('exportskus')->log('[ENDED] [exportSkus]');
        
        return $this;
    }
    
    /**
     * Ensures that a string is not null or empty.  Used for AS/400 integration
     * that treats empty strings as null.  Adds a '*' to make not empty
     *
     * @param unknown_type $value
     */
    protected function _ensureNotEmpty($value) 
    {
        if (empty($value)) {
            $value = '*';
        }

        return $value;
    }

    protected function _legacyIdToWebsiteCode($legacyId)
    {
        switch ((int)$legacyId) {
            case 8:
                $websiteCode = 'clover';
                break;
            case 7:
                $websiteCode = 'macksoods';
                break;
            case 6:
                $websiteCode = 'sciotovalley';
                break;
            case 5:
                $websiteCode = 'grandrapids';
                break;
            case 4:
                $websiteCode = 'stlouis';
                break;
            case 3:
                $websiteCode = 'louisville';
                break;
            case 2:
                $websiteCode = 'dayton';
                break;
            case 1:
            default:
                $websiteCode = 'cincinnati';
                break;
        }
        return $websiteCode;
    }
}
