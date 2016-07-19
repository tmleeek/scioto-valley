<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2015 Amasty (http://www.amasty.com)
* @package Amasty_Zoom
*/
class Amasty_Zoom_Block_Grid extends Mage_Core_Block_Template
{
    public function __construct()
    {
        if(Mage::getStoreConfig('amzoom/zoom_on_category_grid/enable')) {
            parent::__construct();
            $this->setTemplate('amasty/amzoom/grid.phtml');
        }
    }
    
    public function getProductInformation()
    {
        $filteredList = $this->getLayout()
            ->getBlockSingleton('catalog/product_list')
            ->getLoadedProductCollection();

        $data = array();

        foreach($filteredList as $product) {
            $orig_image = (string)Mage::helper('catalog/image')->init($product, 'small_image');
            $grid_image = substr($orig_image, strrpos($orig_image, "/"));
            $data[] = array(
                'id'            => $product->getId(),
                'orig_image'    => $orig_image,
                'grid_image'    => $grid_image,
            );
        }
        return Zend_Json::encode($data);
    }

    public function getSetting()
    {
        $imageSizeAtCategoryPageX = Mage::getStoreConfig('amzoom/zoom_on_category_grid/main_image_list_size_x');
        $imageSizeAtCategoryPageY = Mage::getStoreConfig('amzoom/zoom_on_category_grid/main_image_list_size_y');
        $data[] = array(
            'zoomWindowFadeIn'            => 500,
            'zoomWindowFadeOut'           => 500,
            'lensFadeIn'                  => 500,
            'lensFadeOut'                 => 500,
            'responsive'                  => 'true',
            'zoomWindowWidth'             => (int)Mage::getStoreConfig('amzoom/zoom_on_category_grid/viewer_width'),
            'zoomWindowHeight'            => (int)Mage::getStoreConfig('amzoom/zoom_on_category_grid/viewer_height'),
            'zoomWindowOffetx'            => (int)Mage::getStoreConfig('amzoom/zoom_on_category_grid/viewer_margin'),
            'loadingIcon'                 => Mage::getDesign()->getSkinUrl('amasty/amzoom/images/preloaderSmall.gif',array('_area'=>'frontend')),
        );

        return Zend_Json::encode($data);
    }
}