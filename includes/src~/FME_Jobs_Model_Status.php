<?php

 /**
 * Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME
 * @package    FME_Jobs
 * @author     Malik Tahir Mehmood<malik.tahir786@gmail.com>
 * @copyright  Copyright 2010 © free-magentoextensions.com All right reserved
 */
 
class FME_Jobs_Model_Status extends Varien_Object
{
    const STATUS_YES    	= '1';
    const STATUS_NO     	= '0';
  

    static public function getOptionArray()
    {
        return array(
           
            self::STATUS_NO   => Mage::helper('jobs')->__('Disable'),
            self::STATUS_YES    => Mage::helper('jobs')->__('Enable')
        );
    }
}