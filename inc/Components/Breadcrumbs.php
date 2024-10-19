<?php

namespace BuiltNorth\Utility\Components;

class Breadcrumbs
{
	public static function render($show_on_front = null)
	{
		// Settings
		$separator = '&raquo;';
		$breadcrumbs_class = 'breadcrumbs';
		$home_title = 'Home';
		$prefix = null;

		// If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
		$custom_taxonomy = false;

		// Get the query & post information
		global $post, $wp_query;

		// Do not display on the homepage
		if ((is_front_page() && ($show_on_front == true)) || (!is_front_page())) {

			// Build the breadcrumbs
			echo '<nav class="' . $breadcrumbs_class . '">';
			echo '<ol class="' . $breadcrumbs_class . '__list">';

			// Home page
			echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--home">';
			echo '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a>';
			echo '</li>';
			echo '<li class="' . $breadcrumbs_class . '__separator"> ' . $separator . ' </li>';

			if (is_archive() && !is_tax() && !is_category() && !is_tag()) {
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current">';
				echo '<strong class="' . $breadcrumbs_class . '__current">' . post_type_archive_title($prefix, false) . '</strong>';
				echo '</li>';
			} else if (is_archive() && is_tax() && !is_category() && !is_tag()) {
				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--custom-post-type-' . $post_type . '">';
					echo '<a class="' . $breadcrumbs_class . '__link" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>';
					echo '</li>';
					echo '<li class="' . $breadcrumbs_class . '__separator"> ' . $separator . ' </li>';
				}

				$custom_tax_name = get_queried_object()->name;
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current">';
				echo '<strong class="' . $breadcrumbs_class . '__current">' . $custom_tax_name . '</strong>';
				echo '</li>';
			} else if (is_single()) {
				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--custom-post-type-' . $post_type . '">';
					echo '<a class="' . $breadcrumbs_class . '__link" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a>';
					echo '</li>';
					echo '<li class="' . $breadcrumbs_class . '__separator"> ' . $separator . ' </li>';
				}

				// Get post category info
				$category = get_the_category();

				if (!empty($category)) {
					// Get last category post is in
					$end = array_values($category);
					$last_category = end($end);

					// Get parent any categories and create array
					$get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','), ',');
					$cat_parents = explode(',', $get_cat_parents);

