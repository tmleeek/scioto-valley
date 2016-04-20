<?php

class Watsons_Exportskus_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
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

        $this->_redirect('exportorders/adminhtml_order');
    }

    /**
     * Export Skus
     */
    public function exportskusAction() 
    {
        $coreConfig = Mage::getModel('core/config');

        $exportPath  = Mage::getBaseDir('var') . DS . 'watsons_sync'
            . DS . 'mage-skus.csv';
        $ioFile     = new Varien_Io_File();

        $syncModel = Mage::getSingleton('exportskus/sync');
        $syncModel->exportSkus($exportPath);

        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('exportskus')->__(
                'Product Skus exported successfully!'
            )
        );

        $this->_redirect('exportorders/adminhtml_order/index');
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('exportskus/adminhtml/index');
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
