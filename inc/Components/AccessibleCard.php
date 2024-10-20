<?php

namespace BuiltNorth\Utility\Components;

class AccessibleCard
{
	public static function render(
		$link = '#',
		$target = null,
		$screen_reader = 'Read more about ...'
	) {

		// Add target
		if ($target) {
			$target = 'target="' . $target . '"';
		}

		echo
		'<a aria-hidden="true" tabindex="-1" href="' . $link . '"' . $target . ' class="accessible-card-link">' .
			'<span class="screen-reader-text">' . $screen_reader . '</span>' .
			'</a>';
	}
}
