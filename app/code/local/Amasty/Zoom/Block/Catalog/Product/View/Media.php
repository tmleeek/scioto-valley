<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (http://www.amasty.com)
 * @package Amasty_Zoom
 */
class Amasty_Zoom_Block_Catalog_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{
    public function setTemplate($template)
    {
        $this->_template = $template;
        /*extension code. set to use our template*/
        if(Mage::getStoreConfig('amzoom/general/enable')) {
            $this->_template = 'amasty/amzoom/media.phtml';
        }

        return $this;
    }
}