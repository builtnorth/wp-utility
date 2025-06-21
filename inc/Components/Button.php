<?php

namespace BuiltNorth\Utility\Components;

class Button
{
	public static function render(
		$button_type = 'a',
		$class = null,
		$style = 'default',
		$size = 'default',
		$text = 'Button Text',
		$link = null,
		$target = null,
		$screen_reader = null,
		$attributes = null
	) {

		// Add screen reader text
		if ($screen_reader) {
			$screen_reader = '<span class="screen-reader-text">' . $screen_reader . '</span>';
		}

		// Add target
		if ($target) {
			$target = 'target="' . $target . '"';
		}

		// Add class
		if ($class) {
			$wrapper_class = $class . '__button ';
			$link_class = $class . '__button-link ';
		} else {
			$wrapper_class = null;
			$link_class = null;
		}

		if ($link) {
			$link = 'href="' . $link . '"';
		}
		else {
			$link = null;
		}

		// Add attributes
		if ($attributes) {
			$attributes = ' ' . $attributes;
		}

		$target = $target ? ' ' . $target : '';
		$attributes = $attributes ? ' ' . $attributes : '';

		// For button elements, don't wrap text in span to avoid click event issues
		if ($button_type === 'button') {
			$button = '<' . $button_type . ' class="' . $wrapper_class . 'polaris-button is-style-' . $style . ' is-size-' . $size . '"' . $link . $target . $attributes . '>' .
				$text . $screen_reader .
				'</' . $button_type . '>';
		} else {
			$button = '<' . $button_type . ' class="' . $wrapper_class . 'polaris-button is-style-' . $style . ' is-size-' . $size . '"' . $link . $target . $attributes . '>' .
				'<span class="' . $link_class . 'polaris-button__text">' . $text . '</span>' .
				$screen_reader .
				'</' . $button_type . '>';
		}

		echo $button;
	}
}
