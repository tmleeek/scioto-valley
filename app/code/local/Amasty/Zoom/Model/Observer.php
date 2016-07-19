<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Zoom
 */
class Amasty_Zoom_Model_Observer
{
    public function onMediaHtmlAfter($observer)//core_block_abstract_to_html_after
    {
        if (!($observer->getBlock() instanceof Infortis_CloudZoom_Block_Product_View_Media && Mage::getStoreConfig('amzoom/general/enable'))) {
            return;
        }

        $html = $observer->getTransport()->getHtml();
        $html = Mage::app()->getLayout()->createBlock('amzoom/catalog_product_view_media', 'product.info.media')
                                        ->setTemplate("amasty/amzoom/media.phtml")
                                        ->toHtml();

        $observer->getTransport()->setHtml($html);
    }
}
