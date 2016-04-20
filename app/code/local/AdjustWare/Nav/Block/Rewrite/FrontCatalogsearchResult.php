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
class AdjustWare_Nav_Block_Rewrite_FrontCatalogsearchResult extends Mage_CatalogSearch_Block_Result
{
    /**
     * Retrieve Search result list HTML output, wrapped with <div>
     *
     * @return string
     */
    public function getProductListHtml()
    {
        $html = parent::getProductListHtml();
        $html = Mage::helper('adjnav')->wrapProducts($html);
        return $html;
    }
    
    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */ 
    public function setListCollection()
    {
            $this->getListBlock()
               ->setCollection($this->_getProductCollection());
        return $this;
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) 
        {
            $this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if(!$this->getResultCount())
        {
            $html = Mage::helper('adjnav')->wrapProducts($html);
        }    
        return $html;
    }
}