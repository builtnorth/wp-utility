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
defined('ABSPATH') || exit;

class Utility
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
		// Convert get_title to GetTitle
		$className = str_replace('_', '', ucwords($name, '_'));
		$utilityClass = __NAMESPACE__ . '\\Utilities\\' . $className;

		if (class_exists($utilityClass)) {
			return call_user_func_array([$utilityClass, 'render'], $arguments);
		}
		throw new \BadMethodCallException("Utility $name does not exist.");
	}
}
