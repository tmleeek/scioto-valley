<?php
/**
 * Sync.php
 */
class Watsons_Retailinvoice_Model_Sync extends Mage_Core_Model_Abstract
{
    const NUM_DECIMALS = 2;
    const SCOPE = 'default';
    const EXPORT_ORDERS_LAST_RUN_PATH = 'watsons/sync/export_orders/last_run';
    
    /**
     * 
     * @param type $data
     * @throws type
     */
    public function updateRetailOrderNumbers($data)
    {
        Mage::helper('retailinvoice')->log('[STARTED] [updateRetailOrderNumbers]');

        if (count(current($data)) != 2){
            throw Mage::exception(
                'Watsons_Sync'
                , 'Invalid data! Can not process.'
            );
        }
        
        foreach ($data as $row) {
            $orderModel = Mage::getModel('sales/order');
            /* @var $orderModel Mage_Sales_Model_Order */
            $retailOrderId  = trim($row[0]);
            $orderId        = trim($row[1]);
            // Not the right id??
            $orderModel->loadByIncrementId($orderId);
            //$orderModel->load($orderId);
            
            if ($orderModel->getId() > 0) {
                $origRetailId = $orderModel->getRetailId();
                if ($retailOrderId != $origRetailId) {
                    $orderModel->setRetailId($retailOrderId);
                    if ($origRetailId == ''){
                        /*if ($orderModel->canInvoice() && !$orderModel->hasInvoices()) {
                            $invoiceModel = $orderModel->prepareInvoice();
                            try {
                                $invoiceModel->register();
                            } 
                            catch(Exception $e) {
                                Mage::logException($e);
                            }
                            $invoiceModel->save();
                        }
                        if ($orderModel->canShip() && !$orderModel->hasShipments()) {
                            $shipment = $orderModel->prepareShipment();
                            $shipment->register();
                            $shipment->save();
                        }*/
                        
                        // Setting the state to complete is not needed anymore
                        // We do this above.
                        // Error here, not allow to manually do this..
                        // The Order State "complete" must not be set manually.
                        /*$orderModel->setState(
                            Mage_Sales_Model_Order::STATE_COMPLETE
                            , true
                            , $comment = ''
                            , $notify = false
                        );*/
                    }
                    try {
                        $orderModel->save();
                        Mage::helper('retailinvoice')->log('[UPDATED] ['.$orderId.'] => ['.$retailOrderId.']');
                    } 
                    catch (Exception $ex) {
                        Mage::logException($ex);
                        Mage::helper('retailinvoice')->log('Error with Order ['.$orderId.']: ' . $ex->getMessage());
                    }
                }
                else {
                    Mage::helper('retailinvoice')->log(
                       'Order ['.$orderId.']: Retail ID already set ['.$retailOrderId.']'
                    );
                }
            }
        }
        
        Mage::helper('retailinvoice')->log('[ENDED] [updateRetailOrderNumbers]');
    }
}
