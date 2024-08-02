<?php

/**
 * ------------------------------------------------------------------
 * Class: Utility
 * ------------------------------------------------------------------
 *
 * This class contains useful utility & helper functions for the built-starter theme.
 * 
 * Usage examples. Be sure to properly namespace any files that use the utility functions.
 * Utility::image($id, $class, $custom_alt);
 * Utility::button($style, $text, $link);
 * $title = Utility::get_title();
 *
 * @package BuiltStarter
 * @since BuiltStarter 2.0.0
 * 
 **/

namespace BuiltNorth\Utility;

class Utility
{

	/**
	 * Responsive Image Helper
	 */
	public static function image(
		$id = null,
		$class = null,
		$custom_alt = null,
		$show_caption = null,
		$lazy = true,
		$wrap_class = 'standard',
		$include_figure = true,
		$size = 'full',
		$max_width = '1200px'
	) {

		// check the image ID is not empty
		if (!empty($id)) {

			// image src
			$src = wp_get_attachment_image_url($id, $size);

			// image srcset
			$srcset = wp_get_attachment_image_srcset($id, $size);

			// image alt 
			$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);

			// image caption
			$image_caption = wp_get_attachment_caption($id);

			// image attributes
			$attributes = wp_get_attachment_image_src($id, $size);

			// set width & height
			if (is_array($attributes) && count($attributes) >= 3) {
				$width = $attributes[1];
				$height = $attributes[2];
			} else {
				// Handle the case where attributes are not available
				$width = '';
				$height = '';
			}

			// set alt text
			if ($custom_alt) {
				$alt = $custom_alt;
			} else {
				$alt = $image_alt;
			}

			// add class
			if ($class) {
				$class = " class='$class'";
			} else {
				$class = null;
			}

			// add caption
			if ((true === $show_caption) && (!empty($image_caption))) {
				$caption = "<figcaption>$image_caption</figcaption>";
			} else {
				$caption = null;
			}

			// set lazy loading
			if (true == $lazy) {
				$lazy = 'loading=lazy decoding=async';
			} else {
				$lazy = 'loading=eager decoding=sync fetchpriority="high"';
			}

			// Include figure
			if (true == $include_figure) {

				return
					"<figure class='image__wrap image__wrap--$wrap_class'>
						<img
							$lazy 
							$class 
							alt='$alt'
							src='$src'
							srcset='$srcset'
							sizes='(max-width: $max_width) 100vw, $max_width'
							width='$width'
							height='$height'
						/>
						$caption 
					</figure>";
			} else {

				return
					"<img
							$lazy 
							$class 
							alt='$alt'
							src='$src'
							srcset='$srcset'
							sizes='(max-width: $max_width) 100vw, $max_width'
							width='$width'
							height='$height'
						/>";
			}
		}
	}

	/**
	 * Get Button
	 */
	public static function button(
		$style = null,
		$text = 'Button Text',
		$link = '#',
		$target = null,
		$screen_reader = null
	) {

		// Add screen reader text
		if ($screen_reader) {
			$screen_reader = '<span class="sr-only">' . $screen_reader . '</span>';
		}

		// Add target
		if ($target) {
			$target = 'target="' . $target . '"';
		}

		echo
		'<div class="wp-block-button ' . $style . '">' .
			'<a class="wp-block-button__link wp-element-button" href="' . $link . '"' . $target . '>' .
			$text .
			$screen_reader .
			'</a>' .
			'</div>';
	}

	/**
	 * Accessible card
	 */
	public static function accessible_card(
		$link = '#',
		$target = null,
		$screen_reader = 'Read more about ...'
	) {

		// Add target
		if ($target) {
			$target = 'target="' . $target . '"';
		}

		echo
		'<a aria-hidden="true" tabindex="-1" href="' . $link . '"' . $target . ' class="accessible-card-link">' .
			'<span class="sr-only">' . $screen_reader . '</span>' .
			'</a>';
	}

	/**
	 * Get the title
	 */
	public static function get_title()
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

	/**
	 * Set up global pagination
	 */
	public static function pagination()
	{

		global $wp_query;
		$big = 999999999; // need an unlikely integer
		$translated = __('Page', 'mytextdomain'); // Supply translatable string

		$paginate_links = paginate_links(array(
			'base'                => str_replace($big, '%#%', get_pagenum_link($big)),
			'current'             => max(1, get_query_var('paged')),
			'total'               => $wp_query->max_num_pages,
			'mid_size'            => 3,
			'type'                => 'list',
			'before_page_number'  => '<span class="sr-only">' . $translated . ' </span>',
		));

		// Display the pagination if more than one page is found
		if ($paginate_links) :
?>
			<div class="pagination" aria-label="Navigate Between Archive Pages">
				<?php echo $paginate_links; ?>
			</div>
<?php endif;
	}

	/**
	 * Get the terms
	 */
	public static function get_terms(
		$taxonomy = null,
		$taxonomy_link = false,
		$first_term_only = false
	) {

		// Get the terms
		$terms = get_the_terms(get_the_ID(), $taxonomy);

		// Make sure some exist
		if (!empty($terms) && !is_wp_error($terms)) :

			// Check if only firt term is requested
			if ($first_term_only) :

				// First term only
				$first_term = $terms[0];
				$name = $first_term->name;
				$link = get_term_link($first_term->term_id);

				// Add link if set
				if ($taxonomy_link) {
					echo '<span class="term-list__item"><a class="is-interior-link" href="' . $link . '">' . $name . '</a></span>';
				} else {
					echo '<span class="term-list__item">' . $name . '</span>';
				}

			else :

				echo '<ul class="term-list">';
				foreach ($terms as $term) :
					// Variables
					$name = $term->name;
					$link = get_term_link($term->term_id);

					// Add link if set
					if ($taxonomy_link) {
						echo '<li class="term-list__item"><a class="is-interior-link" href="' . $link . '">' . $name . '</a></li>';
					} else {
						echo '<li class="term-list__item">' . $name . '</li>';
					}

				endforeach;
				echo '</ul>';
			endif;
		endif;
	}


	/**
	 * Get parent block name
	 * @todo - modify to work for blocks that have a defined parent in block.json
	 */
	public static function first_block_lazy_image($block)
	{
		$lazy = true; // Default to true

		if (
			isset($block->block_type->parent) &&
			is_array($block->block_type->parent) &&
			!empty($block->block_type->parent)
		) {

			$parent_block = $block->block_type->parent[0];

			// echo $parent_block;

			$hero_area_blocks = [
				'built/hero-area-primary',
				'built/hero-area-secondary',
				'built/hero-area-section'
			];

			if (in_array($parent_block, $hero_area_blocks)) {
				$lazy = false;
			}
		}

		return $lazy;
	}


	/**
	 * Estimated Reading Time
	 * @link https://medium.com/@shaikh.nadeem/how-to-add-reading-time-in-wordpress-without-using-plugin-d2e8af7b1239
	 */
	public static function reading_time()
	{

		$word_count = str_word_count(strip_tags(get_the_content()));
		$readingtime = ceil($word_count / 200);
		return $readingtime;
	}

	/**
	 * Breadcrumbs function
	 * @link https://gist.github.com/abelsaad/40d77f411b7fe37b8046cab221735f7d
	 */
	public static function breadcrumbs($show_on_front = null)
	{

		// Settings
		$separator            = '&raquo;';
		$breadcrumbs_id       = 'breadcrumbs';
		$breadcrumbs_class    = 'breadcrumbs';
		$home_title           = 'Home';
		$prefix               = null;

		// If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
		// $custom_taxonomy    = 'product_cat';
		$custom_taxonomy    = false;

		// Get the query & post information
		global $post, $wp_query;

		// Do not display on the homepage
		if ((is_front_page() && ($show_on_front == true)) || (!is_front_page())) {

			// Build the breadcrums
			echo '<nav>';
			echo '<ol id="' . $breadcrumbs_id . '" class="' . $breadcrumbs_class . '">';

			// Home page
			echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
			echo '<li class="separator separator-home"> ' . $separator . ' </li>';

			if (is_archive() && !is_tax() && !is_category() && !is_tag()) {

				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</strong></li>';
			} else if (is_archive() && is_tax() && !is_category() && !is_tag()) {

				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {

					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
				}

				$custom_tax_name = get_queried_object()->name;
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';
			} else if (is_single()) {

				// If post is a custom post type
				$post_type = get_post_type();

				// If it is a custom post type display name and link
				if ($post_type != 'post') {

					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);

					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
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
						$cat_display .= '<li class="item-cat">' . $parents . '</li>';
						$cat_display .= '<li class="separator"> ' . $separator . ' </li>';
					}
				}

				// If it's a custom post type within a custom taxonomy
				$taxonomy_exists = taxonomy_exists($custom_taxonomy);
				if (empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {

					$taxonomy_terms = get_the_terms($post->ID, $custom_taxonomy);
					$cat_id         = $taxonomy_terms[0]->term_id;
					$cat_nicename   = $taxonomy_terms[0]->slug;
					$cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
					$cat_name       = $taxonomy_terms[0]->name;
				}

				// Check if the post is in a category
				if (!empty($last_category)) {
					echo $cat_display;
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';

					// Else if post is in a custom taxonomy
				} else if (!empty($cat_id)) {

					echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
				} else {

					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
				}
			} else if (is_category()) {

				// Category page
				echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';
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
						$parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
						$parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
					}

					// Display parent pages
					echo $parents;

					// Current page
					echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
				} else {

					// Just display current page if not parents
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
				}
			} else if (is_tag()) {

				// Tag page

				// Get tag information
				$term_id        = get_query_var('tag_id');
				$taxonomy       = 'post_tag';
				$args           = 'include=' . $term_id;
				$terms          = get_terms($taxonomy, $args);
				$get_term_id    = $terms[0]->term_id;
				$get_term_slug  = $terms[0]->slug;
				$get_term_name  = $terms[0]->name;

				// Display the tag name
				echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</strong></li>';
			} elseif (is_day()) {

				// Day archive

				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

				// Month link
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';

				// Day display
				echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
			} else if (is_month()) {

				// Month Archive

				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link(get_the_time('Y')) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';

				// Month display
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
			} else if (is_year()) {

				// Display year archive
				echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
			} else if (is_author()) {

				// Auhor archive

				// Get the author information
				global $author;
				$userdata = get_userdata($author);

				// Display author name
				echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
			} else if (get_query_var('paged')) {

				// Paginated archives
				echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">' . __('Page') . ' ' . get_query_var('paged') . '</strong></li>';
			} else if (is_search()) {

				// Search results page
				echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
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
