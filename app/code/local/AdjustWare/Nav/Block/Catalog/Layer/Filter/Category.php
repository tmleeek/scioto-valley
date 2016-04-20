<?php
/**
 * Layered Navigation Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Nav
 * @version      2.5.4
 * @license:     yc4tx3fdyujjEs5czyndvhoc8zpLrKl3OCuGehtGvM
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter/category.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_category'; 
    }
   
    public function getVar(){
        return $this->_filter->getRequestVar();
    }

    public function getClearUrl()
    {
        $url = '';
        $query = Mage::helper('adjnav')->getParams();
        if (!empty($query[$this->getVar()])){
            $query[$this->getVar()] = null;
            $url = Mage::getUrl('*/*/*', array(
                '_use_rewrite' => true, 
                '_query'       => $query,
             )); 
        }
        
        return $url;
    }
}