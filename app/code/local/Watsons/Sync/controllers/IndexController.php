<?php
class Watsons_Sync_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $syncModel = Mage::getSingleton('sync/sync');
        $syncModel->exportSkus();
    }
}