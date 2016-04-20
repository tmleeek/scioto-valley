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

class MageWorx_CurrencySwitcher_Block_Adminhtml_Currency_Relations extends Mage_Adminhtml_Block_Template
{
    /**
     * Custom currency relation data
     *
     * @var array
     */
    protected $_relationsData = array();

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'currencyswitcher';
        $this->_controller = 'adminhtml_relations';
        parent::__construct();
    }

    /**
     * Returns page header text
     *
     * @return string
     */
    public function getHeader()
    {
        return Mage::helper('currencyswitcher')->__('Manage Currency Relations');
    }

    /**
     * Returns 'Save Currency Symbol' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('currencyswitcher')->__('Save Currency Relations'),
            'onclick'   => 'currencyRelationsForm.submit();',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns 'Refresh' button's HTML code
     *
     * @return string
     */
    public function getRefreshButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('currencyswitcher')->__('Refresh'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/refresh') . '\')',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns Custom currency relation data
     *
     * @return array
     */
    public function getCurrencyRelations()
    {
        if (!$this->_relationsData) {
            $this->_relationsData = Mage::getModel('currencyswitcher/relations')->getCollection()->getItems();
        }
        return $this->_relationsData;
    }

    /**
     * Returns inheritance text
     *
     * @return string
     */
    public function getInheritText()
    {
        return Mage::helper('currencyswitcher')->__('Use Standard');
    }
}
