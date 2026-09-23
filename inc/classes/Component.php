<?php

/**
 * Component facade.
 *
 * @package BuiltNorth\WPUtility
 * @since 3.0.0
 */

namespace BuiltNorth\WPUtility;

use BuiltNorth\WPUtility\Components\AccessibleCard;
use BuiltNorth\WPUtility\Components\Breadcrumbs;
use BuiltNorth\WPUtility\Components\Button;
use BuiltNorth\WPUtility\Components\Image;
use BuiltNorth\WPUtility\Components\Pagination;
use BuiltNorth\WPUtility\Components\PostTypeLandingUrl;
use WP_Post_Type;
use WP_Query;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

/**
 * Single import for every rendering component in this package.
 *
 * A consumer imports this one class and reaches the whole `Components\` area
 * through it. If something in that area is only reachable by importing the leaf
 * directly, that is a gap here, not a reason for the consumer to add an import.
 *
 * Forwarding methods restate the leaf's parameters rather than taking
 * `...$args`. A variadic hides the contract: it reports `mixed ...$args` to an
 * IDE and to static analysis, and it silently accepts named arguments the leaf
 * has never heard of. The README documented `Component::breadcrumbs(block: ...)`
 * for exactly that reason, which would have raised `Unknown named parameter`
 * had anyone run it.
 */
class Component
{
	/**
	 * Render an accessible card wrapper.
	 *
	 * @param string|null $link          URL the card links to.
	 * @param string|null $target        Anchor target attribute.
	 * @param string      $screen_reader Screen-reader text for the card link.
	 * @param string|null $class         Class applied to the card.
	 */
	public static function accessible_card(
		$link = null,
		$target = null,
		$screen_reader = 'Read more about ...',
		$class = null
	) {
		return AccessibleCard::render($link, $target, $screen_reader, $class);
	}

	/**
	 * Render breadcrumbs.
	 *
	 * @param bool|null   $show_on_front  Whether to render on the front page.
	 * @param string      $class          Class applied to the wrapper.
	 * @param string      $separator      Markup between crumbs.
	 * @param string      $home_title     Label for the home crumb.
	 * @param string|null $prefix         Text before the trail, e.g. 'You are here:'.
	 * @param string      $nav_attributes Block wrapper attributes for the `<nav>`.
	 */
	public static function breadcrumbs(
		$show_on_front = null,
		$class = 'breadcrumbs',
		$separator = '&raquo;',
		$home_title = 'Home',
		$prefix = null,
		$nav_attributes = ''
	) {
		return Breadcrumbs::render($show_on_front, $class, $separator, $home_title, $prefix, $nav_attributes);
	}

	/**
	 * The breadcrumb trail as data, for callers building their own markup.
	 *
	 * @return array<int, mixed>
	 */
	public static function get_breadcrumb_data()
	{
		return Breadcrumbs::get_breadcrumb_data();
	}

	/**
	 * Render a button or link.
	 *
	 * @param string      $button_type   Element to render: 'a' or 'button'.
	 * @param string|null $class         Base class.
	 * @param string|null $extra_class   Additional classes.
	 * @param string      $style         Style variant.
	 * @param string      $size          Size variant.
	 * @param string      $appearance    Appearance variant, e.g. 'fill'.
	 * @param string      $text          Button label.
	 * @param string|null $link          URL for an anchor.
	 * @param string|null $target        Anchor target attribute.
	 * @param string|null $screen_reader Screen-reader text.
	 * @param string|null $attributes    Extra HTML attributes.
	 * @param array|null  $icon          Icon definition.
	 * @param string      $icon_position 'left' or 'right'.
	 */
	public static function button(
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
		return Button::render(
			$button_type,
			$class,
			$extra_class,
			$style,
			$size,
			$appearance,
			$text,
			$link,
			$target,
			$screen_reader,
			$attributes,
			$icon,
			$icon_position
		);
	}

	/**
	 * Screen-reader text for a button, or null when none is needed.
	 *
	 * @param array<string, mixed> $args See Button::resolve_screen_reader_text().
	 */
	public static function resolve_screen_reader_text(array $args): ?string
	{
		return Button::resolve_screen_reader_text($args);
	}

	/**
	 * Generic link labels that should not be announced verbatim.
	 *
	 * @return array<int, string>
	 */
	public static function get_generic_link_labels(): array
	{
		return Button::get_generic_link_labels();
	}

	/**
	 * Whether a label is too generic to be useful to a screen reader.
	 */
	public static function is_generic_link_label(string $label): bool
	{
		return Button::is_generic_link_label($label);
	}

	/**
	 * Render an image.
	 *
	 * @param int|null    $id                 Attachment ID.
	 * @param string|null $class              Base class.
	 * @param string|null $additional_classes Additional classes.
	 * @param string|null $custom_alt         Alt text override.
	 * @param bool|null   $show_caption       Whether to render the caption.
	 * @param bool        $lazy               Whether to lazy-load.
	 * @param string      $wrap_class         Wrapper class variant.
	 * @param bool        $include_figure     Whether to wrap in `<figure>`.
	 * @param string      $size               Registered image size.
	 * @param string      $max_width          Max width for the sizes attribute.
	 * @param string|null $style              Inline style.
	 * @param string      $caption            Caption text.
	 * @param string      $alt                Alt text.
	 * @param string|null $sizes              Explicit sizes attribute.
	 */
	public static function image(
		$id = null,
		$class = null,
		$additional_classes = null,
		$custom_alt = null,
		$show_caption = null,
		$lazy = true,
		$wrap_class = 'standard',
		$include_figure = true,
		$size = 'full',
		$max_width = '1200px',
		$style = null,
		$caption = '',
		$alt = '',
		$sizes = null
	) {
		return Image::render(
			$id,
			$class,
			$additional_classes,
			$custom_alt,
			$show_caption,
			$lazy,
			$wrap_class,
			$include_figure,
			$size,
			$max_width,
			$style,
			$caption,
			$alt,
			$sizes
		);
	}

	/**
	 * Build a responsive sizes attribute string.
	 *
	 * @param int      $desktop_vw        Percentage of viewport width at desktop.
	 * @param int      $mobile_breakpoint Breakpoint in px below which the image is 100vw.
	 * @param int|null $content_width     Content width in px to cap against.
	 */
	public static function sizes(int $desktop_vw = 100, int $mobile_breakpoint = 782, ?int $content_width = null): string
	{
		return Image::sizes($desktop_vw, $mobile_breakpoint, $content_width);
	}

	/**
	 * Restore the content width Image::sizes() resolved against.
	 */
	public static function reset_content_width(): void
	{
		Image::reset_content_width();
	}

	/**
	 * Render pagination for a query.
	 *
	 * @param WP_Query|null $query Query to paginate. Defaults to the main query.
	 */
	public static function pagination($query = null)
	{
		return Pagination::render($query);
	}

	/**
	 * Landing-page URL for a post type archive.
	 */
	public static function post_type_landing_url(string $post_type): string
	{
		return PostTypeLandingUrl::resolve($post_type);
	}

	/**
	 * Rewrite slug for a post type.
	 */
	public static function get_rewrite_slug(WP_Post_Type $post_type): string
	{
		return PostTypeLandingUrl::get_rewrite_slug($post_type);
	}
}
