<?php

namespace BuiltNorth\Utility\Helpers;

use BuiltNorth\Utility\Component;

class ForegroundImage
{
	/**
	 * Render foreground image with focal point support.
	 * 
	 * @param array $attributes Block attributes array.
	 * @param array $options Image rendering options.
	 * @return string Rendered image HTML or empty string.
	 */
	public static function render($attributes, $options = [])
	{
		$background_image = !empty($attributes['backgroundImage']) ? intval($attributes['backgroundImage']) : 0;

		if (!$background_image) {
			return '';
		}

		$styles = [];

		// Focal point style
		if (!empty($attributes['focalPoint']) && is_array($attributes['focalPoint'])) {
			$x = ($attributes['focalPoint']['x'] ?? 0.5) * 100;
			$y = ($attributes['focalPoint']['y'] ?? 0.5) * 100;
			$styles[] = "object-position: {$x}% {$y}%;";
		}

		$image_style = implode(' ', $styles);

		// Set default options
		$defaults = [
			'class' => '',
			'show_caption' => false,
			'size' => 'large',
			'max_width' => '600px',
		];

		$options = array_merge($defaults, $options);

		return Component::Image(
			id: $background_image,
			class: $options['class'],
			show_caption: $options['show_caption'],
			size: $options['size'],
			max_width: $options['max_width'],
			style: $image_style,
		);
	}
} 