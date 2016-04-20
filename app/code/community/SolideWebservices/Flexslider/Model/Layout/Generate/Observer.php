<?php
/**
 * @category	Solide Webservices
 * @package		Flexslider
 */

class SolideWebservices_Flexslider_Model_Layout_Generate_Observer {

	/**
	 * Add scripts depending on configuration values
	 */
	public function loadScripts($observer) {

		if (Mage::getStoreConfig('flexslider/general/enabled')) {
			$_head = $this->__getHeadBlock();
			
			if ($_head) {
				$_head->addFirst(		'skin_css', 'flexslider/css/flexslider.css');
				
				// determine if the scripts should be loaded first or last
				if(Mage::getStoreConfig('flexslider/general/jquery_position') == 'before') {
					$_head->addFirst(	'js',		'flexslider/jquery.flexslider-min.js');
				} else {
					$_head->addEnd(		'js',		'flexslider/jquery.flexslider-min.js');
				}

				// if jQuery is enabled
				if(Mage::getStoreConfig('flexslider/general/enable_jquery')) {
					
					// should the latest jquery version be loaded through Google CDN or a selected local version
					if (Mage::getStoreConfig('flexslider/general/version_jquery') == 'latest') {
                        if(Mage::app()->getStore()->isCurrentlySecure()) {
                            $_head->addBefore(		'js', 	'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 	'flexslider/jquery.flexslider-min.js');
                            // should noConflict mode be loaded
                            if(Mage::getStoreConfig('flexslider/general/jquery_noconflictmode')) {
                                $_head->addAfter(	'js', 	'flexslider/jquery.noconflict.js', 	'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                            }
                        } else {
                            $_head->addBefore(		'js', 	'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', 	'flexslider/jquery.flexslider-min.js');
                            // should noConflict mode be loaded
                            if(Mage::getStoreConfig('flexslider/general/jquery_noconflictmode')) {
                                $_head->addAfter(	'js', 	'flexslider/jquery.noconflict.js', 	'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                            }
                        }
					} else {
						$_head->addBefore(	'js', 	'flexslider/jquery-'. Mage::getStoreConfig('flexslider/general/version_jquery') .'.min.js', 	'flexslider/jquery.flexslider-min.js');
						// should noConflict mode be loaded
						if(Mage::getStoreConfig('flexslider/general/jquery_noconflictmode')) {
							$_head->addAfter(	'js', 	'flexslider/jquery.noconflict.js', 								'flexslider/jquery-'. Mage::getStoreConfig('flexslider/general/version_jquery') .'.min.js');
						}
					}
					
					// should the easing library be loaded
					if (Mage::getStoreConfig('flexslider/general/enable_easing')) {
						// if noconflict mode is loaded
						if(Mage::getStoreConfig('flexslider/general/jquery_noconflictmode')) {
							$_head->addAfter(		'js', 	'flexslider/jquery.easing.js', 			'flexslider/jquery.noconflict.js');
							$_head->addAfter(		'js', 	'flexslider/jquery.fitvid.js', 			'flexslider/jquery.easing.js');
						} else {
                            if (Mage::getStoreConfig('flexslider/general/version_jquery') == 'latest') {
                                if(Mage::app()->getStore()->isCurrentlySecure()) {
                                    $_head->addAfter(	'js', 	'flexslider/jquery.easing-nojquery.js', 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                                } else {
                                    $_head->addAfter(	'js', 	'flexslider/jquery.easing-nojquery.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                                }
								$_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 			'flexslider/jquery.easing-nojquery.js');
							} else {
								$_head->addAfter(	'js', 	'flexslider/jquery.easing-nojquery.js', 'flexslider/jquery-'. Mage::getStoreConfig('flexslider/general/version_jquery') .'.min.js');
								$_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 			'flexslider/jquery.easing-nojquery.js');
							}
						}
					} else {
						if(Mage::getStoreConfig('flexslider/general/jquery_noconflictmode')) {
							$_head->addAfter(		'js', 	'flexslider/jquery.fitvid.js', 			'flexslider/jquery.noconflict.js');
						} else {
							if (Mage::getStoreConfig('flexslider/general/version_jquery') == 'latest') {
                                if(Mage::app()->getStore()->isCurrentlySecure()) {
                                    $_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 			'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                                } else {
                                    $_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 			'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
                                }
							} else {
								$_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 			'flexslider/jquery-'. Mage::getStoreConfig('flexslider/general/version_jquery') .'.min.js');
							}
						}
					}
					
					// always load the froogaloop and hoverIntent libraries
					$_head->addAfter(		'js', 	'flexslider/froogaloop.js', 					'flexslider/jquery.fitvid.js');
					$_head->addAfter(		'js', 	'flexslider/jquery.hoverIntent.js', 			'flexslider/froogaloop.js');
					
				// if jQuery isnt enabled
				} else {
				
					// should the easing library be loaded
					if (Mage::getStoreConfig('flexslider/general/enable_easing')) {
						$_head->addBefore(	'js', 	'flexslider/jquery.easing-nojquery.js', 		'flexslider/jquery.flexslider-min.js');
						$_head->addAfter(	'js', 	'flexslider/jquery.fitvid.js', 					'flexslider/jquery.easing-nojquery.js');
					} else {
						$_head->addBefore(	'js', 	'flexslider/jquery.fitvid.js', 					'flexslider/jquery.flexslider-min.js');
					}
					
					// always load the froogaloop and hoverIntent libraries
					$_head->addAfter(		'js', 	'flexslider/froogaloop.js', 					'flexslider/jquery.fitvid.js');
					$_head->addAfter(		'js', 	'flexslider/jquery.hoverIntent.js', 			'flexslider/froogaloop.js');
				}
			}
		}
	}

	/*
	 * Get head block
	 */
	private function __getHeadBlock() {
		return Mage::getSingleton('core/layout')->getBlock('flexslider_head');
	}

}