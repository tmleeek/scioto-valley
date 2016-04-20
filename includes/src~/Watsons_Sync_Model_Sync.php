<?php
class Watsons_Sync_Model_Sync extends Mage_Core_Model_Abstract
{
    const NUM_DECIMALS = 2;

    const SCOPE = 'default';

    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    public function exportOrders($last_run=null, $state=null) {
        Mage::log("Exporting orders ...", Zend_Log::DEBUG);

        $varDir     = Mage::getBaseDir('var');
        $exportDir  = $varDir . DS . 'watsons_sync' . DS . 'orders';

        $symlinkFilePath= $exportDir . DS . 'orders.csv';
 		$serverFileName = 'orders-' . date('Ymd-His') . '.csv';
        $exportFilePath = $exportDir . DS
            . $serverFileName;

        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $orderModel     = Mage::getModel('sales/order');
        $customerModel  = Mage::getModel('customer/customer');

        // When getting orders we maybe able to use $last_run to limit the export
        // to only new orders.  Although this requires some guarantee that the
        // export will always be process right after every export.
        $orderCollection = $orderModel->getCollection();

        if ($state != null) {
            $orderCollection->addFieldToFilter('state', $state);
        } else {
            // default to pending and processing orders
            $orderCollection->addFieldToFilter('state', array(
                'in' => array(
                    Mage_Sales_Model_Order::STATE_PROCESSING
                    , Mage_Sales_Model_Order::STATE_PENDING_PAYMENT
                    , Mage_Sales_Model_Order::STATE_NEW
                )
            ));

        }

        if ($last_run != null) {
            $orderCollection->addFieldToFilter('created_at', array(
                'gt' => date('Y-m-d H:i:s', $last_run)
            ));
        }

        // export orders that do not have a retail id
        $orderCollection->addAttributeToFilter(
            'retail_id'
            , array(array('null' => 'null'), array('eq' => '')) // DOES AN "OR"
            , 'left'
        );

//        $conn = $orderModel->getResource()->getReadConnection();
//        $conn->query('SET GLOBAL general_log = 1;');
//        $orderCollection->load();
//        $conn->query('SET GLOBAL general_log = 0;');


        $stateModel     = Mage::getSingleton('sync/state');
        $stringToUpper  = new Zend_Filter_StringToUpper();
        $numericFilter  = new Zend_Filter_Digits();
// $orderCollection->printlogquery(true);
// exit;
        $fp = fopen($exportFilePath, 'w');
        foreach ($orderCollection as $order) {
            // Load each order, otherwise can send emails as customer email
            // was not loaded as part of the collection, which is needed
            // for Mage_Sales_Model_Order::sendOrderUpdateEmail
            $order->load($order->getId());

            $customerModel->reset();

            if ($order->getCustomerIsGuest()) {
            	// GUEST
            	$customerId = 0;
            } else {
            	$customerModel->load($order->getCustomerId());
            	$customerId = $order->getCustomerId();
            }

            $shippingAddress    = $order->getShippingAddress();
            $billingAddress     = $order->getBillingAddress();
            $itemsCollection    = $order->getItemsCollection();
            $paymentModel       = $order->getPayment();
            
            if ($order->hasInvoices()) {

                $invoices           = $order->getInvoiceCollection();
                foreach ($invoices as $invoice) {
                    if ($invoice->getTransactionId()) {
                        $paymentCCAproval   = $invoice->getTransactionId();
                        break;
                    }
                }
            } else {
                $paymentCCAproval   = $paymentModel->getCcApproval();
            }
            
            $cards = $paymentModel->getAdditionalInformation('authorize_cards');
            if(is_array($cards)) {
                $card = array_shift($cards);
                if(isset($card['last_trans_id'])) {
                    $paymentCCAproval =  $card['last_trans_id'];
                }
            } 

            $totalWOTax         = $order->getSubtotal(); // - $order->getTaxAmount();

            foreach($itemsCollection as $item) {
                $itemTaxRound= round($item->getTaxAmount(), self::NUM_DECIMALS);
                $itemQty = $item->getQtyOrdered();
                /**
                 * One Item per Item Ordered, possibly multiple items per SKU
                 */
                for($i = 0; $i < $itemQty; $i++) {
                    /**
                     * @todo    check if Discount is greater than price of a single item
                     *          if so we may need to split it over multiple items to avoid negative price.
                     */
                    //if($lineDisount > $item->getPrice()) {
                        // split over multiple lines.
                    //}
                    
                    if($i == 0) {
                       $lineDisount = $item->getDiscountAmount();
                    }
                    else {
                        $lineDisount = 0;
                    }
                    
                    $line = array(
                        $order->getIncrementId()
                        , $customerId
                        , number_format($order->getGrandTotal(), self::NUM_DECIMALS,'.','')
                        , number_format($itemTaxRound, self::NUM_DECIMALS,'.','')
                        , number_format($order->getShippingAmount(), self::NUM_DECIMALS,'.','')
                        , $item->getSku()
                        , 1 //number_format($item->getQtyOrdered(), 0,'.','')
                        , number_format($item->getPrice() - $lineDisount, self::NUM_DECIMALS,'.','') // take off discount too!
                        , $order->getCustomerEmail()
                        , $stringToUpper->filter($billingAddress->getFirstname())
                        , $stringToUpper->filter($billingAddress->getLastname())
                        , $stringToUpper->filter($billingAddress->getStreet1())
                        , $this->_ensureNotEmpty(
                            $stringToUpper->filter($billingAddress->getStreet2())
                        )
                        , $stringToUpper->filter($billingAddress->getCity())
                        , $stringToUpper->filter(
                            $stateModel->toAbbr($billingAddress->getRegion())
                        )
                        , $billingAddress->getPostcode()
                        , $numericFilter->filter($billingAddress->getTelephone())
                        , $stringToUpper->filter($shippingAddress->getFirstname())
                        , $stringToUpper->filter($shippingAddress->getLastname())
                        , $numericFilter->filter($shippingAddress->getTelephone())
                        , $stringToUpper->filter($shippingAddress->getStreet1())
                        , $this->_ensureNotEmpty(
                            $stringToUpper->filter($shippingAddress->getStreet2())
                        )
                        , $stringToUpper->filter($shippingAddress->getCity())
                        , $stringToUpper->filter(
                            $stateModel->toAbbr($shippingAddress->getRegion())
                        )
                        , $shippingAddress->getPostcode()
                        , $shippingAddress->getCountryId()
                        , $paymentCCAproval
                    );
                    fputcsv($fp, $line);
                }
            }

            // SKU 98080 basically tells the warehouse to ship the order
            $line = array(
                $order->getIncrementId()
                , $customerId
                , number_format($order->getGrandTotal(), self::NUM_DECIMALS,'.','')
                , number_format($order->getShippingTaxAmount(), self::NUM_DECIMALS,'.','')
                , number_format($order->getShippingAmount(), self::NUM_DECIMALS,'.','')
                , '98004' // SKU
                , number_format('1', 0) // QTY
                , number_format($order->getShippingAmount(), self::NUM_DECIMALS,'.','')
                , $order->getCustomerEmail()
                , $stringToUpper->filter($billingAddress->getFirstname())
                , $stringToUpper->filter($billingAddress->getLastname())
                , $stringToUpper->filter($billingAddress->getStreet1())
                , $this->_ensureNotEmpty(
                    $stringToUpper->filter($billingAddress->getStreet2())
                )
                , $stringToUpper->filter($billingAddress->getCity())
                , $stringToUpper->filter(
                    $stateModel->toAbbr($billingAddress->getRegion())
                )
                , $billingAddress->getPostcode()
                , $numericFilter->filter($billingAddress->getTelephone())
                , $stringToUpper->filter($shippingAddress->getFirstname())
                , $stringToUpper->filter($shippingAddress->getLastname())
                , $numericFilter->filter($shippingAddress->getTelephone())
                , $stringToUpper->filter($shippingAddress->getStreet1())
                , $this->_ensureNotEmpty(
                    $stringToUpper->filter($shippingAddress->getStreet2())
                )
                , $stringToUpper->filter($shippingAddress->getCity())
                , $stringToUpper->filter(
                    $stateModel->toAbbr($shippingAddress->getRegion())
                )
                , $shippingAddress->getPostcode()
                , $shippingAddress->getCountryId()
                , $paymentCCAproval
            );

            fputcsv($fp, $line);
        }
        fclose($fp);

         if (file_exists($symlinkFilePath)) {
             unlink($symlinkFilePath);
         }
         
         symlink('.' . DS . basename($exportFilePath), $symlinkFilePath);

        return $exportFilePath. '**' .$serverFileName;
    }

