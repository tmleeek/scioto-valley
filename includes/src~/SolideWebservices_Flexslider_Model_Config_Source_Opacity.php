<?php
/**
 * @category	Solide Webservices
 * @package		Flexslider
 */

class SolideWebservices_Flexslider_Model_Config_Source_Opacity {

	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function toOptionArray($includeEmpty = false, $emptyText = '-- Please Select --') {
		$options = array();

		if ($includeEmpty) {
			$options[] = array(
				'value' => '',
				'label' => Mage::helper('adminhtml')->__($emptyText),
			);
		}

		foreach($this->getOptions() as $value => $label) {
			$options[] = array(
				'value' => $value,
				'label' => Mage::helper('adminhtml')->__($label),
			);
		}

		return $options;
	}

	/**
	 * Retrieve an array of possible options
	 *
	 * @return array
	 */
	public function getOptions() {
		return array(
			'0.0'			=> 'Fully Transparent',
			'0.1' 			=> '10%',
			'0.2' 			=> '20%',
			'0.3' 			=> '30%',
			'0.4' 			=> '40%',
			'0.5' 			=> '50%',
			'0.6' 			=> '60%',
			'0.7' 			=> '70%',
			'0.8' 			=> '80%',
			'0.9' 			=> '90%',
			'1' 			=> '100%'
		);
	}
}