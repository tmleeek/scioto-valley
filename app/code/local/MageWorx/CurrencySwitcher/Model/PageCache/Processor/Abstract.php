<?php

if(version_compare(Mage::getVersion(), '1.10.0', '>=')){
    class MageWorx_CurrencySwitcher_Model_PageCache_Processor_Abstract extends Enterprise_PageCache_Model_Processor {}
} else {
    class MageWorx_CurrencySwitcher_Model_PageCache_Processor_Abstract {}
}