<?php
/**
 * AccessibleCard Component
 *
 * Renders an accessible card component with proper ARIA attributes
 * and screen reader support for improved accessibility.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Components
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Components;

class AccessibleCard
{
	public static function render(
		$link = null,
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
		'<a class="' . $class . 'accessible-card-link" href="' . $link . '"' . $target . '>' .
			'<span class="screen-reader-only">' . $screen_reader . '</span>' .
			'</a>';
	}
}
