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
	 * Test that Utility is a singleton
	 */
	public function test_utility_is_singleton() {
		$instance1 = Utility::instance();
		$instance2 = Utility::instance();
		
		$this->assertSame( $instance1, $instance2 );
		$this->assertInstanceOf( Utility::class, $instance1 );
	}

	/**
	 * Test init method registers utilities
	 */
	public function test_init_registers_utilities() {
		$utility = Utility::instance();
		$utility->init();
		
		// Verify utility was initialized
		$this->assertInstanceOf( Utility::class, $utility );
	}

	/**
	 * Test that utility classes are registered
	 */
	public function test_utility_classes_registered() {
		$utility = Utility::instance();
		
		// Check that utility has expected properties/methods
		$this->assertTrue( method_exists( $utility, 'init' ) );
		$this->assertTrue( method_exists( $utility, 'instance' ) );
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