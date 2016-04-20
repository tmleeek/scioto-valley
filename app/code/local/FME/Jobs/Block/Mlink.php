<?php
/**
 * Our Work extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    Our Work
 * @author     Mahmood Rehman <mahmood.rehman240@unitedsol.net>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
class FME_Jobs_Block_Mlink extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $html ='';      
      
        if(Mage::helper('jobs')->getextStatus()){
     
        if(in_array(Mage::app()->getStore()->getStoreId(),Mage::helper('jobs')->getscopeid())){

                $html.= '<li><a  href="'.Mage::helper('jobs')->getjobsUrl().'">'.Mage::helper('jobs')->__('Jobs').'</a></li>';    
                return $html;
            }
        }
        
        return $html;
    }
	
	
}