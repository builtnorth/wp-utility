<?php
/**
 * Tests for the Component facade
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Facade\Component;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * Component facade test case
 *
 * @covers \BuiltNorth\WPUtility\Facade\Component
 */
class ComponentTest extends WPMockTestCase {

	/**
	 * Every component a consumer needs is reachable from this one import.
	 */
	public function test_facade_exposes_the_full_component_surface() {
		foreach ( [
			'accessible_card',
			'breadcrumbs',
			'button',
			'resolve_screen_reader_text',
			'image',
			'sizes',
			'pagination',
		] as $method ) {
			$this->assertTrue(
				method_exists( Component::class, $method ),
				"Component::{$method}() is missing from the facade"
			);
		}
	}

	/**
	 * __callStatic was removed; a missing method is a fatal, not a swallowed call.
	 */
	public function test_facade_does_not_define_call_static() {
		$this->assertFalse( method_exists( Component::class, '__callStatic' ) );
	}

	/**
	 * sizes() delegates to Image and keeps its typed signature.
	 */
	public function test_sizes_delegates_to_image() {
		\WP_Mock::userFunction( 'wp_get_global_settings', [
			'return' => [ 'layout' => [ 'contentSize' => '800px' ] ],
		] );

		$this->assertSame( '100vw', Component::sizes( 100 ) );
	}
}
