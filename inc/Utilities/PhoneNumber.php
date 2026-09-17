<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Utilities;

/**
 * ------------------------------------------------------------------
 * Phone Number Utility
 * ------------------------------------------------------------------
 *
 * Normalizes phone numbers to E.164 for schema/tel: use and builds
 * tel: links.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */
class PhoneNumber
{
	/**
	 * Normalize a phone number to E.164 format for schema/tel: use.
	 *
	 * Only handles numbers that are already E.164 (leading +) or plain
	 * 10-digit NANP numbers (optionally with a leading 1). Anything else is
	 * returned unchanged rather than guessed at, since a wrong E.164 value
	 * is worse than an un-normalized one in structured data.
	 *
	 * @param string $phone                 Raw phone number.
	 * @param string $default_country_code  Country calling code to prepend to a bare 10-digit number. Default '1' (NANP).
	 */
	public static function to_e164(string $phone, string $default_country_code = '1'): string
	{
		$digits = preg_replace('/[^\d+]/', '', $phone);

		if ($digits === '' || $digits === null) {
			return $phone;
		}

		if (str_starts_with($digits, '+')) {
			return $digits;
		}

		if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
			$digits = substr($digits, 1);
		}

		if (strlen($digits) === 10) {
			return '+' . $default_country_code . $digits;
		}

		return $phone;
	}

	/**
	 * Build a `tel:` link for a phone number.
	 *
	 * The visible label keeps the phone number as given (not the E.164
	 * form) since display formatting is a separate concern from the href.
	 *
	 * @param string $phone Raw phone number.
	 * @return string HTML anchor, or an escaped text fallback when the number has no digits to link.
	 */
	public static function tel_link(string $phone): string
	{
		if ($phone === '') {
			return '';
		}

		$digits = preg_replace('/[^\d+]/', '', $phone);
		$href = $digits !== '' && $digits !== null ? 'tel:' . $digits : '';

		if ($href === '') {
			return esc_html($phone);
		}

		return "<a href='" . esc_attr($href) . "'>" . esc_html($phone) . '</a>';
	}
}
