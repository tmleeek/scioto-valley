<?php
/*------------------------------------------------------------------------
 # MegaProductsII - Version 1.0
 # Copyright (C) 2011 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: The YouTech Company
 # Websites: http://smartaddons.com
 -------------------------------------------------------------------------*/

class Smartaddons_MegaProductsII_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
	  $this->loadLayout();
      $this->renderLayout();
    }
}