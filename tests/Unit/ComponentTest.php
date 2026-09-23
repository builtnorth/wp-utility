<?php
/**
 * Tests for the Component facade
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Component;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * Component facade test case
 *
 * @covers \BuiltNorth\WPUtility\Component
 */
class ComponentTest extends WPMockTestCase {

	/**
	 * Every component a consumer needs is reachable from this one import.
	 */
	public function test_facade_exposes_the_full_component_surface() {
		foreach ( [
			'accessible_card',
			'breadcrumbs',
			'get_breadcrumb_data',
			'button',
			'resolve_screen_reader_text',
			'get_generic_link_labels',
			'is_generic_link_label',
			'image',
			'sizes',
			'reset_content_width',
			'pagination',
			'post_type_landing_url',
			'get_rewrite_slug',
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
	 * No forwarding method takes ...$args.
	 *
	 * A variadic reports `mixed ...$args` to static analysis and silently accepts
	 * named arguments the leaf has never declared; the README documented one such
	 * call. Guarding it here keeps the contract visible.
	 */
	public function test_no_forwarding_method_is_variadic() {
		$r = new \ReflectionClass( Component::class );
		foreach ( $r->getMethods( \ReflectionMethod::IS_PUBLIC ) as $m ) {
			$this->assertFalse( $m->isVariadic(), "Component::{$m->getName()}() is variadic" );
		}
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
