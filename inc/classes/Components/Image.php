<?php

/**
 * ------------------------------------------------------------------
 * Class: Image
 * ------------------------------------------------------------------
 *
 * This class is responsible for rendering an image.
 *
 * @package BuiltNorth/Utility
 * @since BuiltNorth/Utility 1.0.0
 **/

namespace BuiltNorth\WPUtility\Components;

class Image
{
	/**
	 * Fallback content width in px, used when the active theme does not expose
	 * a usable `layout.contentSize`.
	 */
	public const DEFAULT_CONTENT_WIDTH = 1280;

	/**
	 * Per-process memo of the resolved content width.
	 *
	 * @var int|null
	 */
	private static $content_width = null;

	/**
	 * Build a responsive sizes attribute string.
	 *
	 * Returns a sizes value suitable for the `sizes` param of Image::render().
	 * When passed to a lazy-loaded image, `auto` is automatically prepended so
	 * supporting browsers can measure the rendered width instead.
	 *
	 * The desktop term is capped at the theme's content width. A bare `50vw`
	 * keeps growing with the viewport, so on a 2560px display it claims 1280px
	 * for a column that a 1280px-capped layout actually renders at ~640px —
	 * the hint overshoots most on the widest screens, which is exactly where
	 * the wasted bytes are largest.
	 *
	 * @param int      $desktop_vw        Percentage of viewport width at desktop (e.g. 50 for a half-width column).
	 * @param int      $mobile_breakpoint Breakpoint in px below which the image is 100vw. Default 782 (WP/Gutenberg stack point).
	 * @param int|null $content_width     Optional. Content width in px to cap against. Defaults to the
	 *                                    theme's resolved `layout.contentSize`.
	 * @return string  e.g. "(max-width: 782px) 100vw, min(50vw, 640px)"
	 */
	public static function sizes(int $desktop_vw = 100, int $mobile_breakpoint = 782, ?int $content_width = null): string
	{
		if ($desktop_vw >= 100) {
			return '100vw';
		}

		// Guard against a nonsensical share producing a negative/zero cap.
		if ($desktop_vw < 1) {
			$desktop_vw = 1;
		}

		$cap = $content_width ?? self::resolve_content_width();
		$desktop_px = (int) round($cap * $desktop_vw / 100);

		return "(max-width: {$mobile_breakpoint}px) 100vw, min({$desktop_vw}vw, {$desktop_px}px)";
	}

	/**
	 * Resolve the theme's content width in px.
	 *
	 * Read at runtime rather than hardcoded: a user's global-styles override
	 * takes precedence over the theme's own theme.json, so the file value can
	 * be stale (observed: theme.json 1240px vs. 1280px resolved).
	 *
	 * Falls back to DEFAULT_CONTENT_WIDTH when the value is unavailable or is
	 * not expressed in px. `contentSize` is free-form CSS — `1280px` parses,
	 * but `80rem`, `90%` or `clamp(...)` cannot be converted here, and a bad
	 * conversion would emit a wrong cap rather than no cap. wp_get_global_settings()
	 * also resolves the merged theme.json tree, which is not reliable before
	 * after_setup_theme, and this package does not require a block theme.
	 */
	private static function resolve_content_width(): int
	{
		if (self::$content_width !== null) {
			return self::$content_width;
		}

		$width = self::DEFAULT_CONTENT_WIDTH;

		if (function_exists('wp_get_global_settings')) {
			$layout = wp_get_global_settings(['layout']);
			$content_size = is_array($layout) ? ($layout['contentSize'] ?? '') : '';

			// Only px is convertible; anything else keeps the fallback.
			if (is_string($content_size) && preg_match('/^\s*(\d+(?:\.\d+)?)\s*px\s*$/i', $content_size, $matches)) {
				$parsed = (int) round((float) $matches[1]);
				if ($parsed > 0) {
					$width = $parsed;
				}
			}
		}

		self::$content_width = $width;

		return $width;
	}

