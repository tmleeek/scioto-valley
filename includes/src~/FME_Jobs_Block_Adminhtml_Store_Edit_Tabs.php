<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ Jobs extension \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Jobs                 \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 © free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */
class FME_Jobs_Block_Adminhtml_Store_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('jobs_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('jobs')->__('Locations'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('jobs')->__('Locations'),
          'title'     => Mage::helper('jobs')->__('Locations'),
          'content'   => $this->getLayout()->createBlock('jobs/adminhtml_store_edit_tab_form')->toHtml(),
      ));
      
      return parent::_beforeToHtml();
  }
}