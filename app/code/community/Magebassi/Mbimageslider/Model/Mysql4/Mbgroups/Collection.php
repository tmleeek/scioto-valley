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

class Magebassi_Mbimageslider_Model_Mysql4_Mbgroups_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mbimageslider/mbgroups');
    }
	
	
	/** Store Filter collection for Groups */
    public function addStoreFilter($store) {
        if (!Mage::app()->isSingleStoreMode()) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }
            $this->getSelect()
				->join(array('store_table' => Mage::getSingleton('core/resource')->getTableName('magebassi_mbgroupstores')), 'main_table.id = store_table.group_id', array())
                ->where('store_table.store_id in (?)', array(0, $store));
            return $this;
        }
        return $this;
    }
	
	
	// Group collection filter based on CMS Page Id
	public function addPageFilter($pageId) 
	{		
        $this->getSelect()
			->join(array('page_table' => Mage::getSingleton('core/resource')->getTableName('magebassi_mbcmspages')), 'main_table.id = page_table.group_id', array())
            ->where('page_table.page_id = ?', $pageId);
        return $this;
    }
	
	// Group collection filter based on Category Id
	public function addCategoryFilter($categoryId) {
        $this->getSelect()
			->join(array('category_table' => Mage::getSingleton('core/resource')->getTableName('magebassi_mbcategorypages')), 'main_table.id = category_table.group_id', array())
            ->where('category_table.category_ids = ?', $categoryId);
        return $this;
    }
	
	/** Group collection filter based on Product Id **/
	public function addProductFilter($productId) {
        $this->getSelect()
			->join(array('product_table' => Mage::getSingleton('core/resource')->getTableName('magebassi_mbproductpages')), 'main_table.id = product_table.group_id', array())
            ->where('product_table.product_ids = ?', $productId);
        return $this;
    }
}