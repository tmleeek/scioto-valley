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

class Magebassi_Mbimageslider_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {		
		$model = Mage::getModel('mbimageslider/mbimageslider');
		$data  = $model->loadByField('sliderid',$row->getId());
				
		$url = Mage::getBaseUrl('media') . 'mbimages/thumbs/' . $data['filename'];
		$out = "<img src=". $url ." width='80px'/>";
		return $out;
	}
}