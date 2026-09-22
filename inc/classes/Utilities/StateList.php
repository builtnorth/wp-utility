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
			'AL' => __('Alabama', 'wp-utility'),
			'AK' => __('Alaska', 'wp-utility'),
			'AZ' => __('Arizona', 'wp-utility'),
			'AR' => __('Arkansas', 'wp-utility'),
			'CA' => __('California', 'wp-utility'),
			'CO' => __('Colorado', 'wp-utility'),
			'CT' => __('Connecticut', 'wp-utility'),
			'DE' => __('Delaware', 'wp-utility'),
			'DC' => __('District of Columbia', 'wp-utility'),
			'FL' => __('Florida', 'wp-utility'),
			'GA' => __('Georgia', 'wp-utility'),
			'HI' => __('Hawaii', 'wp-utility'),
			'ID' => __('Idaho', 'wp-utility'),
			'IL' => __('Illinois', 'wp-utility'),
			'IN' => __('Indiana', 'wp-utility'),
			'IA' => __('Iowa', 'wp-utility'),
			'KS' => __('Kansas', 'wp-utility'),
			'KY' => __('Kentucky', 'wp-utility'),
			'LA' => __('Louisiana', 'wp-utility'),
			'ME' => __('Maine', 'wp-utility'),
			'MD' => __('Maryland', 'wp-utility'),
			'MA' => __('Massachusetts', 'wp-utility'),
			'MI' => __('Michigan', 'wp-utility'),
			'MN' => __('Minnesota', 'wp-utility'),
			'MS' => __('Mississippi', 'wp-utility'),
			'MO' => __('Missouri', 'wp-utility'),
			'MT' => __('Montana', 'wp-utility'),
			'NE' => __('Nebraska', 'wp-utility'),
			'NV' => __('Nevada', 'wp-utility'),
			'NH' => __('New Hampshire', 'wp-utility'),
			'NJ' => __('New Jersey', 'wp-utility'),
			'NM' => __('New Mexico', 'wp-utility'),
			'NY' => __('New York', 'wp-utility'),
			'NC' => __('North Carolina', 'wp-utility'),
			'ND' => __('North Dakota', 'wp-utility'),
			'OH' => __('Ohio', 'wp-utility'),
			'OK' => __('Oklahoma', 'wp-utility'),
			'OR' => __('Oregon', 'wp-utility'),
			'PA' => __('Pennsylvania', 'wp-utility'),
			'RI' => __('Rhode Island', 'wp-utility'),
			'SC' => __('South Carolina', 'wp-utility'),
			'SD' => __('South Dakota', 'wp-utility'),
			'TN' => __('Tennessee', 'wp-utility'),
			'TX' => __('Texas', 'wp-utility'),
			'UT' => __('Utah', 'wp-utility'),
			'VT' => __('Vermont', 'wp-utility'),
			'VA' => __('Virginia', 'wp-utility'),
			'WA' => __('Washington', 'wp-utility'),
			'WV' => __('West Virginia', 'wp-utility'),
			'WI' => __('Wisconsin', 'wp-utility'),
			'WY' => __('Wyoming', 'wp-utility'),
		];
	}
}
