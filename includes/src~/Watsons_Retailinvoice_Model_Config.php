<?php
/**
 * Config.php
 */
class Watsons_Retailinvoice_Model_Config 
{	
    const XML_ENABLED       = 'retailinvoice_section/settings/enabled';
    const XML_LOG_ENABLED   = 'retailinvoice_section/settings/log_enabled';
    
    /**
     * Is the module enabled?
     * 
     * @param   int
     * @return bool
     */
    public function isEnabled($storeId = NULL)
    {
        return (int) Mage::getStoreConfig(self::XML_ENABLED, $storeId);
    }
    
    /**
     * Is the log enabled?
     * 
     * @param   int
     * @return bool
     */
    public function isLogEnabled($storeId = NULL)
    {
        return (int) Mage::getStoreConfig(self::XML_LOG_ENABLED, $storeId);
    }
    
    /**
     * Write a log entry it enabled.
     * 
     * @param   string
     * @param   int
     * @return bool
     */
    public function log($message, $storeId = NULL)
    {
        if( ! $this->isLogEnabled($storeId)) {
            return ;
        }
        
        Mage::log($message, null, 'retailinvoice.log');
    }
}