<?php

namespace BuiltNorth\Utility\Components;

class Image
{
	public static function render(
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
}
