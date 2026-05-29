<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Blocks;

use BuiltNorth\WPUtility\Blocks\MetaGatedBlockSupport;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Block;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Blocks\MetaGatedBlockSupport
 */
class MetaGatedBlockSupportTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('sanitize_text_field')
			->andReturnUsing(static fn( string $value ): string => $value);
	}

	public function test_attribute_definitions_include_meta_gated_fields(): void {
		$attributes = MetaGatedBlockSupport::attribute_definitions();

		$this->assertArrayHasKey('metaField', $attributes);
		$this->assertArrayHasKey('hideWhenMetaEmpty', $attributes);
	}

	public function test_register_attributes_merges_schema_for_supported_blocks(): void {
		$args = MetaGatedBlockSupport::register_attributes(
			[
				'attributes' => [
					'className' => [
						'type' => 'string',
					],
				],
			],
			'core/group'
		);

		$this->assertArrayHasKey('metaField', $args['attributes']);
		$this->assertArrayHasKey('hideWhenMetaEmpty', $args['attributes']);
		$this->assertArrayHasKey('className', $args['attributes']);
	}

	public function test_register_attributes_ignores_unsupported_blocks(): void {
		$args = MetaGatedBlockSupport::register_attributes(
			[
				'attributes' => [],
			],
			'core/image'
		);

		$this->assertSame([], $args['attributes']);
	}

	public function test_filter_render_block_hides_output_when_meta_is_empty(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(42, 'team_email', true)
			->andReturn('');

		$instance = $this->createMock( \WP_Block::class );
		$instance->context = [ 'postId' => 42 ];

		$output = MetaGatedBlockSupport::filter_render_block(
			'<div class="wp-block-group">Email</div>',
			[
				'blockName' => 'core/group',
				'attrs'     => [
					'metaField'         => 'team_email',
					'hideWhenMetaEmpty' => true,
				],
			],
			$instance
		);

		$this->assertSame('', $output);
	}

	public function test_filter_render_block_returns_content_when_meta_is_populated(): void {
		WP_Mock::userFunction('get_post_meta')
			->with(42, 'team_email', true)
			->andReturn('team@example.com');

		$instance = $this->createMock( \WP_Block::class );
		$instance->context = [ 'postId' => 42 ];

		$content = '<div class="wp-block-group">Email</div>';

		$output = MetaGatedBlockSupport::filter_render_block(
			$content,
			[
				'blockName' => 'core/group',
				'attrs'     => [
					'metaField'         => 'team_email',
					'hideWhenMetaEmpty' => true,
				],
			],
			$instance
		);

		$this->assertSame($content, $output);
	}
}
