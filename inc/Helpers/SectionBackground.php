<?php

namespace BuiltNorth\Utility\Helpers;

use BuiltNorth\Utility\Component;

class SectionBackground
{
	/**
	 * Render section background with image, opacity, and focal point support.
	 * 
	 * @param array $attributes Block attributes array.
	 * @return string Rendered background HTML or empty string.
	 */
	public static function render($attributes)
	{
		$background_image = !empty($attributes['backgroundImage']) ? intval($attributes['backgroundImage']) : 0;
		$use_featured_image = !empty($attributes['useFeaturedImage']) ? $attributes['useFeaturedImage'] : false;

		if (!$background_image && !$use_featured_image) {
			return '';
		}

		$classes = [];
		$styles = [];

		// Image style class
		if (!empty($attributes['imageStyle']) && $attributes['imageStyle'] !== 'none') {
			$classes[] = "has-{$attributes['imageStyle']}";
		}

		// Opacity style
		if (!empty($attributes['opacity']) && is_numeric($attributes['opacity'])) {
			$opacity = intval($attributes['opacity']);
			if ($opacity >= 0 && $opacity <= 100) {
				$styles[] = 'opacity: ' . ($opacity / 100) . ';';
			}
		}

		// Focal point style
		if (!empty($attributes['focalPoint']) && is_array($attributes['focalPoint'])) {
			$x = ($attributes['focalPoint']['x'] ?? 0.5) * 100;
			$y = ($attributes['focalPoint']['y'] ?? 0.5) * 100;
			$styles[] = "object-position: {$x}% {$y}%;";
		}

		$image_class = 'section-background';
		$additional_classes = implode(' ', $classes);
		$image_style = implode(' ', $styles);

		ob_start();
?>
		<div class="section-background">
			<?php
			if ($use_featured_image) {
				// Get the featured image ID
				$featured_image_id = get_post_thumbnail_id();
				if ($featured_image_id) {
					echo Component::Image(
						id: $featured_image_id,
						class: $image_class,
						additional_classes: $additional_classes,
						include_figure: false,
						size: 'wide_xlarge',
						max_width: '1600px',
						lazy: false,
						style: $image_style,
					);
				}
			} else {
				echo Component::Image(
					id: $background_image,
					class: $image_class,
					additional_classes: $additional_classes,
					include_figure: false,
					size: 'wide_xlarge',
					max_width: '1600px',
					lazy: false,
					style: $image_style,
				);
			}
			?>
		</div>
<?php
		return ob_get_clean();
	}
}
