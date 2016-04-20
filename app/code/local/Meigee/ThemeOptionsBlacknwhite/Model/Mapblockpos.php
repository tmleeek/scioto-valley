<?php 
/**
 * Magento
 *
 * @author    Meigeeteam http://www.meaigeeteam.com <nick@meaigeeteam.com>
 * @copyright Copyright (C) 2010 - 2012 Meigeeteam
 *
 */
class Meigee_ThemeOptionsBlacknwhite_Model_Mapblockpos
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'left-top', 'label'=>Mage::helper('ThemeOptionsBlacknwhite')->__('Left Top')),
            array('value'=>'right-top', 'label'=>Mage::helper('ThemeOptionsBlacknwhite')->__('Right Top')),
			array('value'=>'left-bottom', 'label'=>Mage::helper('ThemeOptionsBlacknwhite')->__('Left Bottom')),
			array('value'=>'right-bottom', 'label'=>Mage::helper('ThemeOptionsBlacknwhite')->__('Right Bottom'))
        );
    }

}