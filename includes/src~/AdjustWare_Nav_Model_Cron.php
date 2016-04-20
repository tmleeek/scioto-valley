<?php
/**
 * Layered Navigation Pro
 *
 * @category:    AdjustWare
 * @package:     AdjustWare_Nav
 * @version      2.5.4
 * @license:     yc4tx3fdyujjEs5czyndvhoc8zpLrKl3OCuGehtGvM
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class AdjustWare_Nav_Model_Cron extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('adjnav/cron');
    }

    public function canRunJob($code)
    {
        $this->load($code);

        if (time() - strtotime($this->getLastRun()) > Mage::helper('adjnav/featured')->collectPeriod() * 60)
        {
            return true;
        }

        return false;
    }
}