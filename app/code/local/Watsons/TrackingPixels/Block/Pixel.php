<?php


class Watsons_TrackingPixels_Block_Pixel extends Mage_Core_Block_Template
{
    //Gets current page and injects the tracking script for that page

    public function getMathTagPixel()
    {
        // Gets current page
        $urlString = Mage::helper('core/url')->getCurrentUrl();
        $url       = Mage::getSingleton('core/url')->parseUrl($urlString);
        $path      = $url->getPath();



        //Injects re-targeting math-pixel into pages

        switch ($path){
            //Injects re-targeting math-pixel into the /pools-and-spas/above-ground-pools page below the opening body tag
            case '/pools-and-spas/above-ground-pools':
                /*($path == '');*/
                $mtId = 932713;
                break;
            //Injects re-targeting math-pixel into the home-page page below the opening body tag
            case '/':
                $mtId = 932712;
                break;
            //Injects re-targeting math-pixel into the home-page page below the opening body tag
            case '/pools-and-spas/hot-tubs-spas':
                $mtId = 932714;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining page below the opening body tag
            case '/indoor-entertaining':
                $mtId = 932715;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/living-rooms page below the opening body tag
            case '/indoor-entertaining/living-rooms':
                $mtId = 932716;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/fireplaces-and-gas-logs page below the opening body tag
            case '/indoor-entertaining/fireplaces-and-gas-logs':
                $mtId = 932717;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/pool-tables-and-billiards page below the opening body tag
            case '/indoor-entertaining/pool-tables-and-billiards':
                $mtId = 932718;
                break;
            //Injects re-targeting math-pixel into the /outdoor-entertaining page below the opening body tag
            case '/outdoor-entertaining':
                $mtId = 932719;
                break;
            //Injects re-targeting math-pixel into the /financing page below the opening body tag
            case '/financing':
                $mtId = 932720;
                break;


                }

        if( isset( $mtId ) ) {
            return '<script language=\'JavaScript1.1\'"src="//pixel.mathtag.com/event/js?mt_id=' . $mtId . '&mt_adid=158227&v1=&v2=&v3=&s1=&s2=&s3="></script>';
        }

        return '';

    }
}