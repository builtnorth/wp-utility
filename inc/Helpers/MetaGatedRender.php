<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Helpers;

defined('ABSPATH') || exit;

use WP_Block;

/**
 * Shared meta-gated block render checks for dynamic blocks.
 *
 * @see \BuiltNorth\WPUtility\Blocks\MetaGatedBlockSupport Core block attribute registration and render filtering.
 */
class MetaGatedRender
{
	/**
	 * Whether a block should render when hideWhenMetaEmpty is enabled.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public static function should_render(array $attributes, int $post_id): bool
	{
		if (empty($attributes['hideWhenMetaEmpty'])) {
			return true;
		}

		$meta_field = isset($attributes['metaField'])
			? sanitize_text_field((string) $attributes['metaField'])
			: '';

		if ($meta_field === '') {
			return true;
		}

		return ! self::is_post_meta_empty($meta_field, $post_id);
	}

	public static function is_post_meta_empty(string $meta_key, int $post_id): bool
	{
		if ($post_id <= 0 || $meta_key === '') {
			return true;
		}

		$value = get_post_meta($post_id, $meta_key, true);

		if ($value === '' || $value === false || $value === null) {
			return true;
		}

		if (is_array($value)) {
			return ! self::is_valid_icon_meta($value);
		}

		if (is_string($value)) {
			return trim($value) === '';
		}

		return false;
	}

	/**
	 * @param mixed $value Raw post meta value.
	 */
	public static function is_valid_icon_meta(mixed $value): bool
	{
		return is_array($value)
			&& ! empty($value['source'])
			&& ! empty($value['name']);
	}

	public static function resolve_url_from_meta(string $meta_key, int $post_id): string
	{
		if ($post_id <= 0 || $meta_key === '') {
			return '';
		}

		$value = get_post_meta($post_id, $meta_key, true);

		if (! is_string($value) || trim($value) === '') {
			return '';
		}

		$url = esc_url($value);

		if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
			return '';
		}

		return $url;
	}

	/**
	 * Resolve the post ID for a block render request.
	 */
	public static function resolve_post_id(?WP_Block $block = null): int
	{
		if ($block instanceof WP_Block && ! empty($block->context['postId'])) {
			return (int) $block->context['postId'];
		}

		$post_id = get_the_ID();

		return $post_id ? (int) $post_id : 0;
	}
}