	/**
	 * Bust the per-process content-width memo.
	 *
	 * Tests that switch themes or global settings must call this in setUp() —
	 * the memo persists for the life of the PHP process and would otherwise
	 * serve a width resolved under an earlier fixture.
	 */
	public static function reset_content_width(): void
	{
		self::$content_width = null;
	}

	/**
	 * Render an image.
	 *
	 * @param int         $id                 The image ID.
	 * @param string      $class              Optional. The class to add to the image.
	 * @param string      $additional_classes Optional. Extra classes to add to the image.
	 * @param string      $custom_alt         Optional. The custom alt text.
	 * @param bool        $show_caption       Optional. Whether to show the caption.
	 * @param bool        $lazy               Optional. Whether to use lazy loading.
	 * @param string      $wrap_class         Optional. Extra class added to the figure, alongside `{$class}__figure`.
	 * @param bool        $include_figure     Optional. Whether to include the figure.
	 * @param string      $size               Optional. The WordPress image size.
	 * @param string      $max_width          Optional. Max-width used to build the default sizes fallback.
	 * @param string      $style              Optional. Inline style for the img element.
	 * @param string      $caption            Optional. Caption text.
	 * @param string      $alt                Optional. Alt text override.
	 * @param string|null $sizes              Optional. Custom sizes value (use Image::sizes() to generate).
	 *                                        When null, derived from $max_width. For lazy images, `auto` is
	 *                                        automatically prepended as a progressive enhancement.
	 */
	public static function render(
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
		$sizes = null,
	) {
		if (empty($id)) {
			return '';
		}

		// Captured before self::lazy_loading_attr() turns $lazy into an attribute string.
		$is_lazy = (bool) $lazy;

		$class = $class ? esc_attr((string) $class) : 'image';
		$additional_classes = $additional_classes ? (string) $additional_classes : '';

		[$width, $height] = self::resolve_dimensions($id, $size);
		$final_alt = self::resolve_alt($id, $alt, $custom_alt);
		$sizes_attr = self::sizes_attr($sizes, $max_width, $is_lazy);
		$style_attr = self::style_attr($style);
		$lazy_attr = self::lazy_loading_attr($lazy);

		$img_tag = self::build_img_tag(
			$lazy_attr,
			$class,
			$additional_classes,
			$final_alt,
			wp_get_attachment_image_url($id, $size) ?: '',
			wp_get_attachment_image_srcset($id, $size) ?: '',
			$sizes_attr,
			$width,
			$height,
			$style_attr
		);

		/**
		 * Filters the built <img> tag before output.
		 *
		 * Same filter and signature WordPress core applies to content images
		 * (wp_filter_content_tags()) — reusing it here, rather than inventing a
		 * component-specific hook, lets any existing consumer of the standard
		 * `wp_content_img_tag` filter (image format converters, lazy-load
		 * plugins, etc.) apply to every block/template built on Image::render(),
		 * since this component constructs its <img> manually and would
		 * otherwise never pass through core's own content-image filtering.
		 *
		 * @param string $img_tag Full img tag with attributes.
		 * @param string $context Context identifier.
		 * @param int    $id      The image attachment ID.
		 */
		$img_tag = apply_filters('wp_content_img_tag', $img_tag, 'wp_utility_image', (int) $id);

		if (! $include_figure) {
			echo $img_tag;
			return;
		}

		$caption_html = self::caption_html($class, $show_caption, $caption);
		echo self::build_figure_tag($class, $wrap_class, $img_tag, $caption_html);
	}

	/**
	 * Resolve width/height, handling SVGs which have no inherent dimensions in
	 * WordPress metadata.
	 *
	 * @return array{0: string, 1: string} [$width, $height]
	 */
	private static function resolve_dimensions($id, $size): array
	{
		$attributes = wp_get_attachment_image_src($id, $size);

		$width = (isset($attributes[1]) && $attributes[1]) ? (string) $attributes[1] : '';
		$height = (isset($attributes[2]) && $attributes[2]) ? (string) $attributes[2] : '';

		if (($width === '' || $height === '') && get_post_mime_type($id) === 'image/svg+xml') {
			$width = '';
			$height = '';
		}

		return [$width, $height];
	}

