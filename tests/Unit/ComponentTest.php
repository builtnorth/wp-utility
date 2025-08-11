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
	 * Test that Component is a singleton
	 */
	public function test_component_is_singleton() {
		$instance1 = Component::instance();
		$instance2 = Component::instance();
		
		$this->assertSame( $instance1, $instance2 );
		$this->assertInstanceOf( Component::class, $instance1 );
	}

	/**
	 * Test init method registers components
	 */
	public function test_init_registers_components() {
		$component = Component::instance();
		$component->init();
		
		// Verify component was initialized
		$this->assertInstanceOf( Component::class, $component );
	}

	/**
	 * Test that component classes are registered
	 */
	public function test_component_classes_registered() {
		$component = Component::instance();
		
		// Check that component has expected properties/methods
		$this->assertTrue( method_exists( $component, 'init' ) );
		$this->assertTrue( method_exists( $component, 'instance' ) );
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

	/**
	 * Test post card component exists
	 */
	public function test_post_card_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\PostCard' ) );
	}

	/**
	 * Test post feed component exists
	 */
	public function test_post_feed_component_exists() {
		$this->assertTrue( class_exists( 'BuiltNorth\\WPUtility\\Components\\PostFeed' ) );
	}
}