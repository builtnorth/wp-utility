<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Utilities;

/**
 * ------------------------------------------------------------------
 * Business Hours Utility
 * ------------------------------------------------------------------
 *
 * Builds schema.org OpeningHoursSpecification arrays from a weekly-hours
 * config and a list of date-range special-hours entries. Covers only the
 * data shaping shared by every consumer; live "is it open right now"
 * status resolution (with labels/timezone display/etc.) is a larger,
 * plugin-specific concern and stays out of this class.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */
class BusinessHours
{
	private const DAYS_OF_WEEK = [
		'monday'    => 'Monday',
		'tuesday'   => 'Tuesday',
		'wednesday' => 'Wednesday',
		'thursday'  => 'Thursday',
		'friday'    => 'Friday',
		'saturday'  => 'Saturday',
		'sunday'    => 'Sunday',
	];

	/**
	 * Build weekly OpeningHoursSpecification entries.
	 *
	 * Expects the shape:
	 * ```
	 * [
	 *   'enabled'     => bool,
	 *   'always_open' => bool,
	 *   'monday'      => ['closed' => bool, 'open_24_hours' => bool, 'open_time' => string, 'close_time' => string],
	 *   ... one entry per day key in DAYS_OF_WEEK ...
	 * ]
	 * ```
	 *
	 * @param array<string, mixed> $hours
	 * @return list<array<string, mixed>>|null Null when hours are disabled or every day is closed.
	 */
	public static function weekly_schema(array $hours): ?array
	{
		if (empty($hours['enabled'])) {
			return null;
		}

		if (! empty($hours['always_open'])) {
			return [
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array_values(self::DAYS_OF_WEEK),
					'opens'     => '00:00',
					'closes'    => '23:59',
				],
			];
		}

		$opening_hours = [];

		foreach (self::DAYS_OF_WEEK as $day_key => $schema_day) {
			if (! isset($hours[$day_key])) {
				continue;
			}

			$day_hours = $hours[$day_key];

			if (! empty($day_hours['closed'])) {
				continue;
			}

			if (! empty($day_hours['open_24_hours'])) {
				$opening_hours[] = [
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => $schema_day,
					'opens'     => '00:00',
					'closes'    => '23:59',
				];
				continue;
			}

			if (! empty($day_hours['open_time']) && ! empty($day_hours['close_time'])) {
				$opening_hours[] = [
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => $schema_day,
					'opens'     => self::format_time_for_schema((string) $day_hours['open_time']),
					'closes'    => self::format_time_for_schema((string) $day_hours['close_time']),
				];
			}
		}

		return $opening_hours !== [] ? $opening_hours : null;
	}

	/**
	 * Build date-range OpeningHoursSpecification entries (holidays, seasonal hours, etc).
	 *
	 * Each entry's date_from/date_to is MM-DD (year-less, since these recur
	 * annually); the current year is applied, rolling to next year when the
	 * range has already passed so a stale entry doesn't emit a validThrough
	 * date in the past.
	 *
	 * @param array<int, array<string, mixed>> $entries Each: date_from, date_to (MM-DD), opens, closes, closed, label.
	 * @return list<array<string, mixed>>|null Null when no entry produced a usable range.
	 */
	public static function special_schema(array $entries): ?array
	{
		$output = [];
		$today  = (int) wp_date('Ymd');

		foreach ($entries as $entry) {
			$raw_from = trim((string) ($entry['date_from'] ?? ''));
			$raw_to   = trim((string) ($entry['date_to'] ?? ''));

			if ($raw_from === '' || $raw_to === '') {
				continue;
			}

			$raw_from = self::pad_month_day($raw_from);
			$raw_to   = self::pad_month_day($raw_to);

			if (! preg_match('/^\d{2}-\d{2}$/', $raw_from) || ! preg_match('/^\d{2}-\d{2}$/', $raw_to)) {
				continue;
			}

			$year      = (int) wp_date('Y');
			$date_from = "{$year}-{$raw_from}";
			$date_to   = "{$year}-{$raw_to}";

			if ((int) str_replace('-', '', $date_to) < $today) {
				$year     += 1;
				$date_from = "{$year}-{$raw_from}";
				$date_to   = "{$year}-{$raw_to}";
			}

			$closed = ! empty($entry['closed']);
			$opens  = $closed ? '00:00' : self::format_time_for_schema((string) ($entry['opens'] ?? '09:00'));
			$closes = $closed ? '00:00' : self::format_time_for_schema((string) ($entry['closes'] ?? '17:00'));

			$spec = [
				'@type'        => 'OpeningHoursSpecification',
				'validFrom'    => $date_from,
				'validThrough' => $date_to,
				'opens'        => $opens,
				'closes'       => $closes,
			];

			$label = trim((string) ($entry['label'] ?? ''));
			if ($label !== '') {
				$spec['name'] = $label;
			}

			$output[] = $spec;
		}

		return $output !== [] ? $output : null;
	}

	/**
	 * Zero-pad a loosely-formatted MM-DD string (e.g. "3-5" -> "03-05").
	 *
	 * Values already in another shape (or not MM-DD at all) pass through
	 * unchanged so the caller's own validation catches them.
	 */
	public static function pad_month_day(string $date): string
	{
		if ($date === '') {
			return '';
		}

		if (preg_match('/^(\d{1,2})-(\d{1,2})$/', $date, $matches)) {
			return sprintf('%02d-%02d', (int) $matches[1], (int) $matches[2]);
		}

		return $date;
	}

	/**
	 * Normalize a time value to H:i for schema output.
	 *
	 * Already-H:i values pass through as-is; anything else is parsed with
	 * strtotime() and reformatted. Unparseable input falls back to '00:00'
	 * rather than propagating a schema-invalid value.
	 */
	public static function format_time_for_schema(string $time): string
	{
		if (preg_match('/^\d{2}:\d{2}$/', $time)) {
			return $time;
		}

		$timestamp = strtotime($time);

		return $timestamp !== false ? date('H:i', $timestamp) : '00:00';
	}
}
