<?php

/**
 * Component facade.
 *
 * @package BuiltNorth\WPUtility
 * @since 3.0.0
 */

namespace BuiltNorth\WPUtility\Facade;

use BuiltNorth\WPUtility\Components\AccessibleCard;
use BuiltNorth\WPUtility\Components\Breadcrumbs;
use BuiltNorth\WPUtility\Components\Button;
use BuiltNorth\WPUtility\Components\Image;
use BuiltNorth\WPUtility\Components\Pagination;

/**
 * Don't load directly.
 */
defined('ABSPATH') || defined('WP_CLI') || exit;

/**
 * Single import for every rendering component in this package.
 *
 * Lives under `Facade\` so the package root stays free of loose entry-point
 * files while a consumer still reaches the whole component surface through one
 * `use` statement. The previous layout carried this class twice — once at the
 * namespace root and once under `Components\` — with the root copy marked
 * deprecated despite being the one every consumer imported.
 *
 * Methods here delegate and do not implement. A component needing its own
 * logic belongs in `Components\`, and anything exposed here must forward to it.
 */
class Component
{
	/**
	 * Render an accessible card component.
	 *
	 * @param mixed ...$args Arguments forwarded to the component.
	 * @return string The rendered component.
	 */
	public static function accessible_card(...$args)
	{
		return AccessibleCard::render(...$args);
	}

	/**
	 * Render breadcrumbs.
	 *
	 * @param mixed ...$args Arguments forwarded to the component.
	 * @return string The rendered component.
	 */
	public static function breadcrumbs(...$args)
	{
		return Breadcrumbs::render(...$args);
	}

	/**
	 * Render a button.
	 *
	 * @param mixed ...$args Arguments forwarded to the component.
	 * @return string The rendered component.
	 */
	public static function button(...$args)
	{
		return Button::render(...$args);
	}

	/**
	 * Resolve the screen-reader text for a button, or null when none is needed.
	 *
	 * @param array<string, mixed> $args See Button::resolve_screen_reader_text().
	 */
	public static function resolve_screen_reader_text(array $args): ?string
	{
		return Button::resolve_screen_reader_text($args);
	}

	/**
	 * Render an image.
	 *
	 * @param mixed ...$args Arguments forwarded to the component.
	 * @return string The rendered component.
	 */
	public static function image(...$args)
	{
		return Image::render(...$args);
	}

	/**
	 * Build a responsive sizes attribute string.
	 *
	 * @param int      $desktop_vw        Percentage of viewport width at desktop (e.g. 50 for a half-width column).
	 * @param int      $mobile_breakpoint Breakpoint in px below which the image is 100vw. Default 782.
	 * @param int|null $content_width     Optional. Content width in px to cap against. Defaults to the
	 *                                    theme's resolved `layout.contentSize`.
	 */
	public static function sizes(int $desktop_vw = 100, int $mobile_breakpoint = 782, ?int $content_width = null): string
	{
		return Image::sizes($desktop_vw, $mobile_breakpoint, $content_width);
	}

	/**
	 * Render pagination.
	 *
	 * @param mixed ...$args Arguments forwarded to the component.
	 * @return string The rendered component.
	 */
	public static function pagination(...$args)
	{
		return Pagination::render(...$args);
	}
}
