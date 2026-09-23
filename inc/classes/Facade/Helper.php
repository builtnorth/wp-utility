<?php

/**
 * Helper facade.
 *
 * @package BuiltNorth\WPUtility
 * @since 3.0.0
 */

namespace BuiltNorth\WPUtility\Facade;

use BuiltNorth\WPUtility\Helpers\EscapeSvg;
use BuiltNorth\WPUtility\Helpers\MetaGatedRender;
use BuiltNorth\WPUtility\Helpers\PresetColor;
use WP_Block;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

/**
 * Single import for every helper in this package.
 *
 * Lives under `Facade\` so the package root stays free of loose entry-point
 * files while a consumer still reaches the whole helper surface through one
 * `use` statement.
 *
 * The preset-color and post-id methods are exposed here because consumers were
 * importing `Helpers\PresetColor` and `Helpers\MetaGatedRender` alongside this
 * facade to reach them — `resolve_post_id()` alone had twelve call sites. A
 * facade that forces a second import for the most-used helper in the package
 * is not doing its job.
 *
 * Methods here delegate and do not implement.
 */
class Helper
{
	/**
	 * Escape SVG markup for safe output.
	 *
	 * @param string $svg SVG content to escape.
	 */
	public static function escape_svg($svg)
	{
		return EscapeSvg::render($svg);
	}

	/**
	 * Whether a block should render when hideWhenMetaEmpty is enabled.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public static function should_render(array $attributes, int $post_id): bool
	{
		return MetaGatedRender::should_render($attributes, $post_id);
	}

	/**
	 * Whether a post meta value is empty for gating purposes.
	 */
	public static function is_post_meta_empty(string $meta_key, int $post_id): bool
	{
		return MetaGatedRender::is_post_meta_empty($meta_key, $post_id);
	}

	/**
	 * @param mixed $value Raw post meta value.
	 */
	public static function is_valid_icon_meta(mixed $value): bool
	{
		return MetaGatedRender::is_valid_icon_meta($value);
	}

	/**
	 * Resolve a URL stored in post meta.
	 */
	public static function resolve_url_from_meta(string $meta_key, int $post_id): string
	{
		return MetaGatedRender::resolve_url_from_meta($meta_key, $post_id);
	}

	/**
	 * Resolve the post id a block should read meta from.
	 *
	 * Falls back to the queried object when the block carries no context.
	 */
	public static function resolve_post_id(?WP_Block $block = null): int
	{
		return MetaGatedRender::resolve_post_id($block);
	}

	/**
	 * Sanitized CSS value for a preset colour or hex literal.
	 */
	public static function css_value(string $value): string
	{
		return PresetColor::css_value($value);
	}

	/**
	 * Full `property: value;` declaration for a preset colour, or '' when unusable.
	 */
	public static function css_declaration(string $property, string $value): string
	{
		return PresetColor::css_declaration($property, $value);
	}

	/**
	 * Preset colour class name, or '' for a hex literal that has no preset class.
	 */
	public static function preset_class(string $value, string $suffix): string
	{
		return PresetColor::preset_class($value, $suffix);
	}
}
