<?php
/**
 * Tests for the Helper facade
 *
 * @package BuiltNorth\WPUtility\Tests\Unit
 */

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\Helper;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * Helper facade test case
 *
 * @covers \BuiltNorth\WPUtility\Helper
 */
class HelperTest extends WPMockTestCase {

	/**
	 * Every helper a consumer needs is reachable from this one import.
	 *
	 * Guards the reason the facade exists: consumers previously had to import
	 * Helpers\PresetColor and Helpers\MetaGatedRender alongside it to reach
	 * methods it did not expose.
	 */
	public function test_facade_exposes_the_full_helper_surface() {
		foreach ( [
			'escape_svg',
			'should_render',
			'is_post_meta_empty',
			'is_valid_icon_meta',
			'resolve_url_from_meta',
			'resolve_post_id',
			'css_value',
			'css_declaration',
			'preset_class',
		] as $method ) {
			$this->assertTrue(
				method_exists( Helper::class, $method ),
				"Helper::{$method}() is missing from the facade"
			);
		}
	}

	/**
	 * The facade delegates and does not implement.
	 *
	 * A missing method is now a fatal rather than a BadMethodCallException from
	 * __callStatic, which was removed: no consumer used the snake_case names it
	 * translated, and it hid typos from static analysis.
	 */
	public function test_facade_does_not_define_call_static() {
		$this->assertFalse( method_exists( Helper::class, '__callStatic' ) );
	}

	/**
	 * No forwarding method takes ...$args. See ComponentTest for why.
	 */
	public function test_no_forwarding_method_is_variadic() {
		$r = new \ReflectionClass( Helper::class );
		foreach ( $r->getMethods( \ReflectionMethod::IS_PUBLIC ) as $m ) {
			$this->assertFalse( $m->isVariadic(), "Helper::{$m->getName()}() is variadic" );
		}
	}

	/**
	 * preset colour declarations delegate to PresetColor.
	 *
	 * The declaration is a CSS custom property (`--color:`), not a plain
	 * `color:` rule — the leading `--` comes from PresetColor and the facade
	 * must not reshape it.
	 */
	public function test_preset_color_declaration_delegates() {
		\WP_Mock::userFunction( 'sanitize_html_class', [
			'return' => function ( $v ) {
				return preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $v );
			},
		] );
		\WP_Mock::userFunction( 'esc_attr', [
			'return' => function ( $v ) {
				return $v;
			},
		] );

		$this->assertSame(
			'--color: #ffffff;',
			Helper::css_declaration( 'color', '#ffffff' )
		);
	}

	/**
	 * A hex literal has no preset class.
	 */
	public function test_preset_color_class_returns_empty_for_hex() {
		$this->assertSame( '', Helper::preset_class( '#ffffff', 'background' ) );
	}
}
