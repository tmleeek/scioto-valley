<?php

class Watsons_Sync_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Sync'), Mage::helper('adminhtml')->__('Sync'));

        $this->_redirect('*/adminhtml_order');
    }

    public function exportskusAction()
    {
        $syncModel = Mage::getSingleton('sync/sync');
        $syncModel->exportSkus();

        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('sync')->__(
                'Product Skus exported successfully!'
            )
        );

        $this->_redirect('sync/adminhtml_order/index');
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sync/adminhtml/index');
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
