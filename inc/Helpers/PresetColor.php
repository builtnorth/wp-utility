<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Helpers;

/**
 * ------------------------------------------------------------------
 * Preset Color Helper
 * ------------------------------------------------------------------
 *
 * Resolves a block attribute that may hold either a raw hex value
 * (from the custom color picker) or a theme.json preset color slug
 * into a usable CSS value / class name.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Helpers
 * @since 1.0.0
 */
class PresetColor
{
	/**
	 * Resolve a color attribute to a CSS value.
	 *
	 * A hex value (leading `#`) is escaped and used as-is; anything else is
	 * treated as a theme.json preset slug and wrapped in a `var()` custom
	 * property reference. Both branches use sanitize_html_class() — a hex
	 * value never reaches it (it's excluded by the `#` check), and a preset
	 * slug is exactly the kind of value that function is for; esc_attr()
	 * alone does not strip characters that are invalid in a CSS custom
	 * property name.
	 *
	 * @param string $value Attribute value, e.g. '#ff0000' or 'primary'.
	 * @return string CSS value, e.g. '#ff0000' or 'var( --wp--preset--color--primary )'. Empty string when $value is empty.
	 */
	public static function css_value(string $value): string
	{
		if ($value === '') {
			return '';
		}

		if (str_starts_with($value, '#')) {
			return esc_attr($value);
		}

		return 'var( --wp--preset--color--' . sanitize_html_class($value) . ' )';
	}

	/**
	 * Build the `--{$property}: {value};` declaration for a color attribute.
	 *
	 * @param string $property CSS custom property name, without the leading `--`.
	 * @param string $value    Attribute value, e.g. '#ff0000' or 'primary'.
	 * @return string The declaration, or an empty string when $value is empty.
	 */
	public static function css_declaration(string $property, string $value): string
	{
		$css_value = self::css_value($value);

		return $css_value !== '' ? "--{$property}: {$css_value};" : '';
	}

	/**
	 * Build the `has-{slug}-{$suffix}` class for a preset color attribute.
	 *
	 * Returns an empty string for a hex value — that class naming convention
	 * only applies to named presets, core's own block supports do the same.
	 *
	 * @param string $value  Attribute value, e.g. '#ff0000' or 'primary'.
	 * @param string $suffix Class suffix, e.g. 'icon-background-color'.
	 * @return string e.g. 'has-primary-icon-background-color', or ''.
	 */
	public static function preset_class(string $value, string $suffix): string
	{
		if ($value === '' || str_starts_with($value, '#')) {
			return '';
		}

		return 'has-' . sanitize_html_class($value) . '-' . $suffix;
	}
}
