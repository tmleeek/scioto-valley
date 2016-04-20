<?php
/*------------------------------------------------------------------------
 # MegaProducts - Version 1.0
 # Copyright (C) 2011 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: The YouTech Company
 # Websites: http://smartaddons.com
 -------------------------------------------------------------------------*/

class Smartaddons_MegaProducts_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = null;
	protected $products_viewed = null;

	public function __construct($attributes = array()){
		parent::__construct();
		$this->_config = Mage::helper('megaproducts/data')->get($attributes);
	}

	public function getConfig($name=null, $value=null){
		if (is_null($this->_config)){
			$this->_config = Mage::helper('megaproducts/data')->get(null);
		}
		if (!is_null($name) && !empty($name)){
			$valueRet = isset($this->_config[$name]) ? $this->_config[$name] : $value;
			return $valueRet;
		}
		return $this->_config;
	}
	public function setConfig($name, $value=null){
		$config =& $this->getConfig();
		if (!empty($name)){
			$config[$name] = $value;
		}
	}
	
	protected function _toHtml(){
		$template = $this->getConfig('theme', 'theme1');
		$template_file = "smartaddons/megaproducts/default.phtml";
		$this->setTemplate($template_file);
		return parent::_toHtml();
	}
	/**
	 * prepare data, structure.
	 */
	protected function _getProducts(){
			
		if ($this->getConfig('product_category')==''){
			// if has no category selected.
			return array();
		}
		$storeId = Mage::app()->getStore()->getId();
		
		$category_collection = Mage::getModel('catalog/category')->getCollection();
		$category_collection->setStoreId($storeId);
		$category_collection->addIsActiveFilter();
		$category_collection->addAttributeToSelect('*');
		$category_collection->addIdFilter( $this->getConfig('product_category') );
		$category_collection->addAttributeToSort('position');
		
		// echo $category_collection->getSelect();
		$this->addReviewSummaryTemplate('smartaddons', 'smartaddons/megaproducts/summary.phtml');
		$list = array();
		$max_in_category = $this->getConfig('articles_max');

		// foreach ($category_collection as $category) {
			// if (($child_categories=$category->getChildrenCategories()) && $child_categories->count()>0){
				// $category_obj		= new stdClass();
				// $category_obj->id 		= $category->getId();
				// $category_obj->title 	= $category->getName();
				// $category_obj->url		= $category->getUrl();
				// $category_obj->child_category = array();
				
				// // get child categories
				// $child_categories->setLoadProductCount(true);
				// foreach ($child_categories as $child_category){
				foreach ($category_collection as $child_category){
					if ($child_category->getProductCount()){
						$child_category_obj 		= new stdClass();
						$child_category_obj->id 	= $child_category->getId();
						if (($maxchars_cat=$this->getConfig('sub_category_title_maxchars',-1))>0){
							$child_category_obj->title 	= Mage::helper('core/string')->truncate($child_category->getName(), $maxchars_cat);
						} else {
							$child_category_obj->title 	= $child_category->getName();
						}
						$child_category_obj->url 		= $child_category->getUrl();
						$child_category_obj->child 		= array();
						
						// category products
						$product_collection = $child_category->getProductCollection();
						$product_collection->addAttributeToSelect('*');
						$product_collection->addStoreFilter($storeId);
						
						// select active & visible in Catalog products
						Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($product_collection);
						Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($product_collection);
						
						// set limit for current category
						$product_collection->setPageSize($max_in_category);
						
						// order product
						$product_order_by = $this->getConfig('product_order_by');
						if (!empty($product_order_by)){
							$product_order_by  = strtolower($product_order_by);
							$product_order_dir = strtoupper($this->_config['product_order_dir']);
							switch($product_order_by){
								case 'random':
									$product_collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
									break;
								default:
									$product_collection->setOrder($product_order_by, $product_order_dir);
							}
						}
						// get Review summary
						Mage::getModel('review/review')->appendSummary($product_collection);
											
						foreach ($product_collection as $product){
							$product_obj 				= new stdClass();
							$product_obj->id 			= $product->getId();
							$product_obj->full_title 	= $product->getName();
							
							if (($maxchars=$this->getConfig('product_title_maxchars',-1))>0){
								$product_obj->title = Mage::helper('core/string')->truncate($product_obj->full_title, $maxchars);
							} else {
								$product_obj->title = $product_obj->full_title;
							}
							
							$product_obj->description = $product->getShortDescription();
							if (($maxchars=$this->getConfig('product_short_description_maxchars',-1))>0){
								$product_obj->description = Mage::helper('core/string')->truncate($product_obj->description, $maxchars, '...');
							}
							
							$product_obj->image = (string)Mage::helper('catalog/image')->init($product, 'image')->resize($this->getConfig('product_thumbnail_width'), $this->getConfig('product_thumbnail_height'));
							$product_obj->url = $product->getProductUrl();
							//$product_obj->hits = $this->getViewedCount($product_obj->id);
							if ($product->hasData('created_at')){
								$product_obj->createdfrom = Mage::helper('core')->formatDate($product->getData('created_at'));
								if ($product_obj->createdfrom==Mage::helper('core')->formatDate()){
									$product_obj->createdfrom =  Mage::helper('core')->formatTime($product->getData('created_at'));
								}
							} else {
								$product_obj->createdfrom = '';
							}
											
							$product_obj->stock_html = $product->isSaleable() ? $this->__('In stock') : $this->__('Out of stock');
							$product_obj->price_html = $this->getPriceHtml($product, true);
							$product_obj->review_html = $this->getReviewsSummaryHtml($product, 'smartaddons', true);
							// $product_obj->review_html = $this->getReviewsSummaryHtml($product,'short');
							
							//$product_obj->comments = $product->getRatingSummary()->getReviewsCount();
							$child_category_obj->child[$product_obj->id] = $product_obj;
						}
						// add to category child list.
						// $category_obj->child_category[$child_category_obj->id] = $child_category_obj;
						$list[$child_category_obj->id] = $child_category_obj;
					}
				}
				// store category
				// $list[$category_obj->id] = $category_obj;
			// }
		// }
		return $list;
	}

	public function getProducts(){
		return $this->_getProducts();
	}
	
	public function getConfigObject(){
		return (object)$this->getConfig();
	}
	
	public function getScriptTags(){
		$import_str = "";
		$jsHelper = Mage::helper('core/js');
		if (null == Mage::registry('jsmart.jquery')){
			// jquery has not added yet
			if (Mage::getStoreConfigFlag('megaproducts_cfg/advanced/include_jquery')){
				// if module allowed jquery.
				$import_str .= $jsHelper->includeSkinScript('smartaddons/megaproducts/js/jquery-1.5.min.js');
				Mage::register('jsmart.jquery', 1);
			}
		}
		if (null == Mage::registry('jsmart.jquerynoconfict')){
			// add once noConflict
			$import_str .= $jsHelper->includeSkinScript('smartaddons/megaproducts/js/jsmart.noconflict.js');
			Mage::register('jsmart.jquerynoconfict', 1);
		}
		
		// if (null == Mage::registry('jsmart.megaproductsjs')){
			// // add script for this module.
			// $import_str .= $jsHelper->includeSkinScript('smartaddons/megaproducts/js/jsmart.megaii-1.1.min.js');
			
			// Mage::register('jsmart.megaproductsjs', 1);
		// }
		
		return $import_str;
	}
}
