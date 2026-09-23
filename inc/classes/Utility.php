<?php

/**
 * Utility facade.
 *
 * @package BuiltNorth\WPUtility
 * @since 3.0.0
 */

namespace BuiltNorth\WPUtility;

use BuiltNorth\WPUtility\Setup\ImageSetup;
use BuiltNorth\WPUtility\Utilities\ArchiveUrl;
use BuiltNorth\WPUtility\Utilities\BusinessHours;
use BuiltNorth\WPUtility\Utilities\CountryList;
use BuiltNorth\WPUtility\Utilities\FormatAddress;
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
 * A consumer imports this one class and reaches the whole `Utilities\` area
 * through it. If something in that area is only reachable by importing the leaf
 * directly, that is a gap here, not a reason for the consumer to add an import.
 *
 * Methods carry the same names and signatures as the leaf methods they forward
 * to, so there is no second vocabulary to learn and nothing to keep in step.
 */
class Utility
{
	/**
	 * Rewrite pretty taxonomy permalinks to query-string URLs.
	 */
	public static function archive_url(): void
	{
		ArchiveUrl::render();
	}

	/**
	 * Country codes mapped to translated country names.
	 *
	 * @return array<string, string>
	 */
	public static function country_list(): array
	{
		return CountryList::render();
	}

	/**
	 * US state codes mapped to translated state names.
	 *
	 * @return array<string, string>
	 */
	public static function state_list(): array
	{
		return StateList::render();
	}

	/**
	 * Terms for a post, optionally linked and limited to the first.
	 *
	 * @param int|null    $post_id         Post to read terms from. Defaults to the current post.
	 * @param string|null $taxonomy        Taxonomy slug.
	 * @param bool        $taxonomy_link   Whether to wrap each term in a link to its archive.
	 * @param bool        $first_term_only Whether to return only the first term.
	 * @param string|null $class           Class applied to the wrapper.
	 */
	public static function get_terms(
		$post_id = null,
		$taxonomy = null,
		$taxonomy_link = false,
		$first_term_only = false,
		$class = null
	) {
		return GetTerms::render($post_id, $taxonomy, $taxonomy_link, $first_term_only, $class);
	}

	/**
	 * Resolved title for the current context (archive, singular, search, 404).
	 */
	public static function get_title()
	{
		return GetTitle::render();
	}

	/**
	 * Estimated reading time for the current post, in minutes.
	 */
	public static function reading_time(): int
	{
		return ReadingTime::render();
	}

	/**
	 * Register the package's image sizes and related setup.
	 */
	public static function image_setup()
	{
		return ImageSetup::render();
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
	 * Address as a single line, omitting empty parts.
	 */
	public static function format_address(
		string $street = '',
		string $city = '',
		string $state = '',
		string $zip = ''
	): string {
		return FormatAddress::single_line($street, $city, $state, $zip);
	}

	/**
	 * Address as a schema.org PostalAddress array, or null when empty.
	 *
	 * @return array<string, string>|null
	 */
	public static function address_schema(
		string $street = '',
		string $city = '',
		string $state = '',
		string $zip = '',
		string $country = ''
	): ?array {
		return FormatAddress::schema($street, $city, $state, $zip, $country);
	}

	/**
	 * Weekly opening hours as schema.org OpeningHoursSpecification, or null.
	 *
	 * @param array<string, mixed> $hours Weekly hours keyed by day.
	 * @return array<int, mixed>|null
	 */
	public static function weekly_hours_schema(array $hours): ?array
	{
		return BusinessHours::weekly_schema($hours);
	}

	/**
	 * Special/holiday hours as schema.org OpeningHoursSpecification, or null.
	 *
	 * @param array<int, mixed> $entries Special-hours entries.
	 * @return array<int, mixed>|null
	 */
	public static function special_hours_schema(array $entries): ?array
	{
		return BusinessHours::special_schema($entries);
	}
}
