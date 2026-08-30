<?php

/**
 * ------------------------------------------------------------------
 * Class: Image
 * ------------------------------------------------------------------
 *
 * This class is responsible for rendering an image.
 *
 * @package BuiltNorth/Utility
 * @since BuiltNorth/Utility 1.0.0
 **/

namespace BuiltNorth\WPUtility\Components;

class Image
{
	/**
	 * Build a responsive sizes attribute string.
	 *
	 * Returns a sizes value suitable for the `sizes` param of Image::render().
	 * When passed to a lazy-loaded image, `auto` is automatically prepended so
	 * supporting browsers can measure the rendered width instead.
	 *
	 * @param int $desktop_vw        Percentage of viewport width at desktop (e.g. 50 for a half-width column).
	 * @param int $mobile_breakpoint Breakpoint in px below which the image is 100vw. Default 782 (WP/Gutenberg stack point).
	 * @return string  e.g. "(max-width: 782px) 100vw, 50vw"
	 */
	public static function sizes(int $desktop_vw = 100, int $mobile_breakpoint = 782): string
	{
		if ($desktop_vw >= 100) {
			return '100vw';
		}
		return "(max-width: {$mobile_breakpoint}px) 100vw, {$desktop_vw}vw";
	}

	/**
	 * Render an image.
	 *
	 * @param int         $id                 The image ID.
	 * @param string      $class              Optional. The class to add to the image.
	 * @param string      $additional_classes Optional. Extra classes to add to the image.
	 * @param string      $custom_alt         Optional. The custom alt text.
	 * @param bool        $show_caption       Optional. Whether to show the caption.
	 * @param bool        $lazy               Optional. Whether to use lazy loading.
	 * @param string      $wrap_class         Optional. The class to add to the figure.
	 * @param bool        $include_figure     Optional. Whether to include the figure.
	 * @param string      $size               Optional. The WordPress image size.
	 * @param string      $max_width          Optional. Max-width used to build the default sizes fallback.
	 * @param string      $style              Optional. Inline style for the img element.
	 * @param string      $caption            Optional. Caption text.
	 * @param string      $alt                Optional. Alt text override.
	 * @param string|null $sizes              Optional. Custom sizes value (use Image::sizes() to generate).
	 *                                        When null, derived from $max_width. For lazy images, `auto` is
	 *                                        automatically prepended as a progressive enhancement.
	 */
	public static function render(
		$id = null,
		$class = null,
		$additional_classes = null,
		$custom_alt = null,
		$show_caption = null,
		$lazy = true,
		$wrap_class = 'standard',
		$include_figure = true,
		$size = 'full',
		$max_width = '1200px',
		$style = null,
		$caption = '',
		$alt = '',
		$sizes = null,
	) {
		// Check the image ID is not empty
		if (empty($id)) {
			return '';
		}

		// Capture before $lazy is reassigned to a string below.
		$is_lazy = (bool) $lazy;

		// Image src and srcset
		$src = wp_get_attachment_image_url($id, $size) ?: '';
		$srcset = wp_get_attachment_image_srcset($id, $size) ?: '';

		// Image alt and caption
		$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: '';
		$image_caption = wp_get_attachment_caption($id) ?: '';

		// Image attributes
		$attributes = wp_get_attachment_image_src($id, $size);

		// Set width & height - handle SVG files which may not have dimensions
		$width = (isset($attributes[1]) && $attributes[1]) ? (string) $attributes[1] : '';
		$height = (isset($attributes[2]) && $attributes[2]) ? (string) $attributes[2] : '';
		
		// For SVG files, check if we can get dimensions from the file itself
		if (empty($width) || empty($height)) {
			$mime_type = get_post_mime_type($id);
			if ($mime_type === 'image/svg+xml') {
				// SVGs don't have inherent dimensions in WordPress metadata
				// Set reasonable defaults or leave empty for responsive SVGs
				$width = '';
				$height = '';
			}
		}

		// Set alt text - use parameter alt if provided, otherwise custom_alt, otherwise image_alt
		$final_alt = '';
		if (!empty($alt)) {
			$final_alt = (string) $alt;
		} elseif (!empty($custom_alt)) {
			$final_alt = (string) $custom_alt;
		} elseif (!empty($image_alt)) {
			$final_alt = (string) $image_alt;
		}
		
		// add class
		$class = $class ? esc_attr((string) $class) : 'image';

		// add additional classes
		$additional_classes = $additional_classes ? (string) $additional_classes : '';

		// Add caption
		$caption_str = (string) $caption;
		$caption_html = ($show_caption === true && !empty($caption_str)) ? '<figcaption class="' . esc_attr($class) . '__caption">' . esc_html($caption_str) . '</figcaption>' : '';

		// Set lazy loading
		$lazy = $lazy ? 'loading=lazy decoding=async' : 'loading=eager decoding=sync fetchpriority="high"';

		// Add style to img attributes if provided
		if ($style) {
			// Handle both string and array styles
			if (is_array($style)) {
				$style_string = implode('; ', array_filter($style));
			} else {
				$style_string = $style;
			}
			$style_attr = " style='" . esc_attr($style_string) . "'";
		} else {
			$style_attr = '';
		}

		// Build sizes attribute: use explicit $sizes when provided, otherwise derive from $max_width.
		$sizes_attr = !empty($sizes)
			? esc_attr((string) $sizes)
			: '(max-width: ' . esc_attr((string) $max_width) . ') 100vw, ' . esc_attr((string) $max_width);

		// Prepend `auto` for lazy images — supporting browsers measure the actual rendered width
		// and use that instead of the static hint; non-supporting browsers use the fallback value.
		if ($is_lazy) {
			$sizes_attr = 'auto, ' . $sizes_attr;
		}

		// Build the img tag - ensure all values are strings for escaping functions
		$img_tag = "<img
			$lazy 
			class='" . esc_attr($class . "__img " . $additional_classes) . "'
			alt='" . esc_attr((string) $final_alt) . "'
			src='" . esc_url((string) $src) . "'
			srcset='" . esc_attr((string) $srcset) . "'
			sizes='" . $sizes_attr . "'
			width='" . esc_attr((string) $width) . "'
			height='" . esc_attr((string) $height) . "'
			$style_attr
		/>";

		// Include figure
		if ($include_figure) {
			echo "<figure class='" . esc_attr($class) . "__figure'>
				$img_tag
				$caption_html 
			</figure>";
		} else {
			echo $img_tag;
		}
	}
}
