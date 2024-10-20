<?php

namespace BuiltNorth\Utility\Components;

class AccessibleCard
{
	public static function render(
		$link = '#',
		$target = null,
		$screen_reader = 'Read more about ...',
		$class = null,
	) {

		// Add target
		if ($target) {
			$target = 'target="' . $target . '"';
		}

		// Add class
		if ($class) {
			$class = $class . '__accessible-card-link ';
		}

		echo
		'<a class="' . $class . 'accessible-card-link" aria-hidden="true" tabindex="-1" href="' . $link . '"' . $target . '>' .
			'<span class="screen-reader-text">' . $screen_reader . '</span>' .
			'</a>';
	}
}
