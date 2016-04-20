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
/**
 * 
 * @author ksenevich
 */
class AdjustWare_Nav_Model_Mysql4_Eav_Entity_Attribute_Stat extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('adjnav/eav_attribute_stat', 'attribute_id');
    }
}