<?php



class FME_Jobs_Block_Adminhtml_Jobs_Edit_Tab_Applicants extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {   
        parent::__construct();
        $this->setId('jobs_applicants_grid');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        
    }

    protected function _beforeToHtml()
    {
        $this->setId($this->getId().'_'.$this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName().'.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName().'.doFilter()');
        return parent::_beforeToHtml();
    }
    
   
    protected function _addColumnFilterToCollection($column)
    {
		// Set custom filter for in product flag
		if ($column->getId() == 'id') {
			$applicantIds = $this->getApplicants();

			if (empty($applicantIds)) {
				$applicantIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('r_id', array('in'=>$applicantIds));
			} else {
				if($applicantIds) {
					$this->getCollection()->addFieldToFilter('r_id', array('nin'=>$applicantIds));
				}
			}
		} else {
			parent::_addColumnFilterToCollection($column);
		}
	
		return $this;
	}
    
    
    

    protected function _prepareCollection()
    {   $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('jobs/jobsapplications')->getCollection()->addFilter('job_id', $id);
        
        $resource = Mage::getSingleton('core/resource');
      
      $collection->getSelect()
          ->joinLeft(array('jobsapps' => $resource->getTableName('fme_jobs')), "jobsapps.jobs_id = main_table.job_id", array('jobsapps.*'))
          ->joinLeft(array('jobsapps2' => $resource->getTableName('fme_jobs')), "jobsapps2.jobs_id = main_table.job_id", array('appliedon'=>'main_table.create_date'));    
          
          //->group('jobsapps.jobs_id');
//      echo $collection->getSelect(); exit;
        
      $this->setCollection($collection);

        
        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }

        

        

        //$this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
//        $this->addColumn('in_deal', array(
//                        'header'            => Mage::helper('groupdeals')->__('Related'),
//			'header_css_class'  => 'a-center',
//			'type'              => 'checkbox',
//			'name'              => 'in_deal',
//			'align'             => 'center',
//                        'width'             => '20px',
//			'values'            => $this->getSelectedRelatedDeals(),
//			'index'             => 'id'
//         ));
        

        
//        $this->addColumn('prod_title1', array(
//            'header'    => Mage::helper('sales')->__('Product Name'),
//            'sortable'  => true,
//            'width'     => '60px',
//            'index'     => 'prod_title'
//        ));
//        
        $this->addColumn('fullname', array(
            'header'    => Mage::helper('jobs')->__('Applicant Name'),
            'sortable'  => true,
//            'width'     => '60px',
            'index'     => 'fullname'
        ));
        
        $this->addColumn('email', array(
            'header'    => Mage::helper('jobs')->__('Email'),
            'sortable'  => true,
//            'width'     => '60px',
            'index'     => 'email'
        ));
        
       $this->addColumn('telephone', array(
            'header'    => Mage::helper('jobs')->__('Phone'),
            'sortable'  => true,
//            'width'     => '60px',
            'index'     => 'telephone'
        ));
       
       $this->addColumn('cvfile', array(
            'header'    => Mage::helper('jobs')->__('CV'),
            'sortable'  => true,
//            'width'     => '60px',
            'index'     => 'cvfile',
           'renderer'  => new FME_Jobs_Block_Adminhtml_Renderer_Cvrenderer()
        ));
       
       $this->addColumn('appliedon', array(
            'header'    => Mage::helper('jobs')->__('Application Date'),
            'sortable'  => true,
//            'width'     => '60px',
            'index'     => 'appliedon'
        ));
        
    

        

//        $this->addColumn('prod_sku1', array(
//            'header'    => Mage::helper('sales')->__('SKU'),
//            'width'     => '80px',
//            'index'     => 'prod_sku',
//            'column_css_class'=> 'sku'
//        ));
//        
//        $this->addColumn('sprice1', array(
//            'header'    => Mage::helper('sales')->__('Special Price'),
//            'align'     => 'center',
//            'type'      => 'currency',
//            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
//            'rate'      => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
//            'index'     => 'sprice'
//        ));
        
        
        
        
        

        return parent::_prepareColumns();
    }
    
        public function getApplicants()
    {
		$id = $this->getRequest()->getParam('id');
       	$appsArr = array();
        foreach (Mage::getModel('jobs/jobs')->getApplicants($id) as $app) {
           $appsArr[$app["r_id"]] = array('position' => '0');
        }
		$app = array_keys($appsArr);
        return $app;
    }
    
   public function getRowUrl($row)
  {
      return $this->getUrl('adminjobs/adminhtml_applications/edit', array('id' => $row->getId()));
  }

    

    public function getGridUrl()
    {  
        return $this->getUrl('adminjobs/adminhtml_jobs/applicants', array('index' => $this->getIndex(),'_current'=>true));
    }
    
    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }
    
    
    
   
    
    
    
    
}
