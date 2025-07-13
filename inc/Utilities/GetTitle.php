<?php

namespace BuiltNorth\WPUtility\Utilities;

class GetTitle
{
	/**
	 * Get the title
	 */
	public static function render()
	{
		$title = '';

		// Ensure we have the correct query
		wp_reset_query();

		if (is_home() && get_option('page_for_posts')) {
			$title = get_the_title(get_option('page_for_posts'));
		} elseif (is_singular()) {
			$title = get_the_title();
		} elseif (is_tax()) {
			$title = single_term_title('', false);
		} elseif (is_archive() && !is_tax()) {
			$title = get_the_archive_title('', false);
		} elseif (is_category()) {
			$title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = get_the_author();
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		} elseif (is_search()) {
			$title = __('Search Results For: ', 'built-starter') . '"' . get_search_query() . '"';
		} elseif (is_404()) {
			$title = __('Page Not Found', 'built-starter');
		} else {
			$title = __('Check built_get_title() Function', 'built-starter');
		}

		return $title;
	}
}
