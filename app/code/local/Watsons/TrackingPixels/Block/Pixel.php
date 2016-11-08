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
            case '/contact-us':
                /*($path == '');*/
                $mtId = 1089182;
                break;
            //Injects re-targeting math-pixel into the home-page page below the opening body tag
            case '/':
                $mtId = 1089183;
                break;
            //Injects re-targeting math-pixel into the home-page page below the opening body tag
            case '/current-sale':
                $mtId = 1089178;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/living-rooms page below the opening body tag
            case '/email_registration':
                $mtId = 1089180;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/fireplaces-and-gas-logs page below the opening body tag
            case '/financing':
                $mtId = 1089179;
                break;
            //Injects re-targeting math-pixel into the /indoor-entertaining/pool-tables-and-billiards page below the opening body tag
            case '/locations':
                $mtId = 1089175;
                break;
            //Injects re-targeting math-pixel into the /outdoor-entertaining page below the opening body tag
            case '/lowest-price':
                $mtId = 1089181;
                break;
            //Injects re-targeting math-pixel into the /financing page below the opening body tag
            case '/checkout/cart':
                $mtId = 1089176;
                break;


                }

        if( isset( $mtId ) ) {
            return '<script language=\'JavaScript1.1\'"src="//pixel.mathtag.com/event/js?mt_id=' . $mtId . '&mt_adid=158227&v1=&v2=&v3=&s1=&s2=&s3="></script>';
        }

        return '';

    }
}