    /**
     * Ensures that a string is not null or empty.  Used for AS/400 integration
     * that treats empty strings as null.  Adds a '*' to make not empty
     *
     * @param unknown_type $value
     */
    protected function _ensureNotEmpty($value) {

        if (empty($value)) {
            $value = '*';
        }

        return $value;
    }

    public function exportSkus($exportPath) {
        $productModel   = Mage::getModel('catalog/product');
        $resource       = $productModel->getResource();

        $readConn  = Mage::getSingleton('core/resource') ->getConnection('core_read');

        $select = "SELECT sku FROM {$resource->getTable('catalog_product_entity')} ORDER BY sku";

        $results = $readConn->fetchAll($select);

        $fp = fopen($exportPath, 'w+');
        foreach ($results as $row) {
            if (preg_match('/^\d{4,5}$/', $row['sku']) > 0) {
                fputcsv($fp, array($row['sku']));
            }
        }
        fclose($fp);

        return $this;
    }

    public function updateProductInventory($data)
    {
	// echo print_r($data);exit;
        if (count(current($data)) != 5){
             Mage::log('Watsons_Sync Invalid data! Can not process.'.date('m-d-Y H:i:s'));
        }
        $productModel   = Mage::getModel('catalog/product');
        /* @var $productModel Mage_Catalog_Model_Product */

        foreach ($data as $row) {
            $sku            = trim($row[0]);
            $qty            = (int)trim($row[1]);
			$is_in_stock    = $qty > 0 ? true : false;
            $special_price  = trim($row[2]);
            $list_price     = trim($row[3]);
            $web_price      = trim($row[4]);
// echo $web_price;exit;
            $productId      = $productModel->reset()->getIdBySku($sku);
            //$productId      = $productModel->getIdBySku($sku);
// echo $productId;exit;
            if ($productId > 0) {
                $productModel->load($productId);
				if($web_price > $list_price){
                    $productModel->setPrice($web_price);	
                    $productModel->setSpecialPrice(null);	
				} else {
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
                $productModel->setStockData(array(
                    'is_in_stock'   => $qty > 0 ? 1 : 0,
                    'qty'           => $qty,
                    //'manage_stock'  => 1,
                ));
    
                $productModel->save();
                
                //echo "Saving product ID: {$productId}<br />";
                Mage::log(
                    "Saving product ID: {$productId} | ".
                    "new Price {$productModel->getPrice()} ".
                    "| new Qty {$qty}", null, 'updateproductinventory.log');
            } 
            else {
                Mage::log('SKU NOT FOUND! '.$sku, null, 'updateproductinventory.log');
            }
        }
        // $stockItemResource  = Mage::getModel('cataloginventory/stock')->getResource();
        // $stockItemResource->updateSetInStock();
        // $stockItemResource->updateSetOutOfStock();
        //Mage::getSingleton('cataloginventory/stock_status')->rebuild();
    }

    public function updateRetailOrderNumbers($data)
    {
        if (count(current($data)) != 2){
            throw Mage::exception(
                'Watsons_Sync'
                , 'Invalid data! Can not process.'
            );
        }

        $orderModel = Mage::getModel('sales/order');
        foreach ($data as $row) {
            $retailOrderId  = trim($row[0]);
            $orderId        = trim($row[1]);

            $orderModel->reset()->loadByIncrementId($orderId);
            if ($orderModel->getId() > 0) {
                $origRetailId = $orderModel->getRetailId();
                if ($retailOrderId != $origRetailId) {
                    $orderModel->setRetailId($retailOrderId);

                    if ($origRetailId == ''){
                        if ($orderModel->canInvoice() && !$orderModel->hasInvoices()) {
                            $invoiceModel = $orderModel->prepareInvoice();
                            try {
                                $invoiceModel->register();
                            } catch(Exception $e) {
                                Mage::logException($e);
                            }
                            $invoiceModel->save();
                        }
                        if ($orderModel->canShip() && !$orderModel->hasShipments()) {
                            $shipment = $orderModel->prepareShipment();
                            $shipment->register();
                            $shipment->save();
                        }
                        // Setting the state to complete is not needed anymore
                        // We do this above.
                        $orderModel->setState(
                            Mage_Sales_Model_Order::STATE_COMPLETE
                            , true
                            , $comment = ''
                            , $notify = false
                        );
                    }
                    $orderModel->save();
                }
            }
        }
    }

    protected function _legacyIdToWebsiteCode($legacyId) {

        switch ((int)$legacyId) {
            case 8:
                $websiteCode = 'clover';
                break;
            case 7:
                $websiteCode = 'macksoods';
                break;
            case 6:
                $websiteCode = 'sciotovalley';
                break;
            case 5:
                $websiteCode = 'grandrapids';
                break;
            case 4:
                $websiteCode = 'stlouis';
                break;
            case 3:
                $websiteCode = 'louisville';
                break;
            case 2:
                $websiteCode = 'dayton';
                break;
            case 1:
            default:
                $websiteCode = 'cincinnati';
                break;
        }
        return $websiteCode;
    }
}
