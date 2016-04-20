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
 * @package    MageWorx_StoreSwitcher
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Store Auto Switcher extension
 * Exception class
 *
 * @category   MageWorx
 * @package    MageWorx_StoreSwitcher
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_StoreSwitcher_Model_System_Config_Source_Mode
{
    const DISLPAY_MODE_NAMES        = 0;
    const DISPLAY_MODE_NAMES_FLAGS  = 1;

    /**
     * Return options for config "Switcher Display Mode"
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::DISLPAY_MODE_NAMES,
                'label' => Mage::helper('core')->__('Names')
            ),
            array(
                'value' => self::DISPLAY_MODE_NAMES_FLAGS,
                'label' => Mage::helper('core')->__('Flags + Names')
            ),
        );
    }
}