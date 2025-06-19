<?php

/**
 * ------------------------------------------------------------------
 * Helper Class
 * ------------------------------------------------------------------
 *
 * This class is used to provide helper functions.
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

namespace BuiltNorth\Utility;

/**
 * Don't load directly.
 */
defined('ABSPATH') || exit;

class Helper
{
	/**
	 * Call a helper function.
	 *
	 * @param string $name The name of the helper.
	 * @param array $arguments The arguments to pass to the helper.
	 * @return mixed The result from the helper.
	 */
	public static function __callStatic($name, $arguments)
	{
		$helperClass = __NAMESPACE__ . '\\Helpers\\' . ucfirst($name);
		if (class_exists($helperClass) && method_exists($helperClass, 'render')) {
			return call_user_func_array([$helperClass, 'render'], $arguments);
		}
		throw new \BadMethodCallException("Helper $name does not exist.");
	}
}
