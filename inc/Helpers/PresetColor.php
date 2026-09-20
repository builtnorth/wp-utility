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
	 * Strict hex color pattern: #RGB, #RGBA, #RRGGBB, or #RRGGBBAA.
	 */
	private const HEX_PATTERN = '/^#(?:[0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/';

	/**
	 * Resolve a color attribute to a CSS value.
	 *
	 * A leading `#` is treated as a hex color and accepted only when it matches
	 * a strict hex pattern (rejects CSS breakout payloads). Anything else is
	 * treated as a theme.json preset slug and wrapped in a `var()` custom
	 * property reference via sanitize_html_class().
	 *
	 * @param string $value Attribute value, e.g. '#ff0000' or 'primary'.
	 * @return string CSS value, e.g. '#ff0000' or 'var( --wp--preset--color--primary )'. Empty string when $value is empty or an invalid hex.
	 */
	public static function css_value(string $value): string
	{
		if ($value === '') {
			return '';
		}

		if (str_starts_with($value, '#')) {
			return preg_match(self::HEX_PATTERN, $value) === 1
				? esc_attr($value)
				: '';
		}

		return 'var( --wp--preset--color--' . sanitize_html_class($value) . ' )';
	}

	/**
	 * Build the `--{$property}: {value};` declaration for a color attribute.
	 *
	 * @param string $property CSS custom property name, without the leading `--`.
	 * @param string $value    Attribute value, e.g. '#ff0000' or 'primary'.
	 * @return string The declaration, or an empty string when $value is empty/invalid.
	 */
	public static function css_declaration(string $property, string $value): string
	{
		$css_value = self::css_value($value);
		$property  = sanitize_html_class($property);

		if ($css_value === '' || $property === '') {
			return '';
		}

		return "--{$property}: {$css_value};";
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

		$suffix = sanitize_html_class($suffix);
		if ($suffix === '') {
			return '';
		}

		return 'has-' . sanitize_html_class($value) . '-' . $suffix;
	}
}
