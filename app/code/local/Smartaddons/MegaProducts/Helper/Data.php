<?php
/**PHP_COMMENT**/
class Smartaddons_MegaProducts_Helper_Data extends Mage_Core_Helper_Abstract {
public function __construct(){
		$this->defaults = array(
			'isenabled'		=> '1',
			'title' 		=> 'Mega Products II',
			
			/* product query */
			'product_category' 		=> array(),				// Select category
			'product_exclude' 		=> '',  				// Always exclude these products
			'product_order_by'		=> '',					// Sort list product
			'product_order_dir' 	=> '',					// ASC or DESC
			'product_limit_in_category' => '9',
			
			/* category options */
			'columns_max'					=> '3',
			'articles_max'					=> '9',
			'super_category_link'			=> '1',
			'super_category_link_target'	=> '_self',
			'sub_category_link'				=> '1',
			'sub_category_link_target'		=> '_self',
		
			'list_subcategory'				=> '1',
			'sub_category_title_maxchars'	=> '',
			
			/* product details */
			'product_title_disp'	=> '1',
			'product_title_linkable'=> '1',
			'product_title_maxchars'=> '',
			
			'product_image_disp'	=> '1',
			'product_image_linkable'=> '1',
				/* Thumbnail options */
			'product_thumbnail_width'	=> '199',
			'product_thumbnail_height'	=> '150',
			
			'product_short_description_disp' 		=> '1',
			'product_short_description_maxchars' 	=> '100',
			
			'product_details_page_link_disp' 		=> '1',
			'product_details_page_link_text' 		=> 'See details',
					
			'product_links_target'					=> '_self',
			
			'product_price_disp'	=> '1',
			'product_reviews_disp'	=> '1',
			'product_stock_disp'	=> '1',
			'product_created_disp'	=> '1',
		
			/* Tooltip options */
			'tooltip_disp'			=> '1',
			'tooltip_width'			=> '360',
			'tooltip_image_maxwidth'=> '120',
			
			/* Module option */
			'module_width' => '',
			'theme' => 'theme1'
		);
	}

	function get($attributes=array())
	{
		$data 						= $this->defaults;
		$general 					= Mage::getStoreConfig("megaproducts_cfg/general");
		$module_setting				= Mage::getStoreConfig("megaproducts_cfg/module_setting");
		$product_selection 			= Mage::getStoreConfig("megaproducts_cfg/product_selection");
		$category_setting 			= Mage::getStoreConfig("megaproducts_cfg/category_setting");
		$product_display_setting 	= Mage::getStoreConfig("megaproducts_cfg/product_display_setting");
		$advanced 					= Mage::getStoreConfig("megaproducts_cfg/advanced");
		if (!is_array($attributes)) {
			$attributes = array($attributes);
		}
		if (is_array($general))					$data = array_merge($data, $general);
		if (is_array($module_setting)) 			$data = array_merge($data, $module_setting);
		if (is_array($product_selection)) 		$data = array_merge($data, $product_selection);
		if (is_array($category_setting)) 		$data = array_merge($data, $category_setting);
		if (is_array($product_display_setting)) $data = array_merge($data, $product_display_setting);
		if (is_array($advanced)) 				$data = array_merge($data, $advanced);
		
		return array_merge($data, $attributes);;
	}
}
?>