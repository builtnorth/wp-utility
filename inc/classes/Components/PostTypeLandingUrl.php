<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Components;

use WP_Post;
use WP_Post_Type;

defined('ABSPATH') || defined('WP_CLI') || exit;

/**
 * Resolves a clickable landing URL for a post type (archive, matching Page, or rewrite slug).
 *
 * Used when has_archive is false but CPT singles still live under a slug such as /services/.
 */
final class PostTypeLandingUrl
{
	/**
	 * @return string Permalink or empty string when no public landing exists.
	 */
	public static function resolve(string $post_type): string
	{
		if ($post_type === 'page' || $post_type === 'attachment' || $post_type === '') {
			return '';
		}

		$post_type_object = get_post_type_object($post_type);
		if (!$post_type_object instanceof WP_Post_Type || !$post_type_object->public) {
			return '';
		}

		if ($post_type_object->has_archive) {
			$archive_link = get_post_type_archive_link($post_type);
			if (is_string($archive_link) && $archive_link !== '') {
				return $archive_link;
			}
		}

		$slug = self::get_rewrite_slug($post_type_object);
		if ($slug === '') {
			return '';
		}

		$page = get_page_by_path($slug, OBJECT, 'page');
		if ($page instanceof WP_Post) {
			$permalink = get_permalink($page);
			return is_string($permalink) ? $permalink : '';
		}

		return home_url(user_trailingslashit($slug));
	}

	public static function get_rewrite_slug(WP_Post_Type $post_type): string
	{
		if (is_array($post_type->rewrite)) {
			return (string) ($post_type->rewrite['slug'] ?? $post_type->name);
		}

		return $post_type->name;
	}
}
