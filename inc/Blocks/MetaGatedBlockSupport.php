<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Blocks;

use BuiltNorth\WPUtility\Helpers\MetaGatedRender;
use WP_Block;

defined('ABSPATH') || exit;

/**
 * Registers meta-gated attributes on core blocks and suppresses frontend output
 * when hideWhenMetaEmpty is enabled and the bound meta value is empty.
 *
 * Dynamic Polaris blocks should continue calling Helper::metaGatedShouldRender()
 * from their render.php files. This class covers blocks that do not have a
 * dedicated render callback (for example core/group and core/paragraph).
 */
class MetaGatedBlockSupport
{
	/**
	 * @var array<int, string>
	 */
	private const DEFAULT_BLOCK_TYPES = [
		'core/group',
		'core/paragraph',
	];

	public static function init(): void
	{
		add_filter('register_block_type_args', [self::class, 'register_attributes'], 10, 2);
		add_filter('render_block', [self::class, 'filter_render_block'], 10, 3);
	}

	/**
	 * Block attribute definitions shared by meta-gated blocks.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function attribute_definitions(): array
	{
		/**
		 * @param array<string, array<string, mixed>> $attributes Attribute schema.
		 */
		return apply_filters(
			'wp_utility_meta_gated_block_attributes',
			[
				'metaField' => [
					'type' => 'string',
				],
				'hideWhenMetaEmpty' => [
					'type'    => 'boolean',
					'default' => false,
				],
			]
		);
	}

	/**
	 * Block types that receive meta-gated attributes and render filtering.
	 *
	 * @return array<int, string>
	 */
	public static function supported_block_types(): array
	{
		/**
		 * @param array<int, string> $block_types Block names.
		 */
		return apply_filters(
			'wp_utility_meta_gated_block_types',
			self::DEFAULT_BLOCK_TYPES
		);
	}

	/**
	 * @param array<string, mixed> $args Block registration args.
	 */
	public static function register_attributes(array $args, string $block_name): array
	{
		if (! in_array($block_name, self::supported_block_types(), true)) {
			return $args;
		}

		$args['attributes'] = array_merge(
			$args['attributes'] ?? [],
			self::attribute_definitions()
		);

		return $args;
	}

	/**
	 * @param array<string, mixed> $block Parsed block.
	 */
	public static function filter_render_block(string $block_content, array $block, WP_Block $instance): string
	{
		$block_name = $block['blockName'] ?? '';

		if (! in_array($block_name, self::supported_block_types(), true)) {
			return $block_content;
		}

		$attributes = $block['attrs'] ?? [];

		if (empty($attributes['hideWhenMetaEmpty']) || empty($attributes['metaField'])) {
			return $block_content;
		}

		$post_id = MetaGatedRender::resolve_post_id($instance);

		if ($post_id <= 0) {
			return $block_content;
		}

		if (! MetaGatedRender::should_render($attributes, $post_id)) {
			return '';
		}

		return $block_content;
	}
}
