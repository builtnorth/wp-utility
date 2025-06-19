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

namespace BuiltNorth\Utility\Components;

class Image
{
	/**
	 * Render an image
	 *
	 * @param int $id The image ID.
	 * @param string $class Optional. The class to add to the image.
	 * @param string $custom_alt Optional. The custom alt text to use for the image.
	 * @param bool $show_caption Optional. Whether to show the caption.
	 * @param bool $lazy Optional. Whether to use lazy loading.
	 * @param string $wrap_class Optional. The class to add to the figure.
	 * @param bool $include_figure Optional. Whether to include the figure.
	 * @param string $size Optional. The size of the image.
	 * @param string $max_width Optional. The maximum width of the image.
	 * @param string $style Optional. The style to add to the image.
	 * @return string The image HTML.
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
		$style = null
	) {
		// Check the image ID is not empty
		if (empty($id)) {
			return '';
		}

		// Image src and srcset
		$src = wp_get_attachment_image_url($id, $size);
		$srcset = wp_get_attachment_image_srcset($id, $size);

		// Image alt and caption
		$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
		$image_caption = wp_get_attachment_caption($id);

		// Image attributes
		$attributes = wp_get_attachment_image_src($id, $size);

		// Set width & height
		$width = $attributes[1] ?? '';
		$height = $attributes[2] ?? '';

		// Set alt text
		$alt = $custom_alt ?: $image_alt;
		// add class
		$class = $class ? esc_attr($class) : 'image';

		// add additional classes
		$additional_classes = $additional_classes ? $additional_classes : '';

		// Add caption
		$caption = ($show_caption === true && !empty($image_caption)) ? '<figcaption class="' . esc_attr($class) . '__caption">' . esc_html($image_caption) . '</figcaption>' : '';

		// Set lazy loading
		$lazy = $lazy ? 'loading=lazy decoding=async' : 'loading=eager decoding=sync fetchpriority="high"';

		// Add style to img attributes if provided
		$style = $style ? " style='" . esc_attr($style) . "'" : '';

		// Build the img tag	
		$img_tag = "<img
			$lazy 
			class='" . esc_attr($class) . "__img " . esc_attr($additional_classes) . "'
			alt='" . esc_attr($alt) . "'
			src='" . esc_url($src) . "'
			srcset='" . esc_attr($srcset) . "'
			sizes='(max-width: " . esc_attr($max_width) . ") 100vw, " . esc_attr($max_width) . "'
			width='" . esc_attr($width) . "'
			height='" . esc_attr($height) . "'
			$style
		/>";

		// Include figure
		if ($include_figure) {
			echo "<figure class='" . esc_attr($class) . "__figure'>
				$img_tag
				$caption 
			</figure>";
		} else {
			echo $img_tag;
		}
	}
}
