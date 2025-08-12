<?php
/**
 * Tests for the Utility class
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Utility;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * Utility test case
 */
class UtilityTest extends WPMockTestCase {

	/**
	 * Test that Utility uses static methods
	 */
	public function test_utility_uses_static_methods() {
		$this->assertTrue( class_exists( Utility::class ) );
		
		// Utility uses __callStatic for calling utilities
		$this->assertTrue( method_exists( Utility::class, '__callStatic' ) );
	}

	/**
	 * Test that Utility throws exception for non-existent utility
	 */
	public function test_utility_throws_exception_for_nonexistent() {
		$this->expectException( \BadMethodCallException::class );
		$this->expectExceptionMessage( 'Utility method nonexistent does not exist.' );
		
		Utility::nonexistent();
	}

	/**
	 * Test archive URL utility exists
	 */
	public function test_archive_url_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\ArchiveUrl' ) );
	}

	/**
	 * Test country list utility exists
	 */
	public function test_country_list_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\CountryList' ) );
	}

	/**
	 * Test get terms utility exists
	 */
	public function test_get_terms_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\GetTerms' ) );
	}

	/**
	 * Test get title utility exists
	 */
	public function test_get_title_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\GetTitle' ) );
	}

	/**
	 * Test image setup utility exists
	 */
	public function test_image_setup_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\ImageSetup' ) );
	}

	/**
	 * Test lazy load first block utility exists
	 */
	public function test_lazy_load_first_block_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\LazyLoadFirstBlock' ) );
	}

	/**
	 * Test reading time utility exists
	 */
	public function test_reading_time_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\ReadingTime' ) );
	}

	/**
	 * Test state list utility exists
	 */
	public function test_state_list_utility_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Utilities\\StateList' ) );
	}
}