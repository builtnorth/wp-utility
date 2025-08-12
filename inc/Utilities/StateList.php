<?php
/**
 * State List Utility
 *
 * Provides a list of US states and territories with abbreviations
 * for use in forms and dropdowns.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Utilities;

class StateList
{
	/**
	 * State List
	 */
	public static function render()
	{
		return [
			'AL' => __('Alabama', 'polaris-forms'),
			'AK' => __('Alaska', 'polaris-forms'),
			'AZ' => __('Arizona', 'polaris-forms'),
			'AR' => __('Arkansas', 'polaris-forms'),
			'CA' => __('California', 'polaris-forms'),
			'CO' => __('Colorado', 'polaris-forms'),
			'CT' => __('Connecticut', 'polaris-forms'),
			'DE' => __('Delaware', 'polaris-forms'),
			'DC' => __('District of Columbia', 'polaris-forms'),
			'FL' => __('Florida', 'polaris-forms'),
			'GA' => __('Georgia', 'polaris-forms'),
			'HI' => __('Hawaii', 'polaris-forms'),
			'ID' => __('Idaho', 'polaris-forms'),
			'IL' => __('Illinois', 'polaris-forms'),
			'IN' => __('Indiana', 'polaris-forms'),
			'IA' => __('Iowa', 'polaris-forms'),
			'KS' => __('Kansas', 'polaris-forms'),
			'KY' => __('Kentucky', 'polaris-forms'),
			'LA' => __('Louisiana', 'polaris-forms'),
			'ME' => __('Maine', 'polaris-forms'),
			'MD' => __('Maryland', 'polaris-forms'),
			'MA' => __('Massachusetts', 'polaris-forms'),
			'MI' => __('Michigan', 'polaris-forms'),
			'MN' => __('Minnesota', 'polaris-forms'),
			'MS' => __('Mississippi', 'polaris-forms'),
			'MO' => __('Missouri', 'polaris-forms'),
			'OK' => __('Oklahoma', 'polaris-forms'),
			'OR' => __('Oregon', 'polaris-forms'),
			'PA' => __('Pennsylvania', 'polaris-forms'),
			'RI' => __('Rhode Island', 'polaris-forms'),
			'SC' => __('South Carolina', 'polaris-forms'),
			'SD' => __('South Dakota', 'polaris-forms'),
			'TN' => __('Tennessee', 'polaris-forms'),
			'TX' => __('Texas', 'polaris-forms'),
			'UT' => __('Utah', 'polaris-forms'),
			'VT' => __('Vermont', 'polaris-forms'),
			'VA' => __('Virginia', 'polaris-forms'),
			'WA' => __('Washington', 'polaris-forms'),
			'WV' => __('West Virginia', 'polaris-forms'),
			'WI' => __('Wisconsin', 'polaris-forms'),
			'WY' => __('Wyoming', 'polaris-forms'),
		];
	}
}
