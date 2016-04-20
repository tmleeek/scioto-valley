<?php
/**
 * @category	Solide Webservices
 * @package		Flexslider
 */

class SolideWebservices_Flexslider_Block_Page_Html_Head extends Mage_Page_Block_Html_Head {

	/* add dynamic css based on backend settings to HEAD */
	protected function _prepareLayout() {
        parent::_prepareLayout();
        if($this->helper('flexslider')->isEnabled()) {
        	if($head_block = $this->getLayout()->getBlock('head')) {
				$styles_block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'flexslider_styles')->setTemplate('flexslider/styles.php');
				$head_block->setChild('flexslider_styles', $styles_block);
			}                   
        }
    }

	/**
	 * Add HEAD Item First
	 *
	 * Allowed types:
	 *	- js
	 *	- js_css
	 *	- skin_js
	 *	- skin_css
	 *	- rss
	 */
	public function addFirst($type, $name, $params=null, $if=null, $cond=null) {
		$_item = array(
			$type.'/'.$name => array(
				'type'	 => $type,
				'name'	 => $name,
				'params' => $params,
				'if'	 => $if,
				'cond'	 => $cond)
			);
		$_head = $this->__getHeadBlock();
		if (is_object($_head)) {
			$_itemList = $_head->getData('items');
			$_itemList = array_merge($_item, $_itemList);

			$_head->setData('items', $_itemList);
		}
	}

	/**
	 * Add HEAD Item before
	 *
	 * Allowed types:
	 *	- js
	 *	- js_css
	 *	- skin_js
	 *	- skin_css
	 *	- rss
	 */
	public function addBefore($type, $name, $before=null, $params=null, $if=null, $cond=null) {
		if ($before) {
			$_backItem = array();
			$_searchStatus = false;
			$_searchKey = $type.'/'.$before;
			$_head = $this->__getHeadBlock();
			if (is_object($_head)) {
				$_itemList = $_head->getData('items');
				if (is_array($_itemList)) {
					$keyList = array_keys($_itemList);
					foreach ($keyList as &$_key) {
						if ($_searchKey == $_key) {
							$_searchStatus = true;
						}

						if ($_searchStatus) {
							$_backItem[$_key] = $_itemList[$_key];
							unset($_itemList[$_key]);
						}
					}
				}

				if ($type==='skin_css' && empty($params)) {
					$params = 'media="all"';
				}
				$_itemList[$type.'/'.$name] = array(
					'type'	 => $type,
					'name'	 => $name,
					'params' => $params,
					'if'	 => $if,
					'cond'	 => $cond,
				);

				if (is_array($_backItem)) {
					$_itemList = array_merge($_itemList, $_backItem);
				}
				$_head->setData('items', $_itemList);
			}
		}
	}

	/**
	 * Add HEAD Item After
	 *
	 * Allowed types:
	 *	- js
	 *	- js_css
	 *	- skin_js
	 *	- skin_css
	 *	- rss
	 */
	public function addAfter($type, $name, $after=null, $params=null, $if=null, $cond=null) {
		if ($after) {
			$_backItem = array();
			$_searchStatus = false;
			$_searchKey = $type.'/'.$after;
			$_head = $this->__getHeadBlock();
			if (is_object($_head)) {
				$_itemList = $_head->getData('items');
				if (is_array($_itemList)) {
					$keyList = array_keys($_itemList);
					foreach ($keyList as &$_key) {
						if ($_searchStatus) {
							$_backItem[$_key] = $_itemList[$_key];
							unset($_itemList[$_key]);
						}
						if ($_searchKey == $_key) {
							$_searchStatus = true;
						}
					}
				}

				if ($type==='skin_css' && empty($params)) {
					$params = 'media="all"';
				}
				$_itemList[$type.'/'.$name] = array(
					'type'	 => $type,
					'name'	 => $name,
					'params' => null,
					'if'	 => null,
					'cond'	 => null,
				);

				if (is_array($_backItem)) {
					$_itemList = array_merge($_itemList, $_backItem);
				}
				$_head->setData('items', $_itemList);
			}
		}
	}

	/**
	 * Add HEAD Item At End
	 *
	 * Allowed types:
	 *	- js
	 *	- js_css
	 *	- skin_js
	 *	- skin_css
	 *	- rss
	 */
	public function addEnd($type, $name, $params=null, $if=null, $cond=null) {
		$_item = array(
			$type.'/'.$name => array(
				'type'	 => $type,
				'name'	 => $name,
				'params' => $params,
				'if'	 => $if,
				'cond'	 => $cond)
			);
		$_head = $this->__getHeadBlock();
		if (is_object($_head)) {
			$_itemList = $_head->getData('items');
			$_itemList = array_merge($_itemList, $_item);

			$_head->setData('items', $_itemList);
		}
	}

	/**
	* Merge static and skin files of the same format into 1 set of HEAD directives or even into 1 directive
	* EDITED TO LOAD EXTERNAL JAVASCRIPT IF PATH STARTS WITH HTTP OR HTTPS
	*/
	protected function &_prepareStaticAndSkinElements($format, array $staticItems, array $skinItems, $mergeCallback = null) {
		$designPackage = Mage::getDesign();
        $baseJsUrl = Mage::getBaseUrl('js');
        $items = array();
        if ($mergeCallback && !is_callable($mergeCallback)) {
            $mergeCallback = null;
        }
		
		// get static files from the js folder, no need in lookups
		foreach ($staticItems as $params => $rows) {
			foreach ($rows as $name) {
				//if http or htttps, do not add baseurl, do not try to merge
				if(strstr($name, 'http://') || strstr($name, 'https://')) {
					$items[$params][] = $name;
				} else {
					$items[$params][] = $mergeCallback ? Mage::getBaseDir() . DS . 'js' . DS . $name : $baseJsUrl . $name;
				}
			}
		}

		// lookup each file basing on current theme configuration
		foreach ($skinItems as $params => $rows) {
			foreach ($rows as $name) {
				$items[$params][] = $mergeCallback ? $designPackage->getFilename($name, array('_type' => 'skin'))
					: $designPackage->getSkinUrl($name, array());
			}
		}

		$html = '';
		foreach ($items as $params => $rows) {
			// attempt to merge
			$mergedUrl = false;
			if ($mergeCallback) {
				$mergedUrl = call_user_func($mergeCallback, $rows);
			}
			// render elements
			$params = trim($params);
			$params = $params ? ' ' . $params : '';
			if ($mergedUrl) {
				$html .= sprintf($format, $mergedUrl, $params);
			} else {
				foreach ($rows as $src) {
					$html .= sprintf($format, $src, $params);
				}
			}
		}
		return $html;
	}

	/*
	 * Get head block
	 */
	private function __getHeadBlock() {
		return Mage::getSingleton('core/layout')->getBlock('head');
	}

}