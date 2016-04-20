<?php

class Magebassi_Mbimageslider_Block_About
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$logopath	=	'http://www.magentocommerce.com/images/avatars/uploads/avatar_179510.png';
        $html = <<<HTML
		<div style="background:url('$logopath') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 164px;">
		<p>
			<strong>PREMIUM FREE and PAID MAGENTO EXTENSIONS</strong><br />
			<a href="http://www.magebassi.com/" target="_blank">Magebassi</a> offers a wide choice of nice-looking and easily editable free and premium Magento extensions.<br />       
		</p>
		<p>
			You can <a href="mailto:magebassi@gmail.com">Contact Me</a> to customize new magento extensions per your requirement.			
		</p>
		<p>
			We are also expert in BrightPearl, Channel Advisor and Linnworks. 
		</p>
		<p>
			My extensions on <a href="http://www.magentocommerce.com/magento-connect/developer/magebassi#extensions" target="_blank">MagentoConnect</a><br />
			Should you have any questions email at <a href="mailto:magebassi@gmail.com">magebassi@gmail.com</a>
			<br />
		</p>
		</div>
HTML;
        return $html;
    }
}