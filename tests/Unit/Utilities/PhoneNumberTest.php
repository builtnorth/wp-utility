<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\PhoneNumber;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\PhoneNumber
 */
class PhoneNumberTest extends WPMockTestCase {

	// -- to_e164() ---------------------------------------------------

	public function test_bare_ten_digit_number_gets_default_country_code(): void {
		$this->assertSame('+15551234567', PhoneNumber::to_e164('(555) 123-4567'));
	}

	public function test_eleven_digit_number_with_leading_one_drops_the_one(): void {
		$this->assertSame('+15551234567', PhoneNumber::to_e164('1-555-123-4567'));
	}

	public function test_already_e164_number_passes_through_unchanged(): void {
		$this->assertSame('+445551234567', PhoneNumber::to_e164('+44 555 123 4567'));
	}

	public function test_custom_default_country_code_is_used(): void {
		$this->assertSame('+445551234567', PhoneNumber::to_e164('555 123 4567', '44'));
	}

	public function test_unparseable_number_is_returned_unchanged(): void {
		$this->assertSame('555-1234', PhoneNumber::to_e164('555-1234'));
	}

	public function test_empty_string_is_returned_unchanged(): void {
		$this->assertSame('', PhoneNumber::to_e164(''));
	}

	// -- tel_link() ----------------------------------------------------

	public function test_tel_link_wraps_digits_in_an_anchor(): void {
		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('esc_html')->andReturnUsing(static fn($text) => $text);

		$this->assertSame(
			"<a href='tel:5551234567'>(555) 123-4567</a>",
			PhoneNumber::tel_link('(555) 123-4567')
		);
	}

	public function test_tel_link_preserves_a_leading_plus(): void {
		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('esc_html')->andReturnUsing(static fn($text) => $text);

		$this->assertSame(
			"<a href='tel:+15551234567'>+1 555 123 4567</a>",
			PhoneNumber::tel_link('+1 555 123 4567')
		);
	}

	public function test_tel_link_falls_back_to_escaped_text_with_no_digits(): void {
		WP_Mock::userFunction('esc_html')
			->with('Call us')
			->andReturn('Call us');

		$this->assertSame('Call us', PhoneNumber::tel_link('Call us'));
	}

	public function test_tel_link_returns_empty_string_for_empty_input(): void {
		$this->assertSame('', PhoneNumber::tel_link(''));
	}
}
