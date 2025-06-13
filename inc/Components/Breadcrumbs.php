<?php

namespace BuiltNorth\Utility\Components;

class Breadcrumbs
{
	private static $class;
	private static $separator;
	private static $home_title;

	public static function render(
		$show_on_front = null,
		$class = 'breadcrumbs',
		$separator = '&raquo;',
		$home_title = 'Home',
		$prefix = null,
	) {
		// Initialize class properties
		self::$class = $class;
		self::$separator = $separator;
		self::$home_title = $home_title;

		// Don't display on homepage unless specifically requested
		if (is_front_page() && !$show_on_front) {
			return;
		}

		// Start breadcrumb navigation
		echo self::open_nav();
		echo self::home_breadcrumb();

		// Generate appropriate breadcrumbs based on current page type
		if (is_single()) {
			echo self::single_post_breadcrumbs($prefix);
		} elseif (is_page()) {
			echo self::page_breadcrumbs();
		} elseif (is_archive()) {
			echo self::archive_breadcrumbs($prefix);
		} elseif (is_search()) {
			echo self::search_breadcrumbs();
		} elseif (is_404()) {
			echo self::error_breadcrumbs();
		} elseif (is_home() && get_option('page_for_posts')) {
			echo self::blog_home_breadcrumbs();
		}

		echo self::close_nav();
	}

	/**
	 * Generate opening navigation HTML
	 */
	private static function open_nav()
	{
		return '<nav class="' . self::$class . '"><ol class="' . self::$class . '__list">';
	}

	/**
	 * Generate closing navigation HTML
	 */
	private static function close_nav()
	{
		return '</ol></nav>';
	}

	/**
	 * Generate home breadcrumb
	 */
	private static function home_breadcrumb()
	{
		$html = self::breadcrumb_item(
			self::$home_title,
			get_home_url(),
			'home',
			false
		);
		$html .= self::separator();
		return $html;
	}

	/**
	 * Generate breadcrumbs for single posts
	 */
	private static function single_post_breadcrumbs($prefix)
	{
		global $post;
		$html = '';

		// Add post type archive link if not a regular post
		if (get_post_type() !== 'post') {
			$html .= self::post_type_archive_breadcrumb();
		}

		// Add taxonomy breadcrumbs
		$html .= self::post_taxonomy_breadcrumbs($post->ID);

		// Add current post
		$html .= self::current_item(get_the_title(), $post->ID);

		return $html;
	}

	/**
	 * Generate breadcrumbs for pages
	 */
	private static function page_breadcrumbs()
	{
		global $post;
		$html = '';

		// Handle parent pages
		if ($post->post_parent) {
			$ancestors = array_reverse(get_post_ancestors($post->ID));

			foreach ($ancestors as $ancestor_id) {
				$html .= self::breadcrumb_item(
					get_the_title($ancestor_id),
					get_permalink($ancestor_id),
					'parent parent-' . $ancestor_id,
					false
				);
				$html .= self::separator();
			}
		}

		// Add current page
		$html .= self::current_item(get_the_title(), $post->ID);

		return $html;
	}

	/**
	 * Generate breadcrumbs for archive pages
	 */
	private static function archive_breadcrumbs($prefix)
	{
		if (is_category()) {
			return self::category_archive_breadcrumbs();
		} elseif (is_tag()) {
			return self::tag_archive_breadcrumbs();
		} elseif (is_tax()) {
			return self::taxonomy_archive_breadcrumbs();
		} elseif (is_date()) {
			return self::date_archive_breadcrumbs();
		} elseif (is_author()) {
			return self::author_archive_breadcrumbs();
		} elseif (get_query_var('paged')) {
			return self::pagination_breadcrumbs();
		} else {
			return self::post_type_archive_breadcrumbs($prefix);
		}
	}

	/**
	 * Generate breadcrumbs for category archives
	 */
	private static function category_archive_breadcrumbs()
	{
		$category = get_queried_object();
		return self::get_taxonomy_hierarchy_breadcrumbs($category);
	}

	/**
	 * Generate breadcrumbs for tag archives
	 */
	private static function tag_archive_breadcrumbs()
	{
		$tag = get_queried_object();
		return self::current_item($tag->name, 'tag-' . $tag->term_id . ' tag-' . $tag->slug);
	}

