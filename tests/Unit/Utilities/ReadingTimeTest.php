<?php
/**
 * Tests for the ReadingTime utility
 *
 * @package BuiltNorth\WPUtility\Tests\Unit\Utilities
 */

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Utilities\ReadingTime;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\ReadingTime
 */
class ReadingTimeTest extends WPMockTestCase {

	/**
	 * @param string $content Post content returned by get_the_content().
	 * @param int    $wpm     Words-per-minute filter reply.
	 */
	private function mock_content( string $content, int $wpm = 200 ): void {
		WP_Mock::userFunction( 'get_the_content' )->andReturn( $content );
		WP_Mock::onFilter( 'wp_utility_reading_time_wpm' )
			->with( 200 )
			->reply( $wpm );
	}

	public function test_short_text_rounds_up_to_one_minute(): void {
		$this->mock_content( str_repeat( 'word ', 100 ) );

		$this->assertSame( 1, ReadingTime::render() );
	}

	public function test_medium_text_rounds_up_to_three_minutes_at_default_wpm(): void {
		// 500 words / 200 wpm = 2.5 → ceil 3
		$this->mock_content( str_repeat( 'word ', 500 ) );

		$this->assertSame( 3, ReadingTime::render() );
	}

	public function test_long_text_calculates_ten_minutes(): void {
		// 2000 words / 200 wpm = 10
		$this->mock_content( str_repeat( 'word ', 2000 ) );

		$this->assertSame( 10, ReadingTime::render() );
	}

	public function test_html_tags_are_stripped_before_counting(): void {
		$this->mock_content( '<p>one two three four five</p>' );

		$this->assertSame( 1, ReadingTime::render() );
	}

	public function test_empty_content_returns_zero(): void {
		$this->mock_content( '' );

		$this->assertSame( 0, ReadingTime::render() );
	}

	public function test_custom_wpm_filter_affects_the_result(): void {
		// 200 words / 100 wpm = 2
		$this->mock_content( str_repeat( 'word ', 200 ), 100 );

		$this->assertSame( 2, ReadingTime::render() );
	}
}
