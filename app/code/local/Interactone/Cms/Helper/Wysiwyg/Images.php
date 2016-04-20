<?php

/**
 * Fixes path for wysiwyg thumbnails
 */
class Interactone_Cms_Helper_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
    /**
     * Images Storage base URL
     *
     * Override as Wysiwyg directory is not included in core code
     * @return string
     */
    public function getBaseUrl()
    {
        return Mage::getBaseUrl('media') . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY . '/';
    }
}