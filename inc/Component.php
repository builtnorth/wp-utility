<?php

/**
 * ------------------------------------------------------------------
 * Component Class
 * ------------------------------------------------------------------
 *
 * This class is used to render components.
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

namespace BuiltNorth\Utility;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

class Component
{
	/**
	 * Render a component.
	 *
	 * @param string $name The name of the component.
	 * @param array $arguments The arguments to pass to the component.
	 * @return string The rendered component.
	 */
	public static function __callStatic($name, $arguments)
	{
		$componentClass = __NAMESPACE__ . '\\Components\\' . ucfirst($name);
		if (class_exists($componentClass) && method_exists($componentClass, 'render')) {
			return call_user_func_array([$componentClass, 'render'], $arguments);
		}
		throw new \BadMethodCallException("Component $name does not exist.");
	}
}
