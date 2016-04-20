<?php
/**
 * Data.php
 */
class Watsons_Exportorders_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Watsons_Exportorders_Model_Config 
     */
    protected $_config;
    
    /**
     * Is the module enabled?
     * 
     * @param   int
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_getConfig()->isEnabled();
    }
    
    /**
     * Log a message
     * 
     * @param   string
     */
    public function log($message)
    {
        $this->_getConfig()->log($message);
    }
    
    /**
     * Get the config
     * 
     * @return  Watsons_Exportorders_Model_Config
     */
    protected function _getConfig()
    {
        if($this->_config === NULL) {
            $this->_config = Mage::getModel('exportorders/config');
        }
        
        return $this->_config;
    }
}
	 