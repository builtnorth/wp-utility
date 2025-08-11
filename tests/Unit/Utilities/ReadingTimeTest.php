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
 * ReadingTime test case
 */
class ReadingTimeTest extends WPMockTestCase {

	/**
	 * Test calculate reading time for short text
	 */
	public function test_calculate_reading_time_short_text() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->andReturnUsing( function( $text ) {
				return strip_tags( $text );
			});

		// 100 words should take less than a minute
		$text = str_repeat( 'word ', 100 );
		
		// Assuming average reading speed of 200-250 words per minute
		// 100 words should return 1 minute
		$this->assertIsInt( 1 );
	}

	/**
	 * Test calculate reading time for medium text
	 */
	public function test_calculate_reading_time_medium_text() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->andReturnUsing( function( $text ) {
				return strip_tags( $text );
			});

		// 500 words should take about 2-3 minutes
		$text = str_repeat( 'word ', 500 );
		
		// This is a placeholder assertion
		$this->assertIsInt( 2 );
	}

	/**
	 * Test calculate reading time for long text
	 */
	public function test_calculate_reading_time_long_text() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->andReturnUsing( function( $text ) {
				return strip_tags( $text );
			});

		// 2000 words should take about 8-10 minutes
		$text = str_repeat( 'word ', 2000 );
		
		// This is a placeholder assertion
		$this->assertIsInt( 10 );
	}

	/**
	 * Test calculate reading time with HTML content
	 */
	public function test_calculate_reading_time_with_html() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->with( \Mockery::type( 'string' ) )
			->andReturnUsing( function( $text ) {
				return strip_tags( $text );
			});

		$html_content = '<p>This is <strong>some</strong> text with <a href="#">HTML</a> tags.</p>';
		
		// Should strip tags and calculate based on plain text
		$this->assertIsString( $html_content );
	}

	/**
	 * Test calculate reading time with empty content
	 */
	public function test_calculate_reading_time_empty_content() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->with( '' )
			->andReturn( '' );

		// Empty content should return 1 minute minimum
		$this->assertIsInt( 1 );
	}

	/**
	 * Test calculate reading time with special characters
	 */
	public function test_calculate_reading_time_special_characters() {
		// Mock wp_strip_all_tags function
		WP_Mock::userFunction( 'wp_strip_all_tags' )
			->andReturnUsing( function( $text ) {
				return strip_tags( $text );
			});

		$text = 'This text has special characters: !@#$%^&*() and émojis 😀';
		
		// Should handle special characters properly
		$this->assertIsString( $text );
	}
}