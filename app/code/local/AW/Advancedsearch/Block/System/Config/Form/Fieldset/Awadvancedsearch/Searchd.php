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

class AW_Advancedsearch_Block_System_Config_Form_Fieldset_Awadvancedsearch_Searchd
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $html .= '<div id="awas_config_container"></div>
        <div id="awas_config_loader">
            <img src="'.$this->getSkinUrl('aw_advancedsearch/images/ajax-loader.gif').'" />
        </div>
        <div id="awas_config_error" style="display:none">
            <span class="awas_state_bad">'
            . $this->__('Error occurs. Click <a href="%s">here</a> for details', $this->getUrl('adminhtml/awcore_viewlog'))
            . '</span>
        </div>
        <script type="text/javascript">
            var awas_checkStateUrl = "'.$this->getUrl('adminhtml/awadvancedsearch_sphinx/checkstate').'";
            var awas_startDaemonUrl = "'.$this->getUrl('adminhtml/awadvancedsearch_sphinx/start').'";
            var awas_stopDaemonUrl = "'.$this->getUrl('adminhtml/awadvancedsearch_sphinx/stop').'";
        </script>';
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}