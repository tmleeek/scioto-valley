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

class AW_Advancedsearch_Model_Source_Sphinx_Wildcard extends AW_Advancedsearch_Model_Source_Abstract
{
    const START_CODE   = 1;
    const START_LABEL  = '*start';

    const MIDDLE_CODE  = 2;
    const MIDDLE_LABEL = '*middle*';

    const END_CODE     = 3;
    const END_LABEL    = 'end*';

    protected function _toOptionArray()
    {
        $helper = $this->_getHelper();
        return array(
            array(
                'value' => self::START_CODE,
                'label' => $helper->__(self::START_LABEL),
            ),
            array(
                'value' => self::MIDDLE_CODE,
                'label' => $helper->__(self::MIDDLE_LABEL),
            ),
            array(
                'value' => self::END_CODE,
                'label' => $helper->__(self::END_LABEL),
            ),
        );
    }
}