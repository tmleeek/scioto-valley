<?php

class Hm_Testimonial_Model_Mysql4_Testimonial extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the testimonial_id refers to the key field in your database table.
        $this->_init('testimonial/testimonial', 'testimonial_id');
    }
	protected function _afterSave(Mage_Core_Model_Abstract $object){
		$condition = $this->_getWriteAdapter()->quoteInto('testimonial_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('testimonial_store'), $condition);

		if (!$object->getData('stores'))
		{
			$storeArray = array();
            $storeArray['testimonial_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('testimonial_store'), $storeArray);
		}
		else
		{
			foreach ((array)$object->getData('stores') as $store) {
				$storeArray = array();
				$storeArray['testimonial_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('testimonial_store'), $storeArray);
			}
		}

        return parent::_afterSave($object);
    }
    protected function _beforeDelete(Mage_Core_Model_Abstract $object){
		
		// Cleanup stats on blog delete
		$adapter = $this->_getReadAdapter();
		// 1. Delete testimonial/store
		$adapter->delete($this->getTable('testimonial/testimonial_store'), 'testimonial_id='.$object->getId());

	}        
	
    public function getTestimonialStore($id){
    	$arr = $this->getTable('testimonial_store','testimonial_id=1');
    	return "saflajdfklda";
    }
    
}