<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Mysql4_Webforms_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function _construct(){
		parent::_construct();
		$this->_init('webforms/webforms');
	}
	
	protected function _afterLoad()
	{
		parent::_afterLoad();

		Mage::dispatchEvent('webforms_collection_after_load',array('collection'=>$this));

		return $this;
	}
	
	public function addRoleFilter($role_id){
		$this->getSelect()
			->join(array('admin_rule' => $this->getTable('admin/rule')),"admin_rule.resource_id = concat('admin/webforms/webform_',main_table.id)");

		$this->getSelect()
			->where("admin_rule.role_id = {$role_id}")
			->where("admin_rule.permission = 'allow'");

	}
	
}  
