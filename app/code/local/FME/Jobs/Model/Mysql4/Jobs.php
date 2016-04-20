<?php


 /**
 * Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Jobs
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
class FME_Jobs_Model_Mysql4_Jobs extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the jobs_id refers to the key field in your database table.
        $this->_init('jobs/jobs', 'jobs_id');
    }
    
    public function checkIdentifier($identifier)
    {
	
        $select = $this->_getReadAdapter()->select()->from(array('main_table'=>$this->getMainTable()), 'jobs_id')
            ->where('main_table.jobs_url = ?', array($identifier))
            ->where('main_table.status = 1')
            ->order('main_table.jobs_id DESC');
						
        return $this->_getReadAdapter()->fetchOne($select);
    }
 
     protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
               
        $condition = $this->_getWriteAdapter()->quoteInto('jobs_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('job_store'), $condition);
        $arr = (array)$object->getData('stores');
      
        if(in_array('0', $arr))
    {
        $allStores = Mage::app()->getStores();
foreach ($allStores as $_eachStoreId => $val)
{
$_storeId[] = Mage::app()->getStore($_eachStoreId)->getId();

}
foreach ($_storeId as $store) {
            $storeArray = array();
            $storeArray['jobs_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('job_store'), $storeArray);
        }
    }else{
        foreach ((array)$object->getData('stores') as $store)
        {
         
            $storeArray = array();
            $storeArray['jobs_id'] = $object->getId();
            $storeArray['store_id'] = $store;
            $this->_getWriteAdapter()->insert($this->getTable('job_store'), $storeArray);
        }
        }
       
        return parent::_afterSave($object);
    }
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
       
        $selectStores = $this->_getReadAdapter()->select()
                                              ->from($this->getTable('job_store'))
                                              ->where('jobs_id = (?)', $object->getId());
                                        
        $storesData = $this->_getReadAdapter()->fetchAll($selectStores); // echo '<pre>'; print_r($storesData);exit; 
        if ($storesData)
        {
            $storeIds = array();
            foreach ($storesData as $_row)
            {
                $storeIds[] = $_row['store_id'];
            }
           
            $object->setData('stores', $storeIds);
        }
        
        return parent::_afterLoad($object);
    }
}