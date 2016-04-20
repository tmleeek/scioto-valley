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
 * @package    AW_Pquestion2
 * @version    2.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Pquestion2_Block_Adminhtml_Question_Answer_New_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $_result = parent::_prepareColumns();
        $this->_exportTypes = array();
        unset($this->_columns['customer_since']);
        unset($this->_columns['action']);
        return $_result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_answer/customerGrid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return "javascript:AnswerForm.open('" . $this->getUrl(
            '*/adminhtml_answer/new',
            array(
                'customer_id' => $row->getId(),
                'question_id' => Mage::app()->getRequest()->getParam('question_id')
            )
        ) . "', '', '" . Mage::helper('aw_pq2')->escapeHtml($this->__('New Answer')) . "', '', 570)";
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        $_result = parent::_prepareLayout();
        $this->unsetChild('back_button');
        return $_result;
    }
}