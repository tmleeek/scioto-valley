<?php

/**
 * Sync.php
 */
class Watsons_Importinventory_Model_Sync extends Mage_Core_Model_Abstract {

    const NUM_DECIMALS                  = 2;
    const SCOPE                         = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH   = 'watsons/sync/export_orders/last_run';
    const OUT_OF_STOCK_LIMIT            = 5; // Qty where we set out of stock

    /**
     * Update the Inventory
     * 
     * @param type $data
     */
    public function updateProductInventory($data) 
    {
        Mage::helper('importinventory')->log('[STARTED] [updateProductInventory]');

        if (count(current($data)) != 5) {
            Mage::helper('importinventory')->log('Watsons_Sync Invalid data! Can not process.' . date('m-d-Y H:i:s'));
        }

        foreach ($data as $row) {
            $sku = trim($row[0]);
            $qty = (int) trim($row[1]);
            /**
             * We have a min Qty, if we are at or below we want to set out of stock.
             */
            if($qty <= self::OUT_OF_STOCK_LIMIT) {
                $qty = 0;
            }
            //$is_in_stock = $qty > 0 ? true : false;
            $special_price = trim($row[2]);
            $list_price = trim($row[3]);
            $web_price = trim($row[4]);
            $productModel = Mage::getModel('catalog/product');
            /* @var $productModel Mage_Catalog_Model_Product */
            $productId = $productModel->getIdBySku($sku);
            //$productId      = $productModel->reset()->getIdBySku($sku);
            if($productId > 0) {
                $productModel->load($productId);
                //$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productModel);               
                if ($web_price > $list_price) {
                    /**
                     * Skip no change
                     */
                    if($productModel->getQty() == $qty && $productModel->getPrice() == $web_price) {
                        Mage::helper('importinventory')->log($sku . " [unchanged] A {$productModel->getQty()}|{$productModel->getPrice()}");
                        //continue; // FORCE ALWAYS UPDATE
                    }
                    $productModel->setPrice($web_price);
                    $productModel->setSpecialPrice(null);
                } 
                else {
                    /**
                     * Skip no change
                     */
                    if($productModel->getQty() == $qty 
                       //&& $qty > self::OUT_OF_STOCK_LIMIT // Force update low stock - would this make a difference??
                       && $productModel->getPrice() == $list_price 
                       && $productModel->getSpecialPrice() == $special_price) {
                        Mage::helper('importinventory')->log($sku . " [unchanged] B {$productModel->getQty()}|{$productModel->getPrice()}");
                        //continue; // FORCE ALWAYS UPDATE
                    }
                    $productModel->setPrice($list_price);
                    $productModel->setSpecialPrice($special_price);
                }
//                $stockItem  = Mage::getModel('cataloginventory/stock_item')
//                    ->loadByProduct($productId);
//                $stockItem->setQty($qty);
//                $stockItem->setStockStatusChangedAutomaticallyFlag(true);
//                $stockItem->save();

                /**
                 * Less hungry, don't bother to load the stock data model!
                 */
                //$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
                $productModel->setStockData(array(
                    'is_in_stock'   => $qty > 0 ? 1 : 0,
                    'qty'           => $qty,
                    //'manage_stock'  => 1,
                ));
                
                try {
                    $productModel->save();
                } 
                catch (Exception $ex) {
                    //Mage::logException($ex);
                    Mage::helper('importinventory')->log('[Error] Saving product: ' . $productModel->getSku());
                    Mage::helper('importinventory')->log($ex->getMessage());
                }
                

                /*Mage::log(
                    "Saving product ID: {$productId} | " .
                    "new Price {$productModel->getPrice()} " .
                    "| new Qty {$qty}", null, 'updateproductinventory.log'
                );*/
                Mage::helper('importinventory')->log(
                    "Saving product ID: {$productId} | " .
                    "new Price {$productModel->getPrice()} " .
                    "| new Qty {$qty}"
                );
            } 
            else {
                Mage::helper('importinventory')->log('SKU NOT FOUND! ' . $sku);
            }
        }
        // $stockItemResource  = Mage::getModel('cataloginventory/stock')->getResource();
        // $stockItemResource->updateSetInStock();
        // $stockItemResource->updateSetOutOfStock();
        //Mage::getSingleton('cataloginventory/stock_status')->rebuild();
        Mage::helper('importinventory')->log('[ENDED] [updateProductInventory]');
    }
}
