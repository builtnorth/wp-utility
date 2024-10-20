<?php

namespace BuiltNorth\Utility\Components;

class Button
{
	public static function render(
		$style = null,
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

		echo
		'<div class="wp-block-button ' . $style . '">' .
			'<a class="wp-block-button__link wp-element-button" href="' . $link . '"' . $target . '>' .
			$text .
			$screen_reader .
			'</a>' .
			'</div>';
	}
}
