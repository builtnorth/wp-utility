<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\Image;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Components\Image::sizes
 */
class ImageSizesTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		// The resolved content width is memoised for the life of the process,
		// so a value resolved under one fixture would otherwise leak into the
		// next test.
		Image::reset_content_width();
	}

	public function tearDown(): void {
		Image::reset_content_width();

		parent::tearDown();
	}

	/**
	 * Stub the global-settings lookup that resolve_content_width() reads.
	 */
	private function mock_content_size( $content_size ): void {
		WP_Mock::userFunction('wp_get_global_settings')
			->andReturn( [ 'contentSize' => $content_size ] );
	}

	public function test_full_width_returns_plain_100vw(): void {
		// No cap is meaningful for a full-bleed image: it genuinely is the
		// width of the viewport, not of the content column.
		$this->assertSame( '100vw', Image::sizes( 100 ) );
	}

	public function test_over_100_is_still_100vw(): void {
		$this->assertSame( '100vw', Image::sizes( 150 ) );
	}

	public function test_half_width_is_capped_at_half_the_content_width(): void {
		$this->mock_content_size( '1280px' );

		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 640px)',
			Image::sizes( 50 )
		);
	}

	public function test_third_width_rounds_the_pixel_cap(): void {
		$this->mock_content_size( '1280px' );

		// 1280 * 33 / 100 = 422.4 -> 422
		$this->assertSame(
			'(max-width: 782px) 100vw, min(33vw, 422px)',
			Image::sizes( 33 )
		);
	}

	public function test_custom_breakpoint_is_used(): void {
		$this->mock_content_size( '1280px' );

		$this->assertSame(
			'(max-width: 600px) 100vw, min(50vw, 640px)',
			Image::sizes( 50, 600 )
		);
	}

	public function test_explicit_content_width_overrides_the_theme(): void {
		$this->mock_content_size( '1280px' );

		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 450px)',
			Image::sizes( 50, 782, 900 )
		);
	}

	/**
	 * contentSize is free-form CSS. Anything that is not px cannot be
	 * converted to a pixel cap here, so the fallback must be used rather than
	 * emitting a nonsense value like min(50vw, 40px) for "80rem".
	 *
	 * @dataProvider provide_unconvertible_content_sizes
	 */
	public function test_non_px_content_size_falls_back( $content_size ): void {
		$this->mock_content_size( $content_size );

		$expected_cap = (int) round( Image::DEFAULT_CONTENT_WIDTH * 0.5 );

		$this->assertSame(
			"(max-width: 782px) 100vw, min(50vw, {$expected_cap}px)",
			Image::sizes( 50 )
		);
	}

	public static function provide_unconvertible_content_sizes(): array {
		return [
			'rem'        => [ '80rem' ],
			'percent'    => [ '90%' ],
			'clamp'      => [ 'clamp(20rem, 60vw, 80rem)' ],
			'unitless'   => [ '1280' ],
			'empty'      => [ '' ],
			'zero px'    => [ '0px' ],
			'non string' => [ null ],
		];
	}

	public function test_px_value_is_parsed_tolerantly(): void {
		// Whitespace and capitalisation are valid CSS and must still parse.
		$this->mock_content_size( '  1000PX ' );

		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 500px)',
			Image::sizes( 50 )
		);
	}

	public function test_fractional_px_content_size_is_rounded(): void {
		$this->mock_content_size( '1279.6px' );

		// 1280 * 50 / 100 = 640
		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 640px)',
			Image::sizes( 50 )
		);
	}

	/**
	 * A share below 1% would otherwise produce a 0px cap, which reads as
	 * "never download anything" rather than "download something small".
	 */
	public function test_zero_share_is_clamped_to_one_percent(): void {
		$this->mock_content_size( '1280px' );

		$this->assertSame(
			'(max-width: 782px) 100vw, min(1vw, 13px)',
			Image::sizes( 0 )
		);
	}

	public function test_resolved_width_is_memoised(): void {
		// A gallery loop calls sizes() once per image; re-resolving the merged
		// theme.json tree each time would be needless work. The ->once()
		// expectation is verified on teardown.
		$calls = 0;

		WP_Mock::userFunction('wp_get_global_settings')
			->once()
			->andReturnUsing(
				function () use ( &$calls ) {
					$calls++;

					return [ 'contentSize' => '1280px' ];
				}
			);

		Image::sizes( 50 );
		Image::sizes( 25 );
		Image::sizes( 33 );

		$this->assertSame( 1, $calls, 'Content width should be resolved once per process.' );
	}

	public function test_reset_forces_a_fresh_lookup(): void {
		// Guards the memo against leaking a stale width across a theme switch
		// (and across tests, which is why setUp() resets it).
		WP_Mock::userFunction('wp_get_global_settings')
			->andReturn( [ 'contentSize' => '1280px' ], [ 'contentSize' => '900px' ] );

		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 640px)',
			Image::sizes( 50 )
		);

		Image::reset_content_width();

		$this->assertSame(
			'(max-width: 782px) 100vw, min(50vw, 450px)',
			Image::sizes( 50 )
		);
	}
}
