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
	 * Test that Helper uses static methods
	 */
	public function test_helper_uses_static_methods() {
		$this->assertTrue( class_exists( Helper::class ) );
		
		// Helper uses __callStatic for calling helpers
		$this->assertTrue( method_exists( Helper::class, '__callStatic' ) );
	}

	/**
	 * Test that Helper throws exception for non-existent helper
	 */
	public function test_helper_throws_exception_for_nonexistent() {
		$this->expectException( \BadMethodCallException::class );
		$this->expectExceptionMessage( 'Helper nonexistent does not exist.' );
		
		Helper::nonexistent();
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