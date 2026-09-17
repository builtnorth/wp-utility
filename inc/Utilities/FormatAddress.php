<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Utilities;

/**
 * ------------------------------------------------------------------
 * Format Address Utility
 * ------------------------------------------------------------------
 *
 * Builds a single-line display address and a schema.org PostalAddress
 * array from street/city/state/zip/country parts.
 *
 * Country-code sanitization (e.g. ISO 3166-1 alpha-2 validation) is
 * intentionally out of scope — callers pass through whatever country
 * value they already have.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */
class FormatAddress
{
	/**
	 * Build a single-line display address, e.g. "123 Main St, Springfield, IL 62704".
	 *
	 * Empty parts are skipped rather than leaving stray punctuation.
	 */
	public static function single_line(
		string $street = '',
		string $city = '',
		string $state = '',
		string $zip = ''
	): string {
		$parts = [];

		if ($street !== '') {
			$parts[] = $street;
		}

		$city_state_zip = $city;

		if ($state !== '') {
			$city_state_zip = $city_state_zip !== '' ? $city_state_zip . ', ' . $state : $state;
		}

		if ($zip !== '') {
			$city_state_zip = $city_state_zip !== '' ? $city_state_zip . ' ' . $zip : $zip;
		}

		if ($city_state_zip !== '') {
			$parts[] = $city_state_zip;
		}

		return implode(', ', $parts);
	}

	/**
	 * Build a schema.org PostalAddress array.
	 *
	 * Returns null when every part is empty, so callers can omit the
	 * `address` property entirely rather than emitting an empty node.
	 *
	 * @return array<string, string>|null
	 */
	public static function schema(
		string $street = '',
		string $city = '',
		string $state = '',
		string $zip = '',
		string $country = ''
	): ?array {
		if ($street === '' && $city === '' && $state === '' && $zip === '' && $country === '') {
			return null;
		}

		$address = ['@type' => 'PostalAddress'];

		if ($street !== '') {
			$address['streetAddress'] = $street;
		}
		if ($city !== '') {
			$address['addressLocality'] = $city;
		}
		if ($state !== '') {
			$address['addressRegion'] = $state;
		}
		if ($zip !== '') {
			$address['postalCode'] = $zip;
		}
		if ($country !== '') {
			$address['addressCountry'] = $country;
		}

		return $address;
	}
}