					// Loop through parent categories and store in variable $cat_display
					$cat_display = '';
					foreach ($cat_parents as $parents) {
						$cat_display .= '<li class="' . $breadcrumbs_class . '__item">' . $parents . '</li>';
						$cat_display .= '<li class="' . $breadcrumbs_class . '__separator"> ' . $separator . ' </li>';
					}
				}

				// If it's a custom post type within a custom taxonomy
				$taxonomy_exists = taxonomy_exists($custom_taxonomy);
				if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
					$taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
					$cat_id = $taxonomy_terms[0]->term_id;
					$cat_nicename = $taxonomy_terms[0]->slug;
					$cat_link = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
					$cat_name = $taxonomy_terms[0]->name;
				}

				// Check if the post is in a category
				if (!empty($last_category)) {
					echo $cat_display;
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--' . $post->ID . '">';
					echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong>';
					echo '</li>';

					// Else if post is in a custom taxonomy
				} else if (!empty($cat_id)) {
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--cat-' . $cat_id . ' ' . $breadcrumbs_class . '__item--cat-' . $cat_nicename . '">';
					echo '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--cat-' . $cat_id . ' ' . $breadcrumbs_class . '__link--cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a>';
					echo '</li>';
					echo '<li class="' . $breadcrumbs_class . '__separator"> ' . $separator . ' </li>';
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--' . $post->ID . '">';
					echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong>';
					echo '</li>';
				} else {
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--' . $post->ID . '">';
					echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong>';
					echo '</li>';
				}
			} else if (is_category()) {
				// Category page
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current">';
				echo '<strong class="' . $breadcrumbs_class . '__current">' . single_cat_title('', false) . '</strong>';
				echo '</li>';
			} else if (is_page()) {
				// Standard page
				if ($post->post_parent) {
					// If child page, get parents 
					$anc = get_post_ancestors($post->ID);

					// Get parents in the right order
					$anc = array_reverse($anc);

					// Parent page loop
					if (!isset($parents)) $parents = null;
					foreach ($anc as $ancestor) {
						$parents .= '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--parent ' . $breadcrumbs_class . '__item--parent-' . $ancestor . '">';
						$parents .= '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--parent ' . $breadcrumbs_class . '__link--parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a>';
						$parents .= '</li>';
						$parents .= '<li class="' . $breadcrumbs_class . '__separator ' . $breadcrumbs_class . '__separator--' . $ancestor . '"> ' . $separator . ' </li>';
					}

					// Display parent pages
					echo $parents;

					// Current page
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--' . $post->ID . '">';
					echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong>';
					echo '</li>';
				} else {
					// Just display current page if not parents
					echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--' . $post->ID . '">';
					echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $post->ID . '">' . get_the_title() . '</strong>';
					echo '</li>';
				}
			} else if (is_tag()) {
				// Tag page

				// Get tag information
				$term_id = get_query_var('tag_id');
				$taxonomy = 'post_tag';
				$args = 'include=' . $term_id;
				$terms = get_terms($taxonomy, $args);
				$get_term_id = $terms[0]->term_id;
				$get_term_slug = $terms[0]->slug;
				$get_term_name = $terms[0]->name;

				// Display the tag name
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--tag-' . $get_term_id . ' ' . $breadcrumbs_class . '__item--tag-' . $get_term_slug . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--tag-' . $get_term_id . ' ' . $breadcrumbs_class . '__current--tag-' . $get_term_slug . '" title="Search results for: ' . $get_term_name . '">' . $get_term_name . '</strong>';
				echo '</li>';
			} elseif (is_day()) {
				// Day archive

				// Year link
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--year ' . $breadcrumbs_class . '__item--year-' . get_the_time('Y') . '">';
				echo '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--year ' . $breadcrumbs_class . '__link--year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a>';
				echo '</li>';
				echo '<li class="' . $breadcrumbs_class . '__separator ' . $breadcrumbs_class . '__separator--' . get_the_time('Y') . '"> ' . $separator . ' </li>';

				// Month link
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--month ' . $breadcrumbs_class . '__item--month-' . get_the_time('m') . '">';
				echo '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--month ' . $breadcrumbs_class . '__link--month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a>';
				echo '</li>';
				echo '<li class="' . $breadcrumbs_class . '__separator ' . $breadcrumbs_class . '__separator--' . get_the_time('m') . '"> ' . $separator . ' </li>';

				// Day display
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--' . get_the_time('j') . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong>';
				echo '</li>';
			} else if (is_month()) {
				// Month Archive

				// Year link
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--year ' . $breadcrumbs_class . '__item--year-' . get_the_time('Y') . '">';
				echo '<a class="' . $breadcrumbs_class . '__link ' . $breadcrumbs_class . '__link--year ' . $breadcrumbs_class . '__link--year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a>';
				echo '</li>';
				echo '<li class="' . $breadcrumbs_class . '__separator ' . $breadcrumbs_class . '__separator--' . get_the_time('Y') . '"> ' . $separator . ' </li>';

				// Month display
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--month ' . $breadcrumbs_class . '__item--month-' . get_the_time('m') . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong>';
				echo '</li>';
			} else if (is_year()) {
				// Display year archive
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--current-' . get_the_time('Y') . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong>';
				echo '</li>';
			} else if (is_author()) {
				// Auhor archive

				// Get the author information
				global $author;
				$userdata = get_userdata($author);

				// Display author name
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--current-' . $userdata->user_nicename . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong>';
				echo '</li>';
			} else if (get_query_var('paged')) {
				// Paginated archives
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--current-' . get_query_var('paged') . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Page') . ' ' . get_query_var('paged') . '</strong>';
				echo '</li>';
			} else if (is_search()) {
				// Search results page
				echo '<li class="' . $breadcrumbs_class . '__item ' . $breadcrumbs_class . '__item--current ' . $breadcrumbs_class . '__item--current-' . get_search_query() . '">';
				echo '<strong class="' . $breadcrumbs_class . '__current ' . $breadcrumbs_class . '__current--' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong>';
				echo '</li>';
			} elseif (is_404()) {
				// 404 page
				echo '<li>' . 'Error 404' . '</li>';
			} elseif (is_home() && get_option('page_for_posts')) {
				echo '<li>' . get_the_title(get_option('page_for_posts')) . '</li>';
			}

			echo '</ol>';
			echo '</nav>';
		}
	}
}
