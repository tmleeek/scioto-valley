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

class AW_Advancedsearch_Block_Adminhtml_Indexes_Edit_Fieldset_Error
    extends AW_Advancedsearch_Block_Adminhtml_Indexes_Edit_Fieldset_Abstract
{
    protected function _getTemplate()
    {
        return 'aw_advancedsearch/form/renderer/fieldset/error.phtml';
    }

    protected function _getIndexAttributes()
    {
        return null;
    }

    protected function _getDataObject()
    {
        return null;
    }
}