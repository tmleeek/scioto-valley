<?php
/**
 * @category	Solide Webservices
 * @package		Flexslider
 */

class SolideWebservices_Flexslider_Model_Slide extends Mage_Core_Model_Abstract {

	protected function _construct() {
		$this->_init('flexslider/slide');
	}

	/**
	 * Retrieve the group model associated with the slide
	 *
	 * @return SolideWebservices_Flexslider_Model_Group|false
	 */
	public function getGroup() {
		if (!$this->hasGroup()) {
			$this->setGroup($this->getResource()->getGroup($this));
		}

		return $this->getData('group');
	}
	
	/**
	 * Check if images are hosted
	 */
	public function isHostedImage() {
		return $this->getData('hosted_image');
	}
	
	/**
	 * Check if slide is a video
	 *
	 * @return bool
	 */
	public function isVideo() {
		if($this->getData('slidetype')!='image') { return true; }
	}
	
	/**
	 * Return slide type
	 *
	 * @return bool
	 */
	public function getSlideType() {
		return $this->getData('slidetype');
	}

	/**
	 * Retrieve the alt text
	 * If the alt_text field is empty, use the title field
	 *
	 * @return string
	 */
	public function getAltText() {
		return $this->getData('alt_text') ? $this->getData('alt_text') : $this->getTitle();
	}

	/**
	 * Retrieve the image URL
	 *
	 * @return string
	 */
	public function getImageUrl() {

		if (!$this->hasImageUrl()) {
			$this->setImageUrl(Mage::helper('flexslider/image')->getImageUrl($this->getImage()));
		}

		return $this->getData('image_url') ? $this->getData('image_url') : $this->getData('hosted_image_url');

	}

	/**
	 * Retrieve the thumbnail URL
	 *
	 * @return string
	 */
	public function getThumbUrl() {

		if (!$this->hasThumbUrl()) {
			$this->setThumbUrl(Mage::helper('flexslider/image')->getThumbUrl($this->getImage()));
		}

		return $this->getData('thumb_url') ? $this->getData('thumb_url') : $this->getData('hosted_image_thumburl');

	}
	
	/**
	 * Retrieve the video URL
	 *
	 * @return string
	 */
	public function getVideoId() {
		return $this->getData('video_id');
	}
	
	/**
	 * Determine whether the slide has a valid URL
	 *
	 * @return bool
	 */
	public function hasUrl() {
		return strlen($this->getUrl()) > 1;
	}
	
	/**
	 * Retrieve the URL
	 * This converts relative URL's to absolute
	 *
	 * @return string
	 */
	public function getUrl() {
		if ($this->_getData('url')) {
			if (strpos($this->_getData('url'), 'http://') === false && strpos($this->_getData('url'), 'https://') === false) {
				$this->setUrl(Mage::getBaseUrl() . ltrim($this->_getData('url'), '/ '));
			}
		}

		return $this->_getData('url');
	}

	/**
	 * Retrieve the url target
	 * If the url_target field is empty, use the _self
	 *
	 * @return string
	 */
	public function getUrlTarget() {
		return $this->getData('url_target') ? $this->getData('url_target') : '_self';
	}

}