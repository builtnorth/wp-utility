<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\CountryList;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\CountryList
 */
class CountryListTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('__')
			->andReturnUsing(static fn($text) => $text);
	}

	public function test_returns_array_keyed_by_iso_code(): void {
		$countries = CountryList::render();

		$this->assertIsArray($countries);
		$this->assertArrayHasKey('US', $countries);
		$this->assertSame('United States', $countries['US']);
	}

	/**
	 * Every key must be a usable <option value> / stored setting — a stray
	 * lowercase or malformed key would silently break matching against
	 * previously saved values.
	 */
	public function test_every_key_is_a_two_letter_uppercase_code(): void {
		$countries = CountryList::render();

		foreach (array_keys($countries) as $code) {
			$this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $code);
		}
	}

	public function test_keys_are_unique(): void {
		$countries = CountryList::render();

		$this->assertCount(count($countries), array_unique(array_keys($countries)));
	}
}