	/**
	 * Generate breadcrumbs for custom taxonomy archives
	 */
	private static function taxonomy_archive_breadcrumbs()
	{
		$html = '';

		// Add post type archive if not 'post'
		$post_type = get_post_type();
		if ($post_type && $post_type !== 'post') {
			$html .= self::post_type_archive_breadcrumb();
		}

		// Add taxonomy hierarchy
		$term = get_queried_object();
		if ($term instanceof \WP_Term) {
			$html .= self::get_taxonomy_hierarchy_breadcrumbs($term);
		}

		return $html;
	}

	/**
	 * Generate breadcrumbs for date archives
	 */
	private static function date_archive_breadcrumbs()
	{
		$html = '';

		if (is_day()) {
			// Year > Month > Day
			$html .= self::breadcrumb_item(
				get_the_time('Y') . ' Archives',
				get_year_link(get_the_time('Y')),
				'year year-' . get_the_time('Y'),
				false
			);
			$html .= self::separator();

			$html .= self::breadcrumb_item(
				get_the_time('M') . ' Archives',
				get_month_link(get_the_time('Y'), get_the_time('m')),
				'month month-' . get_the_time('m'),
				false
			);
			$html .= self::separator();

			$html .= self::current_item(
				get_the_time('jS') . ' ' . get_the_time('M') . ' Archives',
				get_the_time('j')
			);
		} elseif (is_month()) {
			// Year > Month
			$html .= self::breadcrumb_item(
				get_the_time('Y') . ' Archives',
				get_year_link(get_the_time('Y')),
				'year year-' . get_the_time('Y'),
				false
			);
			$html .= self::separator();

			$html .= self::current_item(
				get_the_time('M') . ' Archives',
				'month month-' . get_the_time('m')
			);
		} elseif (is_year()) {
			// Year only
			$html .= self::current_item(
				get_the_time('Y') . ' Archives',
				'current-' . get_the_time('Y')
			);
		}

		return $html;
	}

	/**
	 * Generate breadcrumbs for author archives
	 */
	private static function author_archive_breadcrumbs()
	{
		global $author;
		$userdata = get_userdata($author);

		return self::current_item(
			'Author: ' . $userdata->display_name,
			'current-' . $userdata->user_nicename
		);
	}

	/**
	 * Generate breadcrumbs for paginated archives
	 */
	private static function pagination_breadcrumbs()
	{
		$page = get_query_var('paged');
		return self::current_item(
			__('Page') . ' ' . $page,
			'current-' . $page
		);
	}

	/**
	 * Generate breadcrumbs for post type archives
	 */
	private static function post_type_archive_breadcrumbs($prefix)
	{
		return self::current_item(post_type_archive_title($prefix, false));
	}

	/**
	 * Generate breadcrumbs for search results
	 */
	private static function search_breadcrumbs()
	{
		$query = get_search_query();
		return self::current_item(
			'Search results for: ' . $query,
			'current-' . $query
		);
	}

	/**
	 * Generate breadcrumbs for 404 pages
	 */
	private static function error_breadcrumbs()
	{
		return '<li>Error 404</li>';
	}

	/**
	 * Generate breadcrumbs for blog home page
	 */
	private static function blog_home_breadcrumbs()
	{
		return '<li>' . get_the_title(get_option('page_for_posts')) . '</li>';
	}

	/**
	 * Generate post type archive breadcrumb item
	 */
	private static function post_type_archive_breadcrumb()
	{
		$post_type = get_post_type();
		$post_type_object = get_post_type_object($post_type);
		$post_type_archive = get_post_type_archive_link($post_type);

		$html = self::breadcrumb_item(
			$post_type_object->labels->name,
			$post_type_archive,
			'custom-post-type-' . $post_type,
			false
		);
		$html .= self::separator();

		return $html;
	}

	/**
	 * Get taxonomy breadcrumbs for a single post
	 */
	private static function post_taxonomy_breadcrumbs($post_id)
	{
		$html = '';

		// Try categories first (for regular posts)
		$categories = get_the_category($post_id);
		if (!empty($categories)) {
			$category = end($categories); // Get the last category
			$html .= self::get_category_hierarchy_breadcrumbs($category);
			return $html;
		}

		// Try custom taxonomies
		$post_type = get_post_type($post_id);
		$taxonomies = get_object_taxonomies($post_type, 'objects');

		// Remove built-in taxonomies we've already handled
		unset($taxonomies['category'], $taxonomies['post_tag']);

		foreach ($taxonomies as $taxonomy) {
			$terms = get_the_terms($post_id, $taxonomy->name);
			if ($terms && !is_wp_error($terms)) {
				$term = self::get_primary_term($terms, $taxonomy->name);
				$html .= self::get_taxonomy_hierarchy_breadcrumbs($term, false);
				break; // Use first available taxonomy
			}
		}

		return $html;
	}

