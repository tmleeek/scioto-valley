<?php
/**
 * Sync.php
 */
class Watsons_Exportorders_Model_Sync extends Mage_Core_Model_Abstract 
{
    const NUM_DECIMALS = 2;
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';

    /**
     * Export Orders
     * 
     * @param type $last_run
     * @param type $state
     * @return type
     */
    public function exportOrders($last_run = null, $state = null) 
    {
        Mage::helper('exportorders')->log('[STARTED] [exportOrders]');

        $varDir = Mage::getBaseDir('var');
        $exportDir = $varDir . DS . 'watsons_sync' . DS . 'orders';

        $symlinkFilePath = $exportDir . DS . 'orders.csv';
        $serverFileName = 'orders-' . date('Ymd-His') . '.csv';
        $exportFilePath = $exportDir . DS . $serverFileName;

        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $orderModel = Mage::getModel('sales/order');
        $customerModel = Mage::getModel('customer/customer');

        // When getting orders we maybe able to use $last_run to limit the export
        // to only new orders.  Although this requires some guarantee that the
        // export will always be process right after every export.
        $orderCollection = $orderModel->getCollection();

        /**
         * Filtering by state not what they expect?
         */
        $orderCollection->addFieldToFilter('state', array(
            'in' => array(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage_Sales_Model_Order::STATE_NEW,
            )
        ));

        if ($last_run != null) {
            $orderCollection->addFieldToFilter('created_at', array(
                'gt' => date('Y-m-d H:i:s', $last_run)
            ));
        }

        /**
         * export orders that do not have a retail id
         * 
         * @todo    possibly this is an issue as retail id is never actually
         * 
         */
        $stateModel = Mage::getSingleton('sync/state');
        $stringToUpper = new Zend_Filter_StringToUpper();
        $numericFilter = new Zend_Filter_Digits();

        $fp = fopen($exportFilePath, 'w');
        $orderCount = 0;
        foreach ($orderCollection as $order) {
            /* @var $order Mage_Sales_Model_Order */
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

            $shippingAddress = $order->getShippingAddress();
            $billingAddress = $order->getBillingAddress();
            $itemsCollection = $order->getItemsCollection();
            $paymentModel = $order->getPayment();

            if ($order->hasInvoices()) {
                $invoices = $order->getInvoiceCollection();
                foreach ($invoices as $invoice) {
                    if ($invoice->getTransactionId()) {
                        $paymentCCAproval = $invoice->getTransactionId();
                        break;
                    }
                }
            } 
            else {
                $paymentCCAproval = $paymentModel->getCcApproval();
            }

            $cards = $paymentModel->getAdditionalInformation('authorize_cards');
            if (is_array($cards)) {
                $card = array_shift($cards);
                if (isset($card['last_trans_id'])) {
                    $paymentCCAproval = $card['last_trans_id'];
                }
            }

            $totalWOTax = $order->getSubtotal();
            $itemCount = 0;
            foreach ($itemsCollection as $item) {
                $itemTaxRound = round($item->getTaxAmount(), self::NUM_DECIMALS);
                $itemQty = $item->getQtyOrdered();
                /**
                 * One Item per Item Ordered, possibly multiple items per SKU
                 */
                for ($i = 0; $i < $itemQty; $i++) {
                    /**
                     * Tax - Just stick all the tax against the first item
                     */
                    if ($i == 0 && $itemCount == 0) {
                        $lineTax = $order->getTaxAmount();
                    } 
                    else {
                        $lineTax = 0;
                    }
                    /**
                     * Discounts
                     */     
                    if($i == 0) {
                        // Just stick all the tax against the first item
                        $lineDisount = $item->getDiscountAmount();
                    } 
                    else {
                        $lineDisount = 0;
                    }

                    $line = array(
                        $order->getIncrementId()
                        , $customerId
                        , number_format($order->getGrandTotal(), self::NUM_DECIMALS, '.', '')
                        , number_format($lineTax, self::NUM_DECIMALS, '.', '')
                        , number_format($order->getShippingAmount(), self::NUM_DECIMALS, '.', '')
                        , $item->getSku()
                        , number_format('1', self::NUM_DECIMALS,'.','')
                        , number_format($item->getPrice() - $lineDisount, self::NUM_DECIMALS, '.', '') // take off discount too!
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
                $itemCount++;
            }

            // SKU 98080 basically tells the warehouse to ship the order
            $line = array(
                $order->getIncrementId()
                , $customerId
                , number_format($order->getGrandTotal(), self::NUM_DECIMALS, '.', '')
                , number_format('0', self::NUM_DECIMALS, '.', '') // We now add ALL tax to the first item
                , number_format($order->getShippingAmount(), self::NUM_DECIMALS, '.', '')
                , '98004' // SKU
                , number_format('1', self::NUM_DECIMALS, '.', '') // QTY
                , number_format($order->getShippingAmount(), self::NUM_DECIMALS, '.', '')
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
            $orderCount++;
        }
        fclose($fp);

        if (file_exists($symlinkFilePath)) {
            unlink($symlinkFilePath);
        }

        //symlink('.' . DS . basename($exportFilePath), $symlinkFilePath);
        copy($exportDir . DS . $serverFileName, $symlinkFilePath);
        
        Mage::helper('exportorders')->log('['.$orderCount.'] Orders Exported');
        Mage::helper('exportorders')->log('[ENDED] [exportOrders]');
        
        return $exportFilePath . '**' . $serverFileName;
    }

    /**
     * Ensures that a string is not null or empty.  Used for AS/400 integration
     * that treats empty strings as null.  Adds a '*' to make not empty
     *
     * @param unknown_type $value
     */
    protected function _ensureNotEmpty($value) 
    {
        if (empty($value)) {
            $value = '*';
        }

        return $value;
    }

    protected function _legacyIdToWebsiteCode($legacyId) 
    {
        switch ((int) $legacyId) {
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
