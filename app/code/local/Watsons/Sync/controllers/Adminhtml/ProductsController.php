<?php
class Watsons_Sync_Adminhtml_ProductsController
    extends Mage_Adminhtml_Controller_Action
{
    public function copy_formAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('sync/copy');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent(
            $this->getLayout()->createBlock(
                'sync/adminhtml_products_copy'
            )
        );

        $this->renderLayout();
    }

    public function copyAction()
    {
        Mage::log(__METHOD__);
        set_time_limit(-1);

        $fromWebsiteId  = $this->getRequest()->getParam('from_website_id');
        $toWebsiteId    = $this->getRequest()->getParam('to_website_id');

        if ($fromWebsiteId && $toWebsiteId && $fromWebsiteId != $toWebsiteId) {
            $productModel = Mage::getModel('catalog/product');

            $productCollection = $productModel->getCollection()
                ->addWebsiteFilter(array($fromWebsiteId))
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('website_ids');

            $toProductIds = array();

            foreach ($productCollection as $product) {
                $websiteIds = $product->getWebsiteIds();
                if (!in_array($toWebsiteId, $websiteIds)) {
                    $toProductIds[] = $product->getId();
                }
            }

            $productWebModel = Mage::getModel('catalog/product_website');
            $productWebModel->addProducts(array($toWebsiteId), $toProductIds);

            Mage::log('COMPLETED!');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('sync')->__(
                    'Select a from and to website and make sure they are different!'
                )
            );
        }
        $this->_redirect('*/*/copy_form/');
    }
}