<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedsearch
 * @version    1.4.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedsearch_Adminhtml_Awadvancedsearch_SphinxController extends Mage_Adminhtml_Controller_Action
{
    public function checkstateAction()
    {
        /** @var AW_Advancedsearch_Model_Engine_Sphinx $sphinx */
        $sphinx = Mage::getModel('awadvancedsearch/engine_sphinx');
        $state = $sphinx->checkSearchdState();
        /** @var Mage_Adminhtml_Block_Template $block */
        $block = Mage::getSingleton('core/layout')->createBlock('adminhtml/template');
        $block->setData('state', $state)->setTemplate('aw_advancedsearch/system/config/form/fieldset/state.phtml');
        $response = array(
            'state' => $state,
            'html' => $block->toHtml()
        );
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function testConnectionAction()
    {
        $addr = $this->getRequest()->getParam('server_addr');
        $port = $this->getRequest()->getParam('server_port');

        /** @var AW_Advancedsearch_Model_Engine_Sphinx $sphinx */
        $sphinx = Mage::getModel('awadvancedsearch/engine_sphinx');
        $state = $sphinx->checkSearchdState($addr, $port);

        $this->getResponse()->setBody(Zend_Json::encode(array('state' => $state)));
    }

    public function stopAction()
    {
        /** @var AW_Advancedsearch_Model_Engine_Sphinx $sphinx */
        $sphinx = Mage::getModel('awadvancedsearch/engine_sphinx');
        $response = array('r' => $sphinx->stopSearchd());
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }

    public function startAction()
    {
        /** @var AW_Advancedsearch_Model_Engine_Sphinx $sphinx */
        $sphinx = Mage::getModel('awadvancedsearch/engine_sphinx');
        $response = array('r' => $sphinx->startSearchd());
        $this->getResponse()->setBody(Zend_Json::encode($response));
    }
}
