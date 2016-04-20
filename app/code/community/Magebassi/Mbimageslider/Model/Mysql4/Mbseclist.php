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
 * File Type		: Model Resource File
 * 
 */
?>
<?php

class Magebassi_Mbimageslider_Model_Mysql4_Mbseclist extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('mbimageslider/mbseclist', 'id'); // Here 'id' is primary key of table identifier 'mbslider'
    }
	
	public function loadByField($field,$value){
        $table 		= $this->getMainTable();
        $where 		= $this->_getReadAdapter()->quoteInto("$field = ?", $value);
        $sql 		= $this->_getReadAdapter()->select()->from($table)->where($where);
        $htmlrow 	= $this->_getReadAdapter()->fetchRow($sql);
        return $htmlrow;
    }
	
	public function addGridPosition($collection,$group_id){
		$table2 = $this->getMainTable();
		$cond = $this->_getWriteAdapter()->quoteInto('e.entity_id = t2.selected_list','');
		$collection->getSelect()->joinLeft(array('t2'=>$table2), $cond);
		$collection->getSelect()->group('e.entity_id');
	}
}