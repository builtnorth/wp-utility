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
		$max_width = '1200px',
		$style = null  // New optional parameter
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

			// Add style to img attributes if provided
			$style = $style ? " style='$style'" : '';

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
							$style
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
							$style
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
	 * Country List
	 */
	public static function array_countries()
	{
		return [
			'AF' => __('Afghanistan', 'polaris-forms'),
			'AL' => __('Albania', 'polaris-forms'),
			'DZ' => __('Algeria', 'polaris-forms'),
			'AD' => __('Andorra', 'polaris-forms'),
			'AO' => __('Angola', 'polaris-forms'),
			'AG' => __('Antigua and Barbuda', 'polaris-forms'),
			'AR' => __('Argentina', 'polaris-forms'),
			'AM' => __('Armenia', 'polaris-forms'),
			'AU' => __('Australia', 'polaris-forms'),
			'AT' => __('Austria', 'polaris-forms'),
			'AZ' => __('Azerbaijan', 'polaris-forms'),
			'BS' => __('Bahamas', 'polaris-forms'),
			'BH' => __('Bahrain', 'polaris-forms'),
			'BD' => __('Bangladesh', 'polaris-forms'),
			'BB' => __('Barbados', 'polaris-forms'),
			'BY' => __('Belarus', 'polaris-forms'),
			'BE' => __('Belgium', 'polaris-forms'),
			'BZ' => __('Belize', 'polaris-forms'),
			'BJ' => __('Benin', 'polaris-forms'),
			'BT' => __('Bhutan', 'polaris-forms'),
			'BO' => __('Bolivia', 'polaris-forms'),
			'BA' => __('Bosnia and Herzegovina', 'polaris-forms'),
			'BW' => __('Botswana', 'polaris-forms'),
			'BR' => __('Brazil', 'polaris-forms'),
			'BN' => __('Brunei', 'polaris-forms'),
			'BG' => __('Bulgaria', 'polaris-forms'),
			'BF' => __('Burkina Faso', 'polaris-forms'),
			'BI' => __('Burundi', 'polaris-forms'),
			'CV' => __('Cabo Verde', 'polaris-forms'),
			'KH' => __('Cambodia', 'polaris-forms'),
			'CM' => __('Cameroon', 'polaris-forms'),
			'CA' => __('Canada', 'polaris-forms'),
			'CF' => __('Central African Republic', 'polaris-forms'),
			'TD' => __('Chad', 'polaris-forms'),
			'CL' => __('Chile', 'polaris-forms'),
			'CN' => __('China', 'polaris-forms'),
			'CO' => __('Colombia', 'polaris-forms'),
			'KM' => __('Comoros', 'polaris-forms'),
			'CD' => __('Congo, Democratic Republic of the', 'polaris-forms'),
			'CG' => __('Congo, Republic of the', 'polaris-forms'),
			'CR' => __('Costa Rica', 'polaris-forms'),
			'HR' => __('Croatia', 'polaris-forms'),
			'CU' => __('Cuba', 'polaris-forms'),
			'CY' => __('Cyprus', 'polaris-forms'),
			'CZ' => __('Czech Republic', 'polaris-forms'),
			'DK' => __('Denmark', 'polaris-forms'),
			'DJ' => __('Djibouti', 'polaris-forms'),
			'DM' => __('Dominica', 'polaris-forms'),
			'DO' => __('Dominican Republic', 'polaris-forms'),
			'EC' => __('Ecuador', 'polaris-forms'),
			'EG' => __('Egypt', 'polaris-forms'),
			'SV' => __('El Salvador', 'polaris-forms'),
			'GQ' => __('Equatorial Guinea', 'polaris-forms'),
			'ER' => __('Eritrea', 'polaris-forms'),
			'EE' => __('Estonia', 'polaris-forms'),
			'SZ' => __('Eswatini', 'polaris-forms'),
			'ET' => __('Ethiopia', 'polaris-forms'),
			'FJ' => __('Fiji', 'polaris-forms'),
			'FI' => __('Finland', 'polaris-forms'),
			'FR' => __('France', 'polaris-forms'),
			'GA' => __('Gabon', 'polaris-forms'),
			'GM' => __('Gambia', 'polaris-forms'),
			'GE' => __('Georgia', 'polaris-forms'),
			'DE' => __('Germany', 'polaris-forms'),
			'GH' => __('Ghana', 'polaris-forms'),
			'GR' => __('Greece', 'polaris-forms'),
			'GD' => __('Grenada', 'polaris-forms'),
			'GT' => __('Guatemala', 'polaris-forms'),
			'GN' => __('Guinea', 'polaris-forms'),
			'GW' => __('Guinea-Bissau', 'polaris-forms'),
			'GY' => __('Guyana', 'polaris-forms'),
			'HT' => __('Haiti', 'polaris-forms'),
			'HN' => __('Honduras', 'polaris-forms'),
			'HU' => __('Hungary', 'polaris-forms'),
			'IS' => __('Iceland', 'polaris-forms'),
			'IN' => __('India', 'polaris-forms'),
			'ID' => __('Indonesia', 'polaris-forms'),
			'IR' => __('Iran', 'polaris-forms'),
			'IQ' => __('Iraq', 'polaris-forms'),
			'IE' => __('Ireland', 'polaris-forms'),
			'IL' => __('Israel', 'polaris-forms'),
			'IT' => __('Italy', 'polaris-forms'),
			'JM' => __('Jamaica', 'polaris-forms'),
			'JP' => __('Japan', 'polaris-forms'),
			'JO' => __('Jordan', 'polaris-forms'),
			'KZ' => __('Kazakhstan', 'polaris-forms'),
			'KE' => __('Kenya', 'polaris-forms'),
			'KI' => __('Kiribati', 'polaris-forms'),
			'KP' => __('Korea, North', 'polaris-forms'),
			'KR' => __('Korea, South', 'polaris-forms'),
			'XK' => __('Kosovo', 'polaris-forms'),
			'KW' => __('Kuwait', 'polaris-forms'),
			'KG' => __('Kyrgyzstan', 'polaris-forms'),
			'LA' => __('Laos', 'polaris-forms'),
			'LV' => __('Latvia', 'polaris-forms'),
			'LB' => __('Lebanon', 'polaris-forms'),
			'LS' => __('Lesotho', 'polaris-forms'),
			'LR' => __('Liberia', 'polaris-forms'),
			'LY' => __('Libya', 'polaris-forms'),
			'LI' => __('Liechtenstein', 'polaris-forms'),
			'LT' => __('Lithuania', 'polaris-forms'),
			'LU' => __('Luxembourg', 'polaris-forms'),
			'MG' => __('Madagascar', 'polaris-forms'),
			'MW' => __('Malawi', 'polaris-forms'),
			'MY' => __('Malaysia', 'polaris-forms'),
			'MV' => __('Maldives', 'polaris-forms'),
			'ML' => __('Mali', 'polaris-forms'),
			'MT' => __('Malta', 'polaris-forms'),
			'MH' => __('Marshall Islands', 'polaris-forms'),
			'MR' => __('Mauritania', 'polaris-forms'),
			'MU' => __('Mauritius', 'polaris-forms'),
			'MX' => __('Mexico', 'polaris-forms'),
			'FM' => __('Micronesia', 'polaris-forms'),
			'MD' => __('Moldova', 'polaris-forms'),
			'MC' => __('Monaco', 'polaris-forms'),
			'MN' => __('Mongolia', 'polaris-forms'),
			'ME' => __('Montenegro', 'polaris-forms'),
			'MA' => __('Morocco', 'polaris-forms'),
			'MZ' => __('Mozambique', 'polaris-forms'),
			'MM' => __('Myanmar', 'polaris-forms'),
			'NA' => __('Namibia', 'polaris-forms'),
			'NR' => __('Nauru', 'polaris-forms'),
			'NP' => __('Nepal', 'polaris-forms'),
			'NL' => __('Netherlands', 'polaris-forms'),
			'NZ' => __('New Zealand', 'polaris-forms'),
			'NI' => __('Nicaragua', 'polaris-forms'),
			'NE' => __('Niger', 'polaris-forms'),
			'NG' => __('Nigeria', 'polaris-forms'),
			'MK' => __('North Macedonia', 'polaris-forms'),
			'NO' => __('Norway', 'polaris-forms'),
			'OM' => __('Oman', 'polaris-forms'),
			'PK' => __('Pakistan', 'polaris-forms'),
			'PW' => __('Palau', 'polaris-forms'),
			'PS' => __('Palestine', 'polaris-forms'),
			'PA' => __('Panama', 'polaris-forms'),
			'PG' => __('Papua New Guinea', 'polaris-forms'),
			'PY' => __('Paraguay', 'polaris-forms'),
			'PE' => __('Peru', 'polaris-forms'),
			'PH' => __('Philippines', 'polaris-forms'),
			'PL' => __('Poland', 'polaris-forms'),
			'PT' => __('Portugal', 'polaris-forms'),
			'QA' => __('Qatar', 'polaris-forms'),
			'RO' => __('Romania', 'polaris-forms'),
			'RU' => __('Russia', 'polaris-forms'),
			'RW' => __('Rwanda', 'polaris-forms'),
			'KN' => __('Saint Kitts and Nevis', 'polaris-forms'),
			'LC' => __('Saint Lucia', 'polaris-forms'),
			'VC' => __('Saint Vincent and the Grenadines', 'polaris-forms'),
			'WS' => __('Samoa', 'polaris-forms'),
			'SM' => __('San Marino', 'polaris-forms'),
			'ST' => __('Sao Tome and Principe', 'polaris-forms'),
			'SA' => __('Saudi Arabia', 'polaris-forms'),
			'SN' => __('Senegal', 'polaris-forms'),
			'RS' => __('Serbia', 'polaris-forms'),
			'SC' => __('Seychelles', 'polaris-forms'),
			'SL' => __('Sierra Leone', 'polaris-forms'),
			'SG' => __('Singapore', 'polaris-forms'),
			'SK' => __('Slovakia', 'polaris-forms'),
			'SI' => __('Slovenia', 'polaris-forms'),
			'SB' => __('Solomon Islands', 'polaris-forms'),
			'SO' => __('Somalia', 'polaris-forms'),
			'ZA' => __('South Africa', 'polaris-forms'),
			'SS' => __('South Sudan', 'polaris-forms'),
			'ES' => __('Spain', 'polaris-forms'),
			'LK' => __('Sri Lanka', 'polaris-forms'),
			'SD' => __('Sudan', 'polaris-forms'),
			'SR' => __('Suriname', 'polaris-forms'),
			'SE' => __('Sweden', 'polaris-forms'),
			'CH' => __('Switzerland', 'polaris-forms'),
			'SY' => __('Syria', 'polaris-forms'),
			'TW' => __('Taiwan', 'polaris-forms'),
			'TJ' => __('Tajikistan', 'polaris-forms'),
			'TZ' => __('Tanzania', 'polaris-forms'),
			'TH' => __('Thailand', 'polaris-forms'),
			'TL' => __('Timor-Leste', 'polaris-forms'),
			'TG' => __('Togo', 'polaris-forms'),
			'TO' => __('Tonga', 'polaris-forms'),
			'TT' => __('Trinidad and Tobago', 'polaris-forms'),
			'TN' => __('Tunisia', 'polaris-forms'),
			'TR' => __('Turkey', 'polaris-forms'),
			'TM' => __('Turkmenistan', 'polaris-forms'),
			'TV' => __('Tuvalu', 'polaris-forms'),
			'UG' => __('Uganda', 'polaris-forms'),
			'UA' => __('Ukraine', 'polaris-forms'),
			'AE' => __('United Arab Emirates', 'polaris-forms'),
			'GB' => __('United Kingdom', 'polaris-forms'),
			'US' => __('United States', 'polaris-forms'),
			'UY' => __('Uruguay', 'polaris-forms'),
			'UZ' => __('Uzbekistan', 'polaris-forms'),
			'VU' => __('Vanuatu', 'polaris-forms'),
			'VA' => __('Vatican City', 'polaris-forms'),
			'VE' => __('Venezuela', 'polaris-forms'),
			'VN' => __('Vietnam', 'polaris-forms'),
			'YE' => __('Yemen', 'polaris-forms'),
			'ZM' => __('Zambia', 'polaris-forms'),
			'ZW' => __('Zimbabwe', 'polaris-forms'),
		];
	}

	/**
	 * State List
	 */
	public static function array_us_states()
	{
		return [
			'AL' => __('Alabama', 'polaris-forms'),
			'AK' => __('Alaska', 'polaris-forms'),
			'AZ' => __('Arizona', 'polaris-forms'),
			'AR' => __('Arkansas', 'polaris-forms'),
			'CA' => __('California', 'polaris-forms'),
			'CO' => __('Colorado', 'polaris-forms'),
			'CT' => __('Connecticut', 'polaris-forms'),
			'DE' => __('Delaware', 'polaris-forms'),
			'DC' => __('District of Columbia', 'polaris-forms'),
			'FL' => __('Florida', 'polaris-forms'),
			'GA' => __('Georgia', 'polaris-forms'),
			'HI' => __('Hawaii', 'polaris-forms'),
			'ID' => __('Idaho', 'polaris-forms'),
			'IL' => __('Illinois', 'polaris-forms'),
			'IN' => __('Indiana', 'polaris-forms'),
			'IA' => __('Iowa', 'polaris-forms'),
			'KS' => __('Kansas', 'polaris-forms'),
			'KY' => __('Kentucky', 'polaris-forms'),
			'LA' => __('Louisiana', 'polaris-forms'),
			'ME' => __('Maine', 'polaris-forms'),
			'MD' => __('Maryland', 'polaris-forms'),
			'MA' => __('Massachusetts', 'polaris-forms'),
			'MI' => __('Michigan', 'polaris-forms'),
			'MN' => __('Minnesota', 'polaris-forms'),
			'MS' => __('Mississippi', 'polaris-forms'),
			'MO' => __('Missouri', 'polaris-forms'),
			'OK' => __('Oklahoma', 'polaris-forms'),
			'OR' => __('Oregon', 'polaris-forms'),
			'PA' => __('Pennsylvania', 'polaris-forms'),
			'RI' => __('Rhode Island', 'polaris-forms'),
			'SC' => __('South Carolina', 'polaris-forms'),
			'SD' => __('South Dakota', 'polaris-forms'),
			'TN' => __('Tennessee', 'polaris-forms'),
			'TX' => __('Texas', 'polaris-forms'),
			'UT' => __('Utah', 'polaris-forms'),
			'VT' => __('Vermont', 'polaris-forms'),
			'VA' => __('Virginia', 'polaris-forms'),
			'WA' => __('Washington', 'polaris-forms'),
			'WV' => __('West Virginia', 'polaris-forms'),
			'WI' => __('Wisconsin', 'polaris-forms'),
			'WY' => __('Wyoming', 'polaris-forms'),
		];
	}

	/**
	 * Breadcrumbs function
	 * @link https://gist.github.com/abelsaad/40d77f411b7fe37b8046cab221735f7d
	 */
	public static function breadcrumbs($show_on_front = null)
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
