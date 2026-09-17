<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\FormatAddress;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\FormatAddress
 */
class FormatAddressTest extends WPMockTestCase {

	// -- single_line() -------------------------------------------------

	public function test_single_line_joins_all_parts(): void {
		$this->assertSame(
			'123 Main St, Springfield, IL 62704',
			FormatAddress::single_line('123 Main St', 'Springfield', 'IL', '62704')
		);
	}

	public function test_single_line_skips_empty_street(): void {
		$this->assertSame(
			'Springfield, IL 62704',
			FormatAddress::single_line('', 'Springfield', 'IL', '62704')
		);
	}

	public function test_single_line_skips_empty_state(): void {
		$this->assertSame(
			'123 Main St, Springfield 62704',
			FormatAddress::single_line('123 Main St', 'Springfield', '', '62704')
		);
	}

	public function test_single_line_skips_empty_zip(): void {
		$this->assertSame(
			'123 Main St, Springfield, IL',
			FormatAddress::single_line('123 Main St', 'Springfield', 'IL', '')
		);
	}

	public function test_single_line_skips_empty_city_but_keeps_state_and_zip(): void {
		$this->assertSame(
			'123 Main St, IL 62704',
			FormatAddress::single_line('123 Main St', '', 'IL', '62704')
		);
	}

	public function test_single_line_returns_empty_string_when_everything_is_empty(): void {
		$this->assertSame('', FormatAddress::single_line());
	}

	public function test_single_line_returns_just_the_street_when_nothing_else_given(): void {
		$this->assertSame('123 Main St', FormatAddress::single_line('123 Main St'));
	}

	// -- schema() --------------------------------------------------------

	public function test_schema_builds_a_full_postal_address(): void {
		$this->assertSame(
			[
				'@type'           => 'PostalAddress',
				'streetAddress'   => '123 Main St',
				'addressLocality' => 'Springfield',
				'addressRegion'   => 'IL',
				'postalCode'      => '62704',
				'addressCountry'  => 'US',
			],
			FormatAddress::schema('123 Main St', 'Springfield', 'IL', '62704', 'US')
		);
	}

	public function test_schema_omits_empty_parts(): void {
		$this->assertSame(
			['@type' => 'PostalAddress', 'addressLocality' => 'Springfield'],
			FormatAddress::schema('', 'Springfield')
		);
	}

	public function test_schema_returns_null_when_every_part_is_empty(): void {
		$this->assertNull(FormatAddress::schema());
	}
}
