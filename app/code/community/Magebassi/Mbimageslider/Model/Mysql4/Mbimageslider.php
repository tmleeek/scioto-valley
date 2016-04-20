<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Bannerslider
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Model_Mysql4_Mbimageslider extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('mbimageslider/mbimageslider', 'imageslider_id');
    }
	
	public function loadByField($field,$value){
        $table 		= $this->getMainTable();
        $where 		= $this->_getReadAdapter()->quoteInto("$field = ?", $value);
        $sql 		= $this->_getReadAdapter()->select()->from($table)->where($where);
        $htmlrow 	= $this->_getReadAdapter()->fetchRow($sql);
        return $htmlrow;
    }
}