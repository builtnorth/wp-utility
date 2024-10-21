<?php

namespace BuiltNorth\Utility\Components;

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

		$paginate_links = paginate_links(array(
			'base' => $base . '%_%',
			'format' => (strpos($base, '?') !== false ? '&' : '?') . 'paged=%#%',
			'current' => max(1, $paged),
			'total' => $query->max_num_pages,
			'mid_size' => 3,
			'type' => 'list',
			'before_page_number' => '<span class="sr-only">' . $translated . ' </span>',
		));

		// Display the pagination if more than one page is found
		if ($paginate_links) {
			return '<div class="pagination" aria-label="Navigate Between Archive Pages">' . $paginate_links . '</div>';
		}

		return '';
	}
}
