<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Helpers;

use BuiltNorth\WPUtility\Helpers\PresetColor;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Helpers\PresetColor
 */
class PresetColorTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('sanitize_html_class')->andReturnUsing(
			static function ( string $text ): string {
				return preg_replace( '/[^A-Za-z0-9_-]/', '', $text ) ?? '';
			}
		);
	}

	// -- css_value() -----------------------------------------------------

	public function test_css_value_returns_a_hex_value_as_is(): void {
		$this->assertSame('#ff0000', PresetColor::css_value('#ff0000'));
	}

	public function test_css_value_rejects_an_invalid_hex_breakout_payload(): void {
		$this->assertSame('', PresetColor::css_value('#000;background-image:url(//evil)'));
	}

	public function test_css_value_rejects_a_short_invalid_hex(): void {
		$this->assertSame('', PresetColor::css_value('#gg0000'));
	}

	public function test_css_value_wraps_a_preset_slug_in_a_var_reference(): void {
		$this->assertSame('var( --wp--preset--color--primary )', PresetColor::css_value('primary'));
	}

	public function test_css_value_returns_empty_string_for_empty_input(): void {
		$this->assertSame('', PresetColor::css_value(''));
	}

	// -- css_declaration() -------------------------------------------

	public function test_css_declaration_builds_a_hex_declaration(): void {
		$this->assertSame('--icon-color: #ff0000;', PresetColor::css_declaration('icon-color', '#ff0000'));
	}

	public function test_css_declaration_sanitizes_the_property_name(): void {
		// sanitize_html_class strips `;evil` characters → no CSS breakout.
		$this->assertSame(
			'--icon-colorevil: #ff0000;',
			PresetColor::css_declaration('icon-color;evil', '#ff0000')
		);
	}

	public function test_css_declaration_builds_a_preset_declaration(): void {
		$this->assertSame(
			'--icon-color: var( --wp--preset--color--primary );',
			PresetColor::css_declaration('icon-color', 'primary')
		);
	}

	public function test_css_declaration_returns_empty_string_for_empty_value(): void {
		$this->assertSame('', PresetColor::css_declaration('icon-color', ''));
	}

	public function test_css_declaration_returns_empty_string_for_invalid_hex(): void {
		$this->assertSame('', PresetColor::css_declaration('icon-color', '#not-hex'));
	}

	// -- preset_class() --------------------------------------------------

	public function test_preset_class_builds_a_class_for_a_preset_slug(): void {
		$this->assertSame(
			'has-primary-icon-background-color',
			PresetColor::preset_class('primary', 'icon-background-color')
		);
	}

	public function test_preset_class_returns_empty_string_for_a_hex_value(): void {
		$this->assertSame('', PresetColor::preset_class('#ff0000', 'icon-background-color'));
	}

	public function test_preset_class_returns_empty_string_for_empty_value(): void {
		$this->assertSame('', PresetColor::preset_class('', 'icon-background-color'));
	}
}
