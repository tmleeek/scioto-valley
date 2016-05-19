<?php

/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Downloads extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_Downloads_Model_Observer
{
    public function saveProductFiles(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        $ids = $product->getDownloadsFilesIds();
        $productId = $product->getId();
        $relation = Mage::getSingleton('downloads/relation');
        if ($productId && Mage::app()->getRequest()->getActionName() == 'save') {
            $relation->getResource()->deleteFilesProduct($productId);
        }
        if ($ids && $productId) {
            $ids = explode(',', $ids);
            $ids = array_unique($ids);
            foreach ($ids as $fileId) {
                $relation->setData(array(
                        'file_id' => $fileId,
                        'product_id' => $productId
                    )
                );
                $relation->save();
            }
        }
    }

    public function addFilesOnCategory($observer)
    {
        $helper = Mage::helper('downloads');
        $block = $observer->getBlock();
        $isCategory = Mage::registry('current_category') && !Mage::registry('current_product');

        if (!($block instanceof Mage_Catalog_Block_Product_Price)
            || !$isCategory
            || !$helper->isEnabled()
            || !$helper->isEnableFilesOnCategoryPages())
        {
            return $this;
        }

        $toolbar = $block->getLayout()->getBlock('product_list_toolbar');
        $isGridMode = $toolbar && $toolbar->getCurrentMode() && $toolbar->getCurrentMode() == 'grid';

        $html = $observer->getTransport()->getHtml();
        $filesHtml = Mage::app()->getLayout()->createBlock('downloads/product_link', '', array('id' => $block->getProduct()->getId(), 'is_category' => true, 'is_grid_mode' => $isGridMode))->toHtml();

        $observer->getTransport()->setHtml($html . $filesHtml);

        return $this;
    }

    public function addCustomerDownloadsTab($observer)
    {
        $block = $observer->getBlock();
        if (!($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs)) {
            return $this;
        }

        $block->addTabAfter('downloads', array(
            'label'  => Mage::helper('downloads')->__('File Downloads'),
            'class'  => 'ajax',
            'url'    => $block->getUrl('mageworx/downloads_files/customer', array('customer_id' => Mage::registry('current_customer')->getId()))
        ), 'tags');

        return $this;
    }
}
