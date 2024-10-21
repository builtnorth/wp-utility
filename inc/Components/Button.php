<?php

namespace BuiltNorth\Utility\Components;

class Button
{
	public static function render(
		$class = null,
		$style = 'default',
		$text = 'Button Text',
		$link = '#',
		$target = null,
		$screen_reader = null
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

		echo
		'<div class="' . $wrapper_class . 'wp-block-button is-style-' . $style . '">' .
			'<a class="' . $link_class . 'wp-block-button__link wp-element-button" href="' . $link . '"' . $target . '>' .
			$text .
			$screen_reader .
			'</a>' .
			'</div>';
	}
}
