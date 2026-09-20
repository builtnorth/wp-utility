<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\BusinessHours;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\BusinessHours
 */
class BusinessHoursTest extends WPMockTestCase {

	// -- weekly_schema() -------------------------------------------------

	public function test_weekly_schema_returns_null_when_disabled(): void {
		$this->assertNull(BusinessHours::weekly_schema(['enabled' => false]));
	}

	public function test_weekly_schema_returns_null_when_not_specified(): void {
		$this->assertNull(BusinessHours::weekly_schema([]));
	}

	public function test_weekly_schema_always_open_covers_all_seven_days(): void {
		$this->assertSame(
			[
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
					'opens'     => '00:00',
					'closes'    => '23:59',
				],
			],
			BusinessHours::weekly_schema(['enabled' => true, 'always_open' => true])
		);
	}

	public function test_weekly_schema_builds_an_entry_per_open_day(): void {
		$result = BusinessHours::weekly_schema([
			'enabled' => true,
			'monday'  => ['open_time' => '09:00', 'close_time' => '17:00'],
			'tuesday' => ['closed' => true],
		]);

		$this->assertSame(
			[
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => 'Monday',
					'opens'     => '09:00',
					'closes'    => '17:00',
				],
			],
			$result
		);
	}

	public function test_weekly_schema_skips_a_closed_day(): void {
		$this->assertNull(BusinessHours::weekly_schema([
			'enabled' => true,
			'monday'  => ['closed' => true],
		]));
	}

	public function test_weekly_schema_handles_open_24_hours_for_a_single_day(): void {
		$result = BusinessHours::weekly_schema([
			'enabled' => true,
			'friday'  => ['open_24_hours' => true],
		]);

		$this->assertSame(
			[
				[
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => 'Friday',
					'opens'     => '00:00',
					'closes'    => '23:59',
				],
			],
			$result
		);
	}

	public function test_weekly_schema_skips_a_day_missing_both_open_and_close_time(): void {
		$this->assertNull(BusinessHours::weekly_schema([
			'enabled' => true,
			'monday'  => ['open_time' => '09:00'],
		]));
	}

	public function test_weekly_schema_ignores_an_unrecognized_day_key(): void {
		$this->assertNull(BusinessHours::weekly_schema([
			'enabled'  => true,
			'holiday'  => ['open_time' => '09:00', 'close_time' => '17:00'],
		]));
	}

	public function test_weekly_schema_parses_a_non_h_i_time_via_strtotime(): void {
		$result = BusinessHours::weekly_schema([
			'enabled' => true,
			'monday'  => ['open_time' => '9am', 'close_time' => '5pm'],
		]);

		$this->assertSame('09:00', $result[0]['opens']);
		$this->assertSame('17:00', $result[0]['closes']);
	}

	// -- special_schema() --------------------------------------------

	public function test_special_schema_returns_null_for_empty_entries(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');

		$this->assertNull(BusinessHours::special_schema([]));
	}

	public function test_special_schema_skips_entries_missing_a_date(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');

		$this->assertNull(BusinessHours::special_schema([
			['date_from' => '', 'date_to' => '12-25'],
		]));
	}

	public function test_special_schema_pads_single_digit_month_and_day(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '3-5', 'date_to' => '3-5', 'opens' => '10:00', 'closes' => '14:00'],
		]);

		$this->assertSame('2026-03-05', $result[0]['validFrom']);
		$this->assertSame('2026-03-05', $result[0]['validThrough']);
	}

	public function test_special_schema_rolls_a_past_range_to_next_year(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260601');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '01-01', 'date_to' => '01-02', 'opens' => '10:00', 'closes' => '14:00'],
		]);

		$this->assertSame('2027-01-01', $result[0]['validFrom']);
		$this->assertSame('2027-01-02', $result[0]['validThrough']);
	}

	public function test_special_schema_closed_entry_omits_opens_and_closes(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '12-25', 'date_to' => '12-25', 'closed' => true],
		]);

		$this->assertArrayNotHasKey('opens', $result[0]);
		$this->assertArrayNotHasKey('closes', $result[0]);
	}

	public function test_special_schema_cross_year_range_extends_valid_through(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260601');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '12-20', 'date_to' => '01-05', 'opens' => '10:00', 'closes' => '14:00'],
		]);

		$this->assertSame('2026-12-20', $result[0]['validFrom']);
		$this->assertSame('2027-01-05', $result[0]['validThrough']);
	}

	public function test_special_schema_defaults_open_hours_when_not_closed_and_no_times_given(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '12-25', 'date_to' => '12-25'],
		]);

		$this->assertSame('09:00', $result[0]['opens']);
		$this->assertSame('17:00', $result[0]['closes']);
	}

	public function test_special_schema_includes_a_label_when_given(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '12-25', 'date_to' => '12-25', 'closed' => true, 'label' => 'Christmas'],
		]);

		$this->assertSame('Christmas', $result[0]['name']);
	}

	public function test_special_schema_omits_name_key_when_label_is_blank(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');
		WP_Mock::userFunction('wp_date')
			->with('Y')
			->andReturn('2026');

		$result = BusinessHours::special_schema([
			['date_from' => '12-25', 'date_to' => '12-25', 'closed' => true],
		]);

		$this->assertArrayNotHasKey('name', $result[0]);
	}

	public function test_special_schema_skips_an_unparseable_date(): void {
		WP_Mock::userFunction('wp_date')
			->with('Ymd')
			->andReturn('20260101');

		$this->assertNull(BusinessHours::special_schema([
			['date_from' => 'not-a-date', 'date_to' => '12-25'],
		]));
	}

	// -- pad_month_day() -------------------------------------------------

	public function test_pad_month_day_pads_single_digits(): void {
		$this->assertSame('03-05', BusinessHours::pad_month_day('3-5'));
	}

	public function test_pad_month_day_leaves_already_padded_values_unchanged(): void {
		$this->assertSame('12-25', BusinessHours::pad_month_day('12-25'));
	}

	public function test_pad_month_day_leaves_unrecognized_input_unchanged(): void {
		$this->assertSame('not-a-date', BusinessHours::pad_month_day('not-a-date'));
	}

	public function test_pad_month_day_returns_empty_string_for_empty_input(): void {
		$this->assertSame('', BusinessHours::pad_month_day(''));
	}

	// -- format_time_for_schema() -----------------------------------

	public function test_format_time_for_schema_passes_through_h_i_values(): void {
		$this->assertSame('09:30', BusinessHours::format_time_for_schema('09:30'));
	}

	public function test_format_time_for_schema_parses_loose_time_strings(): void {
		$this->assertSame('17:00', BusinessHours::format_time_for_schema('5pm'));
	}

	public function test_format_time_for_schema_falls_back_to_midnight_for_unparseable_input(): void {
		$this->assertSame('00:00', BusinessHours::format_time_for_schema('not a time'));
	}
}
