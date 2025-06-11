<?php

namespace BuiltNorth\Utility\Utilities;

/**
 * ------------------------------------------------------------------
 * Archive URL Utility
 * ------------------------------------------------------------------
 *
 * Handles conversion of pretty permalink URLs to query string URLs for archive pages
 *
 * @package BuiltNorth\Utility
 * @since 2.0.0
 */

class ArchiveUrl
{
	/**
	 * Convert pretty permalink URLs to query string URLs
	 */
	public static function convert_url()
	{
		if (!is_tax()) {
			return;
		}

		$term = get_queried_object();
		$post_type = self::get_current_post_type();

		// Get post type object and its archive slug
		$post_type_obj = get_post_type_object($post_type);
		if (!$post_type_obj) {
			return;
		}

		// Get the archive slug from the post type registration
		$post_type_slug = $post_type_obj->has_archive;
		if ($post_type_slug === true) {
			$post_type_slug = $post_type_obj->name;
		}

		// Get taxonomy rewrite slug
		$taxonomy = get_taxonomy($term->taxonomy);
		$taxonomy_slug = $taxonomy->rewrite['slug'] ?? $term->taxonomy;

		// Only redirect if we're not already using query parameters
		if (empty($_GET)) {
			// Build new URL with archive path
			$new_url = home_url("/{$post_type_slug}/");
			$new_url = add_query_arg($taxonomy_slug, $term->slug, $new_url);

			// Debug log
			error_log('Archive URL Debug:');
			error_log('Post Type: ' . $post_type);
			error_log('Post Type Slug: ' . $post_type_slug);
			error_log('Taxonomy: ' . $term->taxonomy);
			error_log('Taxonomy Slug: ' . $taxonomy_slug);
			error_log('Term Slug: ' . $term->slug);
			error_log('New URL: ' . $new_url);

			wp_safe_redirect($new_url);
			exit;
		}
	}

	/**
	 * Get the current post type from the query
	 */
	private static function get_current_post_type()
	{
		global $wp_query;

		// Try to get post type from query
		if (!empty($wp_query->query_vars['post_type'])) {
			return $wp_query->query_vars['post_type'];
		}

		// Try to get post type from taxonomy
		if (!empty($wp_query->query_vars['taxonomy'])) {
			$taxonomy = get_taxonomy($wp_query->query_vars['taxonomy']);
			if ($taxonomy && !empty($taxonomy->object_type)) {
				return $taxonomy->object_type[0];
			}
		}

		// Default to post
		return 'post';
	}
}