	/**
	 * Resolve alt text: explicit $alt param wins, then $custom_alt, then the
	 * attachment's own stored alt text.
	 */
	private static function resolve_alt($id, $alt, $custom_alt): string
	{
		if (! empty($alt)) {
			return (string) $alt;
		}

		if (! empty($custom_alt)) {
			return (string) $custom_alt;
		}

		$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: '';

		return (string) $image_alt;
	}

	/**
	 * Build the (already-escaped) sizes attribute value.
	 *
	 * Uses the explicit $sizes value when given, otherwise derives one from
	 * $max_width. `auto` is prepended for lazy images so supporting browsers
	 * measure the rendered width instead of using the static hint.
	 */
	private static function sizes_attr($sizes, $max_width, bool $is_lazy): string
	{
		$sizes_attr = !empty($sizes)
			? esc_attr((string) $sizes)
			: '(max-width: ' . esc_attr((string) $max_width) . ') 100vw, ' . esc_attr((string) $max_width);

		return $is_lazy ? 'auto, ' . $sizes_attr : $sizes_attr;
	}

	/**
	 * Build the ` style='...'` attribute fragment (including the leading
	 * space), or an empty string when no style is given.
	 *
	 * @param string|array<int, string>|null $style
	 */
	private static function style_attr($style): string
	{
		if (! $style) {
			return '';
		}

		$style_string = is_array($style) ? implode('; ', array_filter($style)) : $style;

		return " style='" . esc_attr($style_string) . "'";
	}

	/**
	 * Build the loading/decoding attribute fragment for the <img> tag.
	 */
	private static function lazy_loading_attr($lazy): string
	{
		return $lazy ? 'loading=lazy decoding=async' : 'loading=eager decoding=sync fetchpriority="high"';
	}

	/**
	 * Build the <figcaption>, or an empty string when no caption should show.
	 */
	private static function caption_html(string $class, $show_caption, $caption): string
	{
		$caption_str = (string) $caption;

		if ($show_caption !== true || $caption_str === '') {
			return '';
		}

		return '<figcaption class="' . esc_attr($class) . '__caption">' . esc_html($caption_str) . '</figcaption>';
	}

	/**
	 * Assemble the <img> tag. All values must already be attribute-safe
	 * (escaped) except $sizes_attr, which self::sizes_attr() already escapes
	 * internally before the unescaped `auto, ` prefix is added.
	 */
	private static function build_img_tag(
		string $lazy_attr,
		string $class,
		string $additional_classes,
		string $final_alt,
		string $src,
		string $srcset,
		string $sizes_attr,
		string $width,
		string $height,
		string $style_attr
	): string {
		return "<img
			" . $lazy_attr . ' ' . "
			class='" . esc_attr($class . "__img " . $additional_classes) . "'
			alt='" . esc_attr($final_alt) . "'
			src='" . esc_url($src) . "'
			srcset='" . esc_attr($srcset) . "'
			sizes='" . $sizes_attr . "'
			width='" . esc_attr($width) . "'
			height='" . esc_attr($height) . "'
			$style_attr
		/>";
	}

	/**
	 * Assemble the <figure> wrapper. $wrap_class is appended alongside the
	 * standard `{$class}__figure` class rather than replacing it.
	 */
	private static function build_figure_tag(string $class, $wrap_class, string $img_tag, string $caption_html): string
	{
		$figure_class = trim($class . '__figure ' . (string) $wrap_class);

		return "<figure class='" . esc_attr($figure_class) . "'>
				" . $img_tag . "
				" . $caption_html . ' ' . "
			</figure>";
	}
}
