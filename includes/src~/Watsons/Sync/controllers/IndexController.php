<?php
class Watsons_Sync_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        Watsons_Sync_Model_Observer::exportSkus();
    }
}