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
	/**
	 * Allowed tag names for $button_type.
	 *
	 * @var string[]
	 */
	private const ALLOWED_BUTTON_TYPES = [ 'a', 'button', 'span' ];

	/**
	 * Render a button or link element.
	 *
	 * Structural attributes (href, target, classes, style/size/appearance, tag)
	 * are escaped here. `$text`, `$icon`, and `$attributes` are intentional HTML
	 * slots — callers must sanitize them (e.g. wp_kses_post, EscapeSvg,
	 * get_block_wrapper_attributes).
	 *
	 * @param string      $button_type   Tag name: a|button|span. Default 'a'.
	 * @param string|null $class         Optional BEM class prefix.
	 * @param string|null $extra_class   Extra class names (caller-sanitized).
	 * @param string      $style         Style slug (sanitized to HTML class).
	 * @param string      $size          Size slug (sanitized to HTML class).
	 * @param string      $appearance    Appearance slug (sanitized to HTML class).
	 * @param string      $text          Visible label HTML (caller-sanitized).
	 * @param string|null $link          Href when rendering an anchor.
	 * @param string|null $target        Target attribute value.
	 * @param string|null $screen_reader Screen-reader-only text (escaped here).
	 * @param string|null $attributes    Raw attribute string (caller-sanitized).
	 * @param string|null $icon          Icon HTML (caller-sanitized).
	 * @param string      $icon_position Icon position: left|right.
	 */
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
		$button_type = in_array( (string) $button_type, self::ALLOWED_BUTTON_TYPES, true )
			? (string) $button_type
			: 'a';

		$style      = sanitize_html_class( (string) $style );
		$size       = sanitize_html_class( (string) $size );
		$appearance = sanitize_html_class( (string) $appearance );
		$extra_class = $extra_class !== null && $extra_class !== ''
			? esc_attr( (string) $extra_class )
			: '';

		// Add screen reader text
		if ($screen_reader) {
			$screen_reader = '<span class="screen-reader-only">' . esc_html($screen_reader) . '</span>';
		}

		// Add target
		$target_attr = $target
			? ' target="' . esc_attr( (string) $target ) . '"'
			: '';

		// Add class
		if ($class) {
			$class_safe     = esc_attr( (string) $class );
			$wrapper_class  = $class_safe . '__button ';
			$link_class     = $class_safe . '__button-link ';
		} else {
			$wrapper_class = '';
			$link_class    = '';
		}

		$link_attr = $link
			? ' href="' . esc_url( (string) $link ) . '"'
			: '';

		// Raw attribute slot — caller must sanitize (e.g. get_block_wrapper_attributes).
		$attributes_attr = $attributes ? ' ' . $attributes : '';

		/**
		 * Filter the button block class prefix.
		 *
		 * @param string $prefix The button class prefix. Default 'wp-block-polaris-button'.
		 */
		$block_prefix = apply_filters('wp_utility_button_block_prefix', 'wp-block-polaris-button');
		$block_prefix = esc_attr( (string) $block_prefix );

		// Prepare icon HTML ($icon is an intentional HTML slot).
		$icon_left_html = '';
		$icon_right_html = '';
		if ($icon) {
			$icon_class = $block_prefix . '__icon ' . $block_prefix . '__icon--' . esc_attr( (string) $icon_position );
			$icon_html = '<span class="' . $icon_class . '">' . $icon . '</span>';

			if ($icon_position === 'right') {
				$icon_right_html = $icon_html;
			} else {
				$icon_left_html = $icon_html;
			}
		}

		$class_attr = trim(
			$wrapper_class . $block_prefix .
			' is-style-' . $style .
			' is-size-' . $size .
			' is-appearance-' . $appearance .
			( $extra_class !== '' ? ' ' . $extra_class : '' )
		);

		// For button elements, don't wrap text in span to avoid click event issues.
		// $text is an intentional HTML slot (caller-sanitized).
		if ($button_type === 'button') {
			$button = '<' . $button_type . ' class="' . $class_attr . '"' . $link_attr . $target_attr . $attributes_attr . '>' .
				$icon_left_html .
				$text . $screen_reader .
				$icon_right_html .
				'</' . $button_type . '>';
		} else {
			$button = '<' . $button_type . ' class="' . $class_attr . '"' . $link_attr . $target_attr . $attributes_attr . '>' .
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
