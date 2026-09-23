<?php
/**
 * Tests for the Utility facade
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Facade\Utility;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * Utility facade test case
 *
 * @covers \BuiltNorth\WPUtility\Facade\Utility
 */
class UtilityTest extends WPMockTestCase {

	/**
	 * Every utility a consumer needs is reachable from this one import.
	 *
	 * Guards the reason the facade exists: consumers previously had to import
	 * Utilities\PhoneNumber alongside it to reach tel_link().
	 */
	public function test_facade_exposes_the_full_utility_surface() {
		foreach ( [
			'archive_url',
			'country_list',
			'get_terms',
			'get_title',
			'image_setup',
			'to_e164',
			'tel_link',
			'reading_time',
			'state_list',
		] as $method ) {
			$this->assertTrue(
				method_exists( Utility::class, $method ),
				"Utility::{$method}() is missing from the facade"
			);
		}
	}

	/**
	 * __callStatic was removed; a missing method is a fatal, not a swallowed call.
	 */
	public function test_facade_does_not_define_call_static() {
		$this->assertFalse( method_exists( Utility::class, '__callStatic' ) );
	}

	/**
	 * to_e164() delegates to PhoneNumber and keeps its typed signature.
	 */
	public function test_phone_to_e164_delegates() {
		$this->assertSame( '+15551234567', Utility::to_e164( '(555) 123-4567' ) );
	}
}
