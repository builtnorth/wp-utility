<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\StateList;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\StateList
 */
class StateListTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('__')
			->andReturnUsing(static fn($text) => $text);
	}

	public function test_returns_array_keyed_by_abbreviation(): void {
		$states = StateList::render();

		$this->assertIsArray($states);
		$this->assertArrayHasKey('CA', $states);
		$this->assertSame('California', $states['CA']);
	}

	public function test_every_key_is_a_two_letter_uppercase_code(): void {
		$states = StateList::render();

		foreach (array_keys($states) as $code) {
			$this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $code);
		}
	}

	public function test_keys_are_unique(): void {
		$states = StateList::render();

		$this->assertCount(count($states), array_unique(array_keys($states)));
	}

	public function test_includes_all_fifty_states_and_dc(): void {
		$states = StateList::render();

		$this->assertCount(51, $states);

		foreach (['MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH'] as $code) {
			$this->assertArrayHasKey($code, $states);
		}
	}
}
