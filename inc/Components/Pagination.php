<?php
/**
 * Pagination Component
 *
 * Generates accessible pagination for WordPress queries with support
 * for custom post types and archives.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Components
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Components;

class Pagination
{
	public static function render($query = null)
	{
		if (!$query) {
			global $wp_query;
			$query = $wp_query;
		}

		$big = 999999999; // need an unlikely integer
		$translated = __('Page', 'mytextdomain'); // Supply translatable string
		$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);

		// Check if we're on a custom post type archive
		$post_type = $query->get('post_type');
		if (is_array($post_type)) {
			$post_type = reset($post_type);
		}
		$post_type_object = get_post_type_object($post_type);

		if ($post_type_object && is_post_type_archive($post_type)) {
			$base = get_post_type_archive_link($post_type);
		} else {
			$base = get_pagenum_link(1);
		}

		$args = array(
			'base' => $base . '%_%',
			'format' => (strpos($base, '?') !== false ? '&' : '?') . 'paged=%#%',
			'current' => max(1, $paged),
			'total' => $query->max_num_pages,
			'mid_size' => 3,
			'type' => 'list',
			'before_page_number' => '<span class="sr-only">' . $translated . ' </span>',
		);

		/**
		 * Filter the pagination arguments.
		 * 
		 * @param array $args Pagination arguments for paginate_links().
		 * @param WP_Query $query The query object.
		 */
		$args = apply_filters('wp_utility_pagination_args', $args, $query);

		$paginate_links = paginate_links($args);

		// Display the pagination if more than one page is found
		if ($paginate_links) {
			$wrapper_class = 'pagination';
			$wrapper_label = 'Navigate Between Archive Pages';
			
			/**
			 * Filter the pagination wrapper attributes.
			 * 
			 * @param array $wrapper Array with 'class' and 'label' keys.
			 * @param WP_Query $query The query object.
			 */
			$wrapper = apply_filters('wp_utility_pagination_wrapper', [
				'class' => $wrapper_class,
				'label' => $wrapper_label,
			], $query);
			
			return sprintf(
				'<div class="%s" aria-label="%s">%s</div>',
				esc_attr($wrapper['class']),
				esc_attr($wrapper['label']),
				$paginate_links
			);
		}

		return '';
	}
}
