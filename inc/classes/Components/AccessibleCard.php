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
		$href_attr = $link ? ' href="' . esc_url( (string) $link ) . '"' : '';
		$target_attr = $target ? ' target="' . esc_attr( (string) $target ) . '"' : '';

		$class_prefix = $class
			? esc_attr( (string) $class ) . '__accessible-card-link '
			: '';

		echo
		'<a class="' . $class_prefix . 'accessible-card-link"' . $href_attr . $target_attr . '>' .
			'<span class="screen-reader-only">' . esc_html( (string) $screen_reader ) . '</span>' .
			'</a>';
	}
}
