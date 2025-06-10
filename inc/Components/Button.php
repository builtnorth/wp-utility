<?php

namespace BuiltNorth\Utility\Components;

class Button
{
	public static function render(
		$button_type = 'a',
		$class = null,
		$style = 'default',
		$text = 'Button Text',
		$link = '#',
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

		// Add attributes
		if ($attributes) {
			$attributes = ' ' . $attributes;
		}

		echo
		'<' . $button_type . ' class="' . $wrapper_class . 'polaris-button is-style-' . $style . '"' . $attributes . '>' .
			'<span class="' . $link_class . 'polaris-button__text" href="' . $link . '"' . $target . '>' .
			$text .
			$screen_reader .
			'</span>' .
			'</' . $button_type . '>';
	}
}
