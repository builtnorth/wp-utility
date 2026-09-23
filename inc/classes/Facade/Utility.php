<?php

/**
 * Utility facade.
 *
 * @package BuiltNorth\WPUtility
 * @since 3.0.0
 */

namespace BuiltNorth\WPUtility\Facade;

use BuiltNorth\WPUtility\Setup\ImageSetup;
use BuiltNorth\WPUtility\Utilities\ArchiveUrl;
use BuiltNorth\WPUtility\Utilities\CountryList;
use BuiltNorth\WPUtility\Utilities\GetTerms;
use BuiltNorth\WPUtility\Utilities\GetTitle;
use BuiltNorth\WPUtility\Utilities\PhoneNumber;
use BuiltNorth\WPUtility\Utilities\ReadingTime;
use BuiltNorth\WPUtility\Utilities\StateList;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

/**
 * Single import for every utility in this package.
 *
 * Lives under `Facade\` so the package root stays free of loose entry-point
 * files while a consumer still reaches the whole utility surface through one
 * `use` statement.
 *
 * The phone methods are exposed here because consumers were importing
 * `Utilities\PhoneNumber` alongside this facade to reach `tel_link()`.
 *
 * Methods here delegate and do not implement.
 */
class Utility
{
	/**
	 * Archive URL for a post type.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function archive_url(...$args)
	{
		return ArchiveUrl::render(...$args);
	}

	/**
	 * Country list.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function country_list(...$args)
	{
		return CountryList::render(...$args);
	}

	/**
	 * Terms for a post.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function get_terms(...$args)
	{
		return GetTerms::render(...$args);
	}

	/**
	 * Resolved title for the current context.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function get_title(...$args)
	{
		return GetTitle::render(...$args);
	}

	/**
	 * Image setup registration.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function image_setup(...$args)
	{
		return ImageSetup::render(...$args);
	}

	/**
	 * Normalize a phone number to E.164.
	 *
	 * @param string $phone                Raw phone number.
	 * @param string $default_country_code Country calling code for a bare 10-digit number. Default '1'.
	 */
	public static function to_e164(string $phone, string $default_country_code = '1'): string
	{
		return PhoneNumber::to_e164($phone, $default_country_code);
	}

	/**
	 * `tel:` anchor for a phone number, or escaped text when it has no linkable digits.
	 */
	public static function tel_link(string $phone): string
	{
		return PhoneNumber::tel_link($phone);
	}

	/**
	 * Estimated reading time.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function reading_time(...$args)
	{
		return ReadingTime::render(...$args);
	}

	/**
	 * State list.
	 *
	 * @param mixed ...$args Arguments forwarded to the utility.
	 * @return mixed
	 */
	public static function state_list(...$args)
	{
		return StateList::render(...$args);
	}
}
