<?php

/**
 * ------------------------------------------------------------------
 * Utility Class
 * ------------------------------------------------------------------
 *
 * This class provides utility methods.
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

namespace BuiltNorth\WPUtility\Utilities;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

class Utility
{
	/**
	 * Get archive URL utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function archiveUrl(...$args)
	{
		return ArchiveUrl::render(...$args);
	}

	/**
	 * Get country list utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function countryList(...$args)
	{
		return CountryList::render(...$args);
	}

	/**
	 * Get terms utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function getTerms(...$args)
	{
		return GetTerms::render(...$args);
	}

	/**
	 * Get title utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function getTitle(...$args)
	{
		return GetTitle::render(...$args);
	}

	/**
	 * Image setup utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function imageSetup(...$args)
	{
		return \BuiltNorth\WPUtility\Setup\ImageSetup::render(...$args);
	}

	/**
	 * Lazy load first block utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function lazyLoadFirstBlock(...$args)
	{
		return LazyLoadFirstBlock::render(...$args);
	}

	/**
	 * Reading time utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function readingTime(...$args)
	{
		return ReadingTime::render(...$args);
	}

	/**
	 * Get state list utility.
	 *
	 * @param mixed ...$args Arguments to pass to the utility.
	 * @return mixed The utility result.
	 */
	public static function stateList(...$args)
	{
		return StateList::render(...$args);
	}

	// Legacy support - PHP method names are case-insensitive
	// So Utility::GetTitle() will work the same as Utility::getTitle()
	// Also supports snake_case: Utility::get_title() maps to getTitle()
	
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
		
		throw new \BadMethodCallException("Utility method $name does not exist.");
	}
}