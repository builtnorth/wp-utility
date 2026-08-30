<?php
/**
 * Button Component
 *
 * Renders flexible button or link elements with support for icons,
 * styles, sizes, and screen reader text.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Components
 * @since 1.0.0
 */

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
			$screen_reader = '<span class="screen-reader-only">' . esc_html($screen_reader) . '</span>';
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

	/**
	 * Resolve supplemental screen reader text for a button link.
	 *
	 * @param array<string, mixed> $args {
	 *     @type string $explicit         Manual override (wins over auto context).
	 *     @type string $text             Visible button label (plain text).
	 *     @type string $link             Button href.
	 *     @type int    $post_id          Post ID from block/query context.
	 *     @type bool   $is_permalink     Whether the link is the post permalink.
	 *     @type bool   $opens_in_new_tab Whether the link opens in a new tab.
	 *     @type string $text_domain      Optional. Text domain for translatable fragments.
	 * }
	 * @return string|null Screen reader string, or null when none is needed.
	 */
	public static function resolve_screen_reader_text(array $args): ?string
	{
		$explicit = isset($args['explicit']) ? trim((string) $args['explicit']) : '';
		$text = isset($args['text']) ? wp_strip_all_tags((string) $args['text']) : '';
		$link = isset($args['link']) ? (string) $args['link'] : '';
		$post_id = isset($args['post_id']) ? (int) $args['post_id'] : 0;
		$is_permalink = !empty($args['is_permalink']);
		$opens_in_new_tab = !empty($args['opens_in_new_tab']);
		$text_domain = isset($args['text_domain']) ? (string) $args['text_domain'] : '';

		$parts = [];

		if ($explicit !== '') {
			$parts[] = $explicit;
		} else {
			$context_suffix = self::get_post_context_screen_reader_suffix(
				$text,
				$link,
				$post_id,
				$is_permalink
			);
			if ($context_suffix !== '') {
				$parts[] = $context_suffix;
			}
		}

		if ($opens_in_new_tab) {
			$new_window = '(opens in a new window)';
			$parts[] = $text_domain !== ''
				? __($new_window, $text_domain)
				: __($new_window);
		}

		$inner = trim(implode(' ', array_map(
			static fn($part) => trim((string) $part),
			array_filter($parts, static fn($part) => trim((string) $part) !== '')
		)));

		// Leading space: visible label and SR span are siblings; ATs do not insert a gap automatically.
		$result = $inner !== '' ? ' ' . $inner : '';

		/**
		 * Filter resolved supplemental screen reader text for a button.
		 *
		 * @param string|null $result Resolved text (leading space when non-empty), or null when empty.
		 * @param array       $args   Arguments passed to resolve_screen_reader_text().
		 */
		$filtered = apply_filters('wp_utility_button_screen_reader_text', $result !== '' ? $result : null, $args);

		return is_string($filtered) && trim($filtered) !== '' ? $filtered : null;
	}

	/**
	 * @return string[] Lowercase generic link labels that need post context.
	 */
	public static function get_generic_link_labels(): array
	{
		$labels = [
			'learn more',
			'read more',
			'view more',
			'find out more',
			'see more',
			'continue reading',
			'view details',
			'read more about',
		];

		/**
		 * Filter generic button labels that should receive post-title context in loops.
		 *
		 * @param string[] $labels Lowercase label strings.
		 */
		return apply_filters('wp_utility_button_generic_link_labels', $labels);
	}

	/**
	 * @return bool Whether the visible label is a generic link phrase.
	 */
	public static function is_generic_link_label(string $text): bool
	{
		$normalized = strtolower(trim(wp_strip_all_tags($text)));

		if ($normalized === '') {
			return false;
		}

		return in_array($normalized, self::get_generic_link_labels(), true);
	}

	/**
	 * Post-title suffix for query/card buttons (leading space for accessible name concatenation).
	 */
	private static function get_post_context_screen_reader_suffix(
		string $text,
		string $link,
		int $post_id,
		bool $is_permalink
	): string
	{
		if ($post_id <= 0) {
			return '';
		}

		$title = get_the_title($post_id);
		if ($title === '') {
			return '';
		}

		$should_append = $is_permalink;

		if (!$should_append) {
			$permalink = get_permalink($post_id);
			if ($link === '' || ($permalink && untrailingslashit($link) === untrailingslashit($permalink))) {
				$should_append = true;
			} elseif (self::is_generic_link_label($text)) {
				$should_append = true;
			}
		}

		if (!$should_append) {
			return '';
		}

		return $title;
	}
}
