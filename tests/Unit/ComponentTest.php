<?php
/**
 * Tests for the Component class
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Component;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * Component test case
 */
class ComponentTest extends WPMockTestCase {

	/**
	 * Test that Component uses static methods
	 */
	public function test_component_uses_static_methods() {
		$this->assertTrue( class_exists( Component::class ) );
		
		// Component uses __callStatic for rendering
		$this->assertTrue( method_exists( Component::class, '__callStatic' ) );
	}

	/**
	 * Test that Component throws exception for non-existent component
	 */
	public function test_component_throws_exception_for_nonexistent() {
		$this->expectException( \BadMethodCallException::class );
		$this->expectExceptionMessage( 'Component method nonexistent does not exist.' );
		
		Component::nonexistent();
	}

	/**
	 * Test accessible card component exists
	 */
	public function test_accessible_card_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\AccessibleCard' ) );
	}

	/**
	 * Test breadcrumbs component exists
	 */
	public function test_breadcrumbs_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\Breadcrumbs' ) );
	}

	/**
	 * Test button component exists
	 */
	public function test_button_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\Button' ) );
	}

	/**
	 * Test image component exists
	 */
	public function test_image_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\Image' ) );
	}

	/**
	 * Test pagination component exists
	 */
	public function test_pagination_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\Pagination' ) );
	}

}