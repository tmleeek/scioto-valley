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
class AdjustWare_Nav_Block_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Block_Layer_Filter_Attribute
{
    protected $_featuredItems = array();
    protected $_optionUses    = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adjnav/filter/attribute.phtml');
        $this->_filterModelName = 'adjnav/catalog_layer_filter_attribute';
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

    public function getAttributeDisplayType()
    {
        return $this->_filter->getAttributeModel()->getAdjnavDisplayType();
    }

    public function getHtmlId($item)
    {
        return $this->getVar() . '-' . $item->getValueString();
    }

    public function isSelected($item)
    {
        $ids = (array)$this->_filter->getActiveState();
        return in_array($item->getValueString(), $ids);
    }

    public function getItemsArray()
    {

        $items                = array();
        $this->_featuredItems = array();
        $featuredValuesLimitDisabled = false;
        $featuredValuesLimit  = $this->helper('adjnav/featured')->getFeaturedValuesLimit();
        if($featuredValuesLimit == 0) {
            $featuredValuesLimitDisabled = true;
        }
        $iconsOnly            = (3 == $this->getColumnsNum());
        $baseUrl              = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $hideLinks            = Mage::getStoreConfig('design/adjnav/remove_links');

        foreach ($this->getItems() as $_item)
        {
			$htmlParams = 'id="' . $this->getHtmlId($_item) . '" ';

			$href = Mage::helper('adjnav')->trimBaseUrl();

			if (!$hideLinks)
			{
				$href .= $this->getRequestPath();

				$params = Mage::helper('adjnav')->getParams();

				if (isset($params[$this->getVar()]))
				{
					$values = explode('-', $params[$this->getVar()]);
					$valueKey = array_search($_item->getValueString(), $values);
                    if (false === $valueKey)
                    {
                        $values[] = $_item->getValueString();
                    }
                    else
                    {
                        unset($values[$valueKey]);
                    }
					$params[$this->getVar()] = implode('-', array_unique($values));
				}
				else
				{
					$params[$this->getVar()] = $_item->getValueString();
				}

				if ($params = http_build_query($params))
				{
					$href .= '?' . $params;
				}
			}

			$htmlParams .= 'href="' . $href . '" ';

            if ($iconsOnly){
                $htmlParams .= ' title="'.$this->escapeHtml($_item->getLabel()).'" class="adj-nav-icon '
                            . ($this->isSelected($_item) ? 'adj-nav-icon-selected' : '') . '" ';
            }
            else{
                $htmlParams .= 'class="adj-nav-attribute ';
                if ('default' == $this->getAttributeDisplayType()) {
                    $htmlParams .= ($featuredValuesLimitDisabled || $featuredValuesLimit > 0 ? '' : 'other ' );
                }
                $htmlParams .= ($this->isSelected($_item) ? 'adj-nav-attribute-selected' : '') . '" ';
            }

            $icon = '';
            if ($_item->getIcon()){
                $icon = '<img border="0" alt="'.$this->escapeHtml($_item->getLabel()).'" src="'.$baseUrl.'icons/'.$_item->getIcon().'" />';
            }

            $qty = '';
            if (!$this->getHideQty())
                $qty =  '(' .  $_item->getCount() .')';

            $label = $_item->getLabel();
            if ($iconsOnly){
                $label = '';
            }
            $label = $icon . $label;

            $items[]        = ($_item->getCount() > 0) ? '<a onclick="return false;" '.$htmlParams.'>'.$label.'</a>'.$qty : '<span class="adj-nav-attr-disabled">'.$label.'</span>'.$qty;
            $isFeaturedItem = false;
            if (($featuredValuesLimit > 0) || $featuredValuesLimitDisabled)
            {
                $isFeaturedItem = true;
                if (!$featuredValuesLimitDisabled)
                {
                    $featuredValuesLimit--;
                }
            }
            $this->_featuredItems[] = $isFeaturedItem;
        }

        return $items;
    }

    /**
     * Will return GET part of the request
     *
     *	@return string
     */
    public function getRequestPath()
    {
    	$request = Mage::app()->getRequest();

    	$requestPath = '';

    	if ($request->isXmlHttpRequest())
    	{
    		$requestPath = Mage::getSingleton('core/session')->getRequestPath();
    	}
    	else
    	{
    		Mage::getSingleton('core/session')->setRequestPath($requestPath = $request->getRequestString());
    	}

    	return $this->escapeHtml($requestPath);
    }

    public function getFeaturedItemStyle($key)
    {
        if (!empty($this->_featuredItems[$key]))
        {
            return 'attr-val-featured';
        }

        return 'attr-val-other';
    }

    public function isShowMoreButton()
    {
        $featuredValuesLimit = $this->helper('adjnav/featured')->getFeaturedValuesLimit();
        if ($featuredValuesLimit && $featuredValuesLimit < count($this->getItems()))
        {
            return true;
        }

        return false;
    }

    /** Implement custom sorting for items if configured
     *
     * @see Mage_Catalog_Model_Layer_Filter_Abstract::getItems()
     * @author ksenevich@aitoc.com
     */
    public function getItems()
    {
        $items = parent::getItems();

        $featuredLimit = Mage::helper('adjnav/featured')->getFeaturedValuesLimit();
        $featuredLimitDisabled = $featuredLimit == 0;
        if (!Mage::helper('adjnav/featured')->isRangeValues())
        {
            return $items;
        }

        $usesRanges  = array();
        $names       = array();
        $attributeId = $this->getAttributeModel()->getId();
        $optionUses  = Mage::getModel('adjnav/eav_entity_attribute_option_stat')->getSortedOptions($attributeId);

        foreach ($items as $k => $item)
        {
            $item->setSortRange(0);

            if (isset($optionUses[$item->getValueString()]))
            {
                $item->setSortRange($optionUses[$item->getValueString()]);
            }
        }

        usort($items, array($this, 'sortItems'));

        $featuredIndex = array();
        $names         = array();
        foreach ($items as $k => $item)
        {
            $item->setSortRange(0);

            if (($k < $featuredLimit) || $featuredLimitDisabled)
            {
                if ($featuredLimitDisabled)
                {
                    $item->setSortRange(1000000 - $k);
                }
                else
                {
                    $item->setSortRange($featuredLimit - $k);
                }
            }
        }

        usort($items, array($this, 'sortItems'));

        return $items;
    }

    public function getAttributeId()
    {
        return $this->_filter->getAttributeModel()->getId();
    }

    public function sortItems($item1, $item2)
    {
        if ($item1->getSortRange() == $item2->getSortRange())
        {//Zend_Debug::dump($item1->getLabel().' '.$item2->getLabel());
            return strcmp($item1->getLabel(), $item2->getLabel());
        }
//Zend_Debug::dump($item1->getLabel().' '.$item2->getLabel().' '.$item1->getSortRange().' '.$item2->getSortRange());
        return (($item1->getSortRange() < $item2->getSortRange()) ? 1 : -1);
    }
}