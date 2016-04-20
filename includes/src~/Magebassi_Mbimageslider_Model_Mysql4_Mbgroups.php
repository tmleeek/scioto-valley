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

class Magebassi_Mbimageslider_Model_Mysql4_Mbgroups extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {            
        $this->_init('mbimageslider/mbgroups', 'id'); // Here 'id' is primary key of table identifier 'mbslider'
    }
	
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
		$group_id = $object->getId();
		
		$storescollection = Mage::getModel('mbimageslider/mbgroupstores')->getCollection()->addFieldToFilter('group_id',$group_id);			
		$storesArray = array();
		foreach($storescollection as $key){						
			$storesArray[] = $key['store_id'];					
		}			
		$object->setData('store',$storesArray);		
		
		return parent::_afterLoad($object);
	}
	
	public function loadByField($field,$value){
        $table 		= $this->getMainTable();
        $where 		= $this->_getReadAdapter()->quoteInto("$field = ?", $value);
        $sql 		= $this->_getReadAdapter()->select()->from($table)->where($where);
        $htmlrow 	= $this->_getReadAdapter()->fetchRow($sql);
        return $htmlrow;
    }
}