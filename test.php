<?php

require_once("app/Mage.php");
Mage::app('default');

echo Mage::getBaseDir();

/*$productModel = Mage::getModel('catalog/product')->getCollection();
        //$productModel->addFieldToFilter('is_online_product', 1);
		//$productModel->addAttributeToFilter('is_online_product', 1);
        $count = 0;
        foreach($productModel as $_product) {
			//$product = Mage::getModel('catalog/product')->load($prod->getId());
            //if (preg_match('/^\d{4,6}$/', $_product->getSku()) > 0) {			
			if ($_product->getSku() !='') {			
			echo $_product->getSku();
			exit;
            //if (preg_match('/^[0-9]{4,7}$/', $_product->getSku()) > 0) {
               // fputcsv($fp, array($_product->getSku()));
                $count++;
            }
        }
		echo $count;*/
		
//		$productModel   = Mage::getModel('catalog/product');
//        $collection = $productModel->getCollection();
//        $collection->addAttributeToFilter('online_product', 1);
//        $count = 0;
//        foreach($collection as $_product) {
//            //if (preg_match('/^\d{4,6}$/', $_product->getSku()) > 0) {
//            if (preg_match('/^[0-9]{4,7}$/', $_product->getSku()) > 0) {
//				echo $_product->getSku();exit;
//               // fputcsv($fp, array($_product->getSku()));
//                $count++;
//            }
//        }
?>