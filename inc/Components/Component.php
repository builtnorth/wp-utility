<?php

/**
 * ------------------------------------------------------------------
 * Component Class
 * ------------------------------------------------------------------
 *
 * This class provides component rendering methods.
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

namespace BuiltNorth\WPUtility\Components;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

class Component
{
	/**
	 * Render an accessible card component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function accessibleCard(...$args)
	{
		return AccessibleCard::render(...$args);
	}

	/**
	 * Render breadcrumbs component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function breadcrumbs(...$args)
	{
		return Breadcrumbs::render(...$args);
	}

	/**
	 * Render a button component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function button(...$args)
	{
		return Button::render(...$args);
	}

	/**
	 * Render an image component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function image(...$args)
	{
		return Image::render(...$args);
	}

	/**
	 * Render pagination component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function pagination(...$args)
	{
		return Pagination::render(...$args);
	}

	/**
	 * Render a post card component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function postCard(...$args)
	{
		return PostCard::render(...$args);
	}

	/**
	 * Render a post feed component.
	 *
	 * @param mixed ...$args Arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function postFeed(...$args)
	{
		return PostFeed::render(...$args);
	}

	// Legacy support for PascalCase methods (PHP method names are case-insensitive)
	// So Component::Image() will work the same as Component::image()
	// Also supports snake_case: Component::post_card() maps to postCard()
	
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
		
		throw new \BadMethodCallException("Component method $name does not exist.");
	}
}