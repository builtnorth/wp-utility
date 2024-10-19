<?php

namespace BuiltNorth\Utility\Utilities;

class GetTitle
{
	/**
	 * Get the title
	 */
	public static function render()
	{

		if (is_home() && get_option('page_for_posts')) {
			$title = get_the_title(get_option('page_for_posts'));
		} elseif (is_singular()) {
			$title = get_the_title();
		} elseif (is_category()) {
			$title = single_cat_title('', false);
		} elseif (is_tag()) {
			$title = single_tag_title('', false);
		} elseif (is_author()) {
			$title = get_the_author();
		} elseif (is_post_type_archive()) {
			$title = post_type_archive_title('', false);
		} elseif (is_tax()) {
			$title = single_term_title('', false);
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
