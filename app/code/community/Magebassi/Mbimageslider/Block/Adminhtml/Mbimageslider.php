<?php
/**
 *
 * Version			: 1.0.4
 * Edition 			: Community 
 * Compatible with 	: Magento Versions 1.5.x to 1.7.x
 * Developed By 	: Magebassi
 * Email			: magebassi@gmail.com
 * Web URL 			: www.magebassi.com
 * Extension		: Magebassi Bannerslider
 * 
 */
?>
<?php
class Magebassi_Mbimageslider_Block_Adminhtml_Mbimageslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
		$this->_controller = 'adminhtml_mbimageslider';
		$this->_blockGroup = 'mbimageslider';
		$this->_headerText = Mage::helper('mbimageslider')->__('Banner Manager');
		$this->_addButtonLabel = Mage::helper('mbimageslider')->__('Add Banner');
		parent::__construct();
  }
}