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
 * @package    MageWorx_CurrencySwitcher
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Currency Auto Switcher extension
 * Exception class
 *
 * @category   MageWorx
 * @package    MageWorx_CurrencySwitcher
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_CurrencySwitcher_Adminhtml_RelationsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/currency')
            ->_addBreadcrumb(
                Mage::helper('currencyswitcher')->__('System'),
                Mage::helper('currencyswitcher')->__('System')
            )
            ->_addBreadcrumb(
                Mage::helper('currencyswitcher')->__('Manage Currency Relations'),
                Mage::helper('currencyswitcher')->__('Manage Currency Relations')
            );

        $this->_title($this->__('System'))
            ->_title($this->__('Manage Currency Relations'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $post = $this->getRequest()->getPost();

        try {
            if (isset($post['currency_relation'])) {
                foreach ($post['currency_relation'] as $id => $relation) {

                    if (empty($relation['countries'])) {
                        continue;
                    }

                    if (isset($relation['countries']['use_default'])) {
                        $countries = Mage::helper('currencyswitcher')->getCountryByCurrency($relation['code']);
                    } else {
                        $countries = $relation['countries'];
                    }
                    if (is_array($countries)) {
                        $countries = implode(',', $countries);
                    }

                    $data = array(
                        'relation_id'   => $id,
                        'currency_code' => $relation['code'],
                        'countries'     => $countries
                    );

                    $relationModel = Mage::getModel('currencyswitcher/relations')->load($id);
                    $relationModel->setData($data);
                    $relationModel->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('currencyswitcher')->__('Currency relations were saved successfully.')
                );
            } else {
                throw new Exception($this->__('No data to save'));
            }
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }

    /**
     * Refresh available currencies
     */
    public function refreshAction()
    {
        try {
            $currentCodes   = array();
            $relationModel  = Mage::getModel('currencyswitcher/relations');
            $collection     = $relationModel->getCollection();

            $availableCodes = array();

            foreach(Mage::app()->getStores() as $store){
                foreach($store->getAvailableCurrencyCodes(true) as $code){
                    if(!in_array($code, $availableCodes)){
                        $availableCodes[] = $code;
                    }
                }
            }

            foreach ($collection->getItems() as $item) {
                if (!in_array($item->getCurrencyCode(), $availableCodes)) {
                    $item->delete();
                    continue;
                }
                $currentCodes[] = $item->getCurrencyCode();
            }

            foreach ($availableCodes as $code) {
                if (!in_array($code, $currentCodes)) {
                    $data = array(
                        'currency_code' => $code,
                        'countries' => Mage::helper('currencyswitcher')->getCountryByCurrency($code)
                    );
                    $relationModel->setData($data)->save();
                }
            }


            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('currencyswitcher')->__('Currency relations were saved successfully.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }
}