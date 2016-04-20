<?php
/**
 * @category    AM
 * @package     AM_RevSlider
 * @copyright   Copyright (C) 2008-2013 ArexMage.com. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      ArexMage.com
 * @email       support@arexmage.com
 */
class AM_RevSlider_Block_Adminhtml_Widget_Grid_Column_Renderer_Slide_Delete extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => Mage::helper('revslider')->__('Delete'),
            'class'     => 'scala delete',
            'onclick'   => sprintf('confirmSetLocation(\'%s\', \'%s\')',
                Mage::helper('revslider')->__('Do you realy want to delete this slide?'),
                $this->getUrl('*/*/deleteSlide', array(
                    'sid'       => $row->getSliderId(),
                    'id'        => $row->getId(),
                    'activeTab' => 'slide_section'
                ))
            )
        ));

        return $button->toHtml();
    }
}