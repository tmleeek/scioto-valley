<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
class Amasty_Paction_Model_Command_Addspecialbycost extends Amasty_Paction_Model_Command_Addspecial
{ 
    public function __construct($type)
    {
        parent::__construct($type);
        $this->_label = 'Modify Special Price using Cost';
    } 

    protected function _getAttrCode()
    {
        return 'cost';
    }        
        
}