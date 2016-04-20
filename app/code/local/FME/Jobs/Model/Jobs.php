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
class FME_Jobs_Model_Jobs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('jobs/jobs');
    }
    public function toOptionArray($model, $order ='Asc', $empty = false , $mode = true)
    {
        $html=array();
        if($empty){
            if($mode){
            array_push($html,array('value'=>'','label'=>''));
            }else{
              array_push($html,array(''=>''));   
            }
        }
       
        $collection=Mage::getModel('jobs/' . $model)->getCollection()->load()->addOrder($model . '_name',$order)->addFieldToFilter('status','1')->getData();
        if(!empty($collection) && is_array($collection)){
            if($mode){
                foreach($collection as $onebyone){
                    if($onebyone[$model.'_id'] && $onebyone[$model . '_name']){
                      array_push($html,array('value' => $onebyone[$model . '_name'] , 'label'=>$onebyone[$model . '_name']));
                    }
                }
            }else{
               foreach($collection as $onebyone){
                    if($onebyone[$model.'_id'] && $onebyone[$model . '_name']){
                  
                     $html[$onebyone[$model . '_name']] = $onebyone[$model . '_name'];
                     
                    }
                }
                //asort($html);
            }
        }
     
        return $html;
    }
    
    public function checkIdentifier($identifier)
    {
        return $this->_getResource()->checkIdentifier($identifier);
    }
    
    public function getApplicants($applicantId)
    {                
        $pro_productsTable = Mage::getSingleton('core/resource')->getTableName('fme_jobsapplications');
        $collection = Mage::getModel('jobs/jobs')->getCollection()
        ->addFieldToFilter('main_table.id', $applicantId);
        $collection->getSelect()
        ->joinLeft(array('applicant' => $pro_productsTable),
        'main_table.id = applicant.job_id')
        ->order('main_table.id');
        return $collection->getData();
    }
    
}