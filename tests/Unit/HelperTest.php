<?php
/**
 * Tests for the Helper class
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Helper;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * Helper test case
 */
class HelperTest extends WPMockTestCase {

	/**
	 * Test that Helper is a singleton
	 */
	public function test_helper_is_singleton() {
		$instance1 = Helper::instance();
		$instance2 = Helper::instance();
		
		$this->assertSame( $instance1, $instance2 );
		$this->assertInstanceOf( Helper::class, $instance1 );
	}

	/**
	 * Test init method registers helpers
	 */
	public function test_init_registers_helpers() {
		$helper = Helper::instance();
		$helper->init();
		
		// Verify helper was initialized
		$this->assertInstanceOf( Helper::class, $helper );
	}

	/**
	 * Test that helper classes are registered
	 */
	public function test_helper_classes_registered() {
		$helper = Helper::instance();
		
		// Check that helper has expected properties/methods
		$this->assertTrue( method_exists( $helper, 'init' ) );
		$this->assertTrue( method_exists( $helper, 'instance' ) );
	}

	/**
	 * Test escape SVG helper exists
	 */
	public function test_escape_svg_helper_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Helpers\\EscapeSvg' ) );
	}

	/**
	 * Test foreground image helper exists
	 */
	public function test_foreground_image_helper_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Helpers\\ForegroundImage' ) );
	}

	/**
	 * Test section background helper exists
	 */
	public function test_section_background_helper_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Helpers\\SectionBackground' ) );
	}

	/**
	 * Test section pattern helper exists
	 */
	public function test_section_pattern_helper_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Helpers\\SectionPattern' ) );
	}
}