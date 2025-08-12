<?php

namespace BuiltNorth\WPUtility\Components;

class Button
{
	public static function render(
		$button_type = 'a',
		$class = null,
		$extra_class = null,
		$style = 'default',
		$size = 'default',
		$appearance = 'fill',
		$text = 'Button Text',
		$link = null,
		$target = null,
		$screen_reader = null,
		$attributes = null,
		$icon = null,
		$icon_position = 'left'
	) {

		// Add screen reader text
		if ($screen_reader) {
			$screen_reader = '<span class="screen-reader-only">' . $screen_reader . '</span>';
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

		/**
		 * Filter the button block class prefix.
		 * 
		 * @param string $prefix The button class prefix. Default 'wp-block-polaris-button'.
		 */
		$block_prefix = apply_filters('wp_utility_button_block_prefix', 'wp-block-polaris-button');

		// Prepare icon HTML
		$icon_left_html = '';
		$icon_right_html = '';
		if ($icon) {
			$icon_class = $block_prefix . '__icon ' . $block_prefix . '__icon--' . esc_attr($icon_position);
			$icon_html = '<span class="' . $icon_class . '">' . $icon . '</span>';
			
			if ($icon_position === 'right') {
				$icon_right_html = $icon_html;
			} else {
				$icon_left_html = $icon_html;
			}
		}

		// For button elements, don't wrap text in span to avoid click event issues
		if ($button_type === 'button') {
			$button = '<' . $button_type . ' class="' . $wrapper_class . $block_prefix . ' is-style-' . $style . ' is-size-' . $size . ' is-appearance-' . $appearance . ' ' . $extra_class . '"' . $link . $target . $attributes . '>' .
				$icon_left_html .
				$text . $screen_reader .
				$icon_right_html .
				'</' . $button_type . '>';
		} else {
			$button = '<' . $button_type . ' class="' . $wrapper_class . $block_prefix . ' is-style-' . $style . ' is-size-' . $size . ' is-appearance-' . $appearance . ' ' . $extra_class . '"' . $link . $target . $attributes . '>' .
				$icon_left_html .
				'<span class="' . $link_class . $block_prefix . '__text">' . $text . '</span>' .
				$screen_reader .
				$icon_right_html .
				'</' . $button_type . '>';
		}
		if ( $text ) {
			echo $button;
		}
	}
}
