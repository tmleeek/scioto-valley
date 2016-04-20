<?php

class Watsons_Sync_Adminhtml_OrderController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
        if($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('sync');
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Watson\'s Sync')
            , Mage::helper('adminhtml')->__('Watson\'s Sync')
        );
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Order')
            , Mage::helper('adminhtml')->__('Order')
        );

        $this->_addContent(
            $this->getLayout()->createBlock(
                'sync/adminhtml_order'
            )
        );

        $this->renderLayout();
    }

    /**
     * Backup list action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock(
            'sync/adminhtml_order_grid')->toHtml()
        );
    }

    /**
     * Create backup action
     */
    public function createAction()
    {
        try {
            $coreConfig = Mage::getModel('core/config');
            $last_run   = (int) Mage::getConfig()->getNode(
                Watsons_Sync_Model_Sync::EXPORT_ORDERS_LAST_RUN_PATH
                , Watsons_Sync_Model_Sync::SCOPE
            );

            $syncModel = Mage::getSingleton('sync/sync');
            //$syncModel->exportOrders($last_run, Mage_Sales_Model_Order::STATE_PROCESSING);
            $syncModel->exportOrders();

            Mage::getConfig()->saveConfig(
                Watsons_Sync_Model_Sync::EXPORT_ORDERS_LAST_RUN_PATH
                , time()
            );

            $this->_getSession()->addSuccess(
                Mage::helper('adminhtml')->__('Exported orders successfully!')
            );
        }
        catch (Exception  $e) {
            $this->_getSession()->addException(
                $e
                , Mage::helper('adminhtml')->__(
                    'Error while creating order export. Please try again later.'
                )
            );
        }
        $this->_redirect('*/*');
    }

    /**
     * Download backup action
     */
    public function downloadAction()
    {
        $order = Mage::getModel('sync/order')
            ->setName($this->getRequest()->getParam('name'))
            ;
        /* @var $backup Mage_Backup_Model_Backup */

        if (!$order->exists()) {
            $this->_redirect('*/*');
        }

        $this->_prepareDownloadResponse(
            $order->getName()
            , null
            , 'text/csv'
            , $order->getSize()
        );

        $this->getResponse()->sendHeaders();

        $order->output();
        exit();
    }

    /**
     * Delete backup action
     */
    public function deleteAction()
    {
        try {
            $backup = Mage::getModel('sync/order')
                ->setName($this->getRequest()->getParam('name'))
                ->deleteFile();

            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Backup record was deleted'));
        }
        catch (Exception $e) {
            // Nothing
        }

        $this->_redirect('*/*/');

    }

    public function massDeleteAction()
    {
        $orderNames = $this->getRequest()->getParam('order');
        if (!is_array($orderNames)) {
            $this->_getSession()->addError($this->__('Please select order(s)'));
        }
        else {
            try {
                foreach ($orderNames as $orderName) {
                    $order = Mage::getSingleton('sync/order')->load($orderName);
                    $order->deleteFile();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($orderNames))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sync/adminhtml/order');
    }

    /**
     * Retrive adminhtml session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
