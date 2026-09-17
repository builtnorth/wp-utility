<?php
/**
 * Tests for the EscapeSvg helper
 *
 * @package BuiltNorth\WPUtility\Tests\Unit\Helpers
 */

namespace BuiltNorth\WPUtility\Tests\Unit\Helpers;

use BuiltNorth\WPUtility\Helpers\EscapeSvg;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * EscapeSvg test case.
 *
 * wp_kses() is mocked as a pass-through, matching how other packages in
 * this codebase test code built on it (see
 * polaris/tests/Unit/Assets/LocalizationManagerTest.php) — WP_Mock cannot
 * exercise wp_kses()'s real tag-stripping, so these tests instead verify
 * what EscapeSvg itself is responsible for: the camelCase restoration
 * that undoes wp_kses()'s lowercasing, which runs after wp_kses() returns
 * and is pure PHP.
 */
class EscapeSvgTest extends WPMockTestCase {

	/**
	 * Mock wp_kses() as a pass-through so render() reaches the
	 * case-restoration step under test.
	 */
	private function mock_kses_passthrough() {
		WP_Mock::userFunction( 'wp_kses' )->andReturnUsing(
			static fn( $content, $allowed ) => $content
		);
	}

	/**
	 * render() returns an empty string for empty input without calling
	 * wp_kses() at all.
	 */
	public function test_render_returns_empty_string_for_empty_input() {
		$this->assertSame( '', EscapeSvg::render( '' ) );
		$this->assertSame( '', EscapeSvg::render( null ) );
	}

	/**
	 * wp_kses() lowercases viewBox to viewbox; render() must restore it.
	 * Losing this attribute leaves the SVG with no intrinsic size, so it
	 * renders blank wherever it is drawn as a CSS mask or background image.
	 */
	public function test_render_restores_view_box_casing() {
		$this->mock_kses_passthrough();

		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24"><path d="M0 0"/></svg>';

		$this->assertStringContainsString( 'viewBox="0 0 24 24"', EscapeSvg::render( $svg ) );
		$this->assertStringNotContainsString( 'viewbox=', EscapeSvg::render( $svg ) );
	}

	/**
	 * preserveAspectRatio must survive with correct casing.
	 */
	public function test_render_restores_preserve_aspect_ratio_casing() {
		$this->mock_kses_passthrough();

		$svg = '<svg viewbox="0 0 24 24" preserveaspectratio="xMidYMid meet"><path d="M0 0"/></svg>';

		$this->assertStringContainsString( 'preserveAspectRatio="xMidYMid meet"', EscapeSvg::render( $svg ) );
	}

	/**
	 * Gradient/clip-path element names and their unit attributes must be
	 * restored, not just plain attributes.
	 */
	public function test_render_restores_gradient_element_and_attribute_casing() {
		$this->mock_kses_passthrough();

		$svg = '<svg viewbox="0 0 24 24">'
			. '<lineargradient id="g" gradientunits="userSpaceOnUse">'
			. '<stop offset="0" stop-color="#fff"/>'
			. '</lineargradient>'
			. '<path d="M0 0"/>'
			. '</svg>';

		$result = EscapeSvg::render( $svg );

		$this->assertStringContainsString( '<linearGradient', $result );
		$this->assertStringContainsString( '</linearGradient>', $result );
		$this->assertStringContainsString( 'gradientUnits="userSpaceOnUse"', $result );
		$this->assertStringNotContainsString( '<lineargradient', $result );
		$this->assertStringNotContainsString( 'gradientunits=', $result );
	}

	/**
	 * radialGradient and clipPath get the same treatment as linearGradient.
	 */
	public function test_render_restores_radial_gradient_and_clip_path_casing() {
		$this->mock_kses_passthrough();

		$svg = '<svg viewbox="0 0 24 24">'
			. '<radialgradient id="r"><stop offset="0" stop-color="#000"/></radialgradient>'
			. '<clippath id="c"><path d="M0 0"/></clippath>'
			. '</svg>';

		$result = EscapeSvg::render( $svg );

		$this->assertStringContainsString( '<radialGradient', $result );
		$this->assertStringContainsString( '<clipPath', $result );
		$this->assertStringContainsString( '</clipPath>', $result );
	}

	/**
	 * Restoration is scoped to the attributes/elements EscapeSvg knows
	 * about; it must not touch unrelated attribute values or element
	 * content, even when they happen to contain a matching substring.
	 */
	public function test_render_does_not_rewrite_unrelated_content() {
		$this->mock_kses_passthrough();

		$svg = '<svg viewbox="0 0 24 24" class="viewbox-icon"><title>A viewbox example</title><path d="M0 0" fill="#fff"/></svg>';

		$result = EscapeSvg::render( $svg );

		// The real viewbox attribute is restored...
		$this->assertStringContainsString( 'viewBox="0 0 24 24"', $result );
		// ...but a class value and text content containing the same
		// substring are left alone.
		$this->assertStringContainsString( 'class="viewbox-icon"', $result );
		$this->assertStringContainsString( 'A viewbox example', $result );
	}

	/**
	 * A plain icon with no camelCase-sensitive attributes passes through
	 * unchanged (aside from whatever wp_kses() itself does).
	 */
	public function test_render_leaves_simple_svg_unchanged() {
		$this->mock_kses_passthrough();

		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M0 0" fill="currentColor"/></svg>';

		$this->assertSame( $svg, EscapeSvg::render( $svg ) );
	}
}
