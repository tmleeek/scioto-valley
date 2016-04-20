<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\   Jobs extension  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    Jobs                    \\\\\\\
 ///////    * @author     Malik Tahir Mehmood <malik.tahir786@gmail.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2010 � free-magentoextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

class FME_Jobs_Block_Detail extends Mage_Core_Block_Template
{
    public function _prepareLayout(){
    	$item = $this->getItem();
	
	
	
    	if ( Mage::getStoreConfig('web/default/show_cms_breadcrumbs') && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) ) {
    		$breadcrumbs->addCrumb('home', array('label'=>Mage::helper('page')->__('Home'), 'title'=>Mage::helper('page')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
			$breadcrumbs->addCrumb('jobs_home', array('label' => Mage::helper('jobs')->getjobsLabel(), 'title' => Mage::helper('jobs')->getjobsLabel(), 'link'=>Mage::getBaseUrl().'jobs'));
			$breadcrumbs->addCrumb('detail', array('label' => $item->getDepartmentName() ." ". $item->getStoreName(), 'title' => $item->getDepartmentName() ." ". $item->getStoreName()));
    	}	
	
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            
            $title = $item->getMetaTitle();
            if ($title) {
                $headBlock->setTitle($title);
            }
            
            $keyword = $item->getMetaKeywords();
            if ($keyword) {
                $headBlock->setKeywords($keyword);
            } 
            
            $description = $item->getMetaDesc();
            if ($description) {
                $headBlock->setDescription( ($description) );
            } 
            
        }
	
	
        return parent::_prepareLayout();
    }


    public function getItem(){ 
        if (!$this->hasData('item')) {
            $this->setData('item', Mage::registry('item'));
        }
	$data=$this->getData('item');
        return $data;      
    }

}