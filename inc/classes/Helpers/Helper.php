<?php

/**
 * ------------------------------------------------------------------
 * Helper Class
 * ------------------------------------------------------------------
 *
 * This class provides helper methods for wp-utility.
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

namespace BuiltNorth\WPUtility\Helpers;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

class Helper
{
	/**
	 * Escape SVG content for safe output.
	 * Supports both escapeSvg() and EscapeSvg() for compatibility.
	 *
	 * @param string $svg SVG content to escape.
	 * @return string Escaped SVG content.
	 */
	public static function escapeSvg($svg)
	{
		return EscapeSvg::render($svg);
	}

	/**
	 * Whether a block should render when hideWhenMetaEmpty is enabled.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public static function metaGatedShouldRender(array $attributes, int $post_id): bool
	{
		return MetaGatedRender::should_render($attributes, $post_id);
	}

	/**
	 * @param mixed $value Raw post meta value.
	 */
	public static function isValidIconMeta(mixed $value): bool
	{
		return MetaGatedRender::is_valid_icon_meta($value);
	}

	public static function resolveUrlFromMeta(string $meta_key, int $post_id): string
	{
		return MetaGatedRender::resolve_url_from_meta($meta_key, $post_id);
	}
	
	// Legacy support - PHP method names are case-insensitive
	// So Helper::EscapeSvg() will work the same as Helper::escapeSvg()
	// Also supports snake_case: Helper::escape_svg() maps to escapeSvg()
	
	/**
	 * Magic method to support snake_case method names.
	 *
	 * @param string $name The method name.
	 * @param array $arguments The arguments.
	 * @return mixed The result.
	 */
	public static function __callStatic($name, $arguments)
	{
		// Convert snake_case to camelCase
		$camelCase = lcfirst(str_replace('_', '', ucwords($name, '_')));
		
		if (method_exists(self::class, $camelCase)) {
			return self::$camelCase(...$arguments);
		}
		
		throw new \BadMethodCallException("Helper method $name does not exist.");
	}
}