	/**
	 * Get the primary term from a list of terms (most deeply nested for hierarchical taxonomies)
	 */
	private static function get_primary_term($terms, $taxonomy)
	{
		if (count($terms) === 1) {
			return $terms[0];
		}

		if (!is_taxonomy_hierarchical($taxonomy)) {
			return $terms[0];
		}

		// Find the most deeply nested term
		$deepest_level = 0;
		$primary_term = $terms[0];

		foreach ($terms as $term) {
			$level = count(get_ancestors($term->term_id, $taxonomy, 'taxonomy'));
			if ($level > $deepest_level) {
				$deepest_level = $level;
				$primary_term = $term;
			}
		}

		return $primary_term;
	}

	/**
	 * Get hierarchical breadcrumbs for a taxonomy term
	 */
	private static function get_taxonomy_hierarchy_breadcrumbs($term, $include_current = true)
	{
		$html = '';

		if (!$term || is_wp_error($term)) {
			return $html;
		}

		// Get ancestors
		if (is_taxonomy_hierarchical($term->taxonomy)) {
			$ancestors = array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy'));

			foreach ($ancestors as $ancestor_id) {
				$ancestor = get_term($ancestor_id, $term->taxonomy);
				if ($ancestor && !is_wp_error($ancestor)) {
					$html .= self::breadcrumb_item(
						$ancestor->name,
						get_term_link($ancestor),
						'tax-' . $ancestor->taxonomy,
						false
					);
					$html .= self::separator();
				}
			}
		}

		// Add current term
		if ($include_current) {
			$html .= self::current_item($term->name);
		} else {
			$html .= self::breadcrumb_item(
				$term->name,
				get_term_link($term),
				'tax-' . $term->taxonomy,
				false
			);
			$html .= self::separator();
		}

		return $html;
	}

	/**
	 * Get hierarchical breadcrumbs for a category (specialized version)
	 */
	private static function get_category_hierarchy_breadcrumbs($category)
	{
		$html = '';

		// Get category parents using WordPress built-in function
		$cat_parents = rtrim(get_category_parents($category->term_id, true, ','), ',');
		$parent_links = explode(',', $cat_parents);

		// Remove the last item (current category) and add separators
		array_pop($parent_links);

		foreach ($parent_links as $parent_link) {
			$html .= '<li class="' . self::$class . '__item">' . $parent_link . '</li>';
			$html .= self::separator();
		}

		return $html;
	}

	/**
	 * Generate a breadcrumb item
	 */
	private static function breadcrumb_item($text, $url = '', $additional_class = '', $is_current = false)
	{
		$class_suffix = $is_current ? '__item--current' : '';
		$full_class = self::$class . '__item ' . self::$class . $class_suffix;

		if ($additional_class) {
			$full_class .= ' ' . self::$class . '__item--' . $additional_class;
		}

		$html = '<li class="' . $full_class . '">';

		if ($is_current || empty($url)) {
			$current_class = self::$class . '__current';
			if ($additional_class) {
				$current_class .= ' ' . self::$class . '__current--' . $additional_class;
			}
			$html .= '<strong class="' . $current_class . '" title="' . esc_attr($text) . '">' . $text . '</strong>';
		} else {
			$link_class = self::$class . '__link';
			if ($additional_class) {
				$link_class .= ' ' . self::$class . '__link--' . $additional_class;
			}
			$html .= '<a class="' . $link_class . '" href="' . esc_url($url) . '" title="' . esc_attr($text) . '">' . $text . '</a>';
		}

		$html .= '</li>';

		return $html;
	}

	/**
	 * Generate a current/active breadcrumb item
	 */
	private static function current_item($text, $additional_class = '')
	{
		return self::breadcrumb_item($text, '', $additional_class, true);
	}

	/**
	 * Generate breadcrumb separator
	 */
	private static function separator()
	{
		return '<li class="' . self::$class . '__separator"> ' . self::$separator . ' </li>';
	}
}
