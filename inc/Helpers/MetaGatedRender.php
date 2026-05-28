<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Helpers;

defined('ABSPATH') || exit;

/**
 * Shared meta-gated block render checks for dynamic blocks.
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
}
