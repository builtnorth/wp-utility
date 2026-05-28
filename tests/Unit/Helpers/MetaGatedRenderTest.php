<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Helpers;

use BuiltNorth\WPUtility\Helpers\MetaGatedRender;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Helpers\MetaGatedRender
 */
class MetaGatedRenderTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('sanitize_text_field')
			->andReturnUsing(static fn( string $value ): string => $value);
	}

	public function test_should_render_when_hide_flag_is_off(): void {
		$this->assertTrue(
			MetaGatedRender::should_render(
				[
					'hideWhenMetaEmpty' => false,
					'metaField' => 'has_map_url',
				],
				42
			)
		);
	}

	public function test_should_not_render_when_meta_is_empty_and_flag_is_on(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(42, 'has_map_url', true)
			->andReturn('');

		$this->assertFalse(
			MetaGatedRender::should_render(
				[
					'hideWhenMetaEmpty' => true,
					'metaField' => 'has_map_url',
				],
				42
			)
		);
	}

	public function test_should_render_when_meta_is_populated_and_flag_is_on(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(42, 'has_map_url', true)
			->andReturn('https://maps.example.com');

		$this->assertTrue(
			MetaGatedRender::should_render(
				[
					'hideWhenMetaEmpty' => true,
					'metaField' => 'has_map_url',
				],
				42
			)
		);
	}

	public function test_is_valid_icon_meta(): void {
		$this->assertTrue(
			MetaGatedRender::is_valid_icon_meta(
				[
					'name' => 'wrench',
					'source' => '<svg></svg>',
				]
			)
		);

		$this->assertFalse(
			MetaGatedRender::is_valid_icon_meta(
				[
					'name' => 'wrench',
				]
			)
		);
	}

	public function test_is_post_meta_empty_for_invalid_icon_object(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(10, 'service_icon', true)
			->andReturn([ 'name' => 'wrench' ]);

		$this->assertTrue(MetaGatedRender::is_post_meta_empty('service_icon', 10));
	}

	public function test_resolve_url_from_meta(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(5, 'has_map_url', true)
			->andReturn('https://maps.example.com/dir');

		WP_Mock::userFunction('esc_url')
			->andReturnUsing(static fn( string $url ): string => $url);

		$this->assertSame(
			'https://maps.example.com/dir',
			MetaGatedRender::resolve_url_from_meta('has_map_url', 5)
		);
	}
}
