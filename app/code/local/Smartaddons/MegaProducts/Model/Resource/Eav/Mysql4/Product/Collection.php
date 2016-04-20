<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Model_Resource_Eav_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection{
	public function __construct(){
		parent::__construct();
	}
	public function addCategoriesFilter($categories){
		$alias = 'cats_index';
		$categoryCondition = $this->getConnection()->quoteInto(
			$alias.'.product_id=e.entity_id AND '. $alias .'.store_id=? AND ',
			$this->getStoreId()
		);

		$categoryCondition.= $alias.'.category_id IN ('. $categories. ')';

		$this->getSelect()->joinInner(
			array($alias => $this->getTable('catalog/category_product_index')),
			$categoryCondition,
			array('position'=>'position')
		);

		$this->_categoryIndexJoined = true;
		$this->_joinFields['position'] = array('table'=>$alias, 'field'=>'position' );

		return $this;

	}
}
