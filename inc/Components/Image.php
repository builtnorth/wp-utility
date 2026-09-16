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
	 * @param string      $wrap_class         Optional. The class to add to the figure.
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
		// Check the image ID is not empty
		if (empty($id)) {
			return '';
		}

		// Capture before $lazy is reassigned to a string below.
		$is_lazy = (bool) $lazy;

		// Image src and srcset
		$src = wp_get_attachment_image_url($id, $size) ?: '';
		$srcset = wp_get_attachment_image_srcset($id, $size) ?: '';

		// Image alt and caption
		$image_alt = get_post_meta($id, '_wp_attachment_image_alt', true) ?: '';
		$image_caption = wp_get_attachment_caption($id) ?: '';

		// Image attributes
		$attributes = wp_get_attachment_image_src($id, $size);

		// Set width & height - handle SVG files which may not have dimensions
		$width = (isset($attributes[1]) && $attributes[1]) ? (string) $attributes[1] : '';
		$height = (isset($attributes[2]) && $attributes[2]) ? (string) $attributes[2] : '';
		
		// For SVG files, check if we can get dimensions from the file itself
		if (empty($width) || empty($height)) {
			$mime_type = get_post_mime_type($id);
			if ($mime_type === 'image/svg+xml') {
				// SVGs don't have inherent dimensions in WordPress metadata
				// Set reasonable defaults or leave empty for responsive SVGs
				$width = '';
				$height = '';
			}
		}

		// Set alt text - use parameter alt if provided, otherwise custom_alt, otherwise image_alt
		$final_alt = '';
		if (!empty($alt)) {
			$final_alt = (string) $alt;
		} elseif (!empty($custom_alt)) {
			$final_alt = (string) $custom_alt;
		} elseif (!empty($image_alt)) {
			$final_alt = (string) $image_alt;
		}
		
		// add class
		$class = $class ? esc_attr((string) $class) : 'image';

		// add additional classes
		$additional_classes = $additional_classes ? (string) $additional_classes : '';

		// Add caption
		$caption_str = (string) $caption;
		$caption_html = ($show_caption === true && !empty($caption_str)) ? '<figcaption class="' . esc_attr($class) . '__caption">' . esc_html($caption_str) . '</figcaption>' : '';

		// Set lazy loading
		$lazy = $lazy ? 'loading=lazy decoding=async' : 'loading=eager decoding=sync fetchpriority="high"';

		// Add style to img attributes if provided
		if ($style) {
			// Handle both string and array styles
			if (is_array($style)) {
				$style_string = implode('; ', array_filter($style));
			} else {
				$style_string = $style;
			}
			$style_attr = " style='" . esc_attr($style_string) . "'";
		} else {
			$style_attr = '';
		}

		// Build sizes attribute: use explicit $sizes when provided, otherwise derive from $max_width.
		$sizes_attr = !empty($sizes)
			? esc_attr((string) $sizes)
			: '(max-width: ' . esc_attr((string) $max_width) . ') 100vw, ' . esc_attr((string) $max_width);

		// Prepend `auto` for lazy images — supporting browsers measure the actual rendered width
		// and use that instead of the static hint; non-supporting browsers use the fallback value.
		if ($is_lazy) {
			$sizes_attr = 'auto, ' . $sizes_attr;
		}

		// Build the img tag - ensure all values are strings for escaping functions
		$img_tag = "<img
			$lazy 
			class='" . esc_attr($class . "__img " . $additional_classes) . "'
			alt='" . esc_attr((string) $final_alt) . "'
			src='" . esc_url((string) $src) . "'
			srcset='" . esc_attr((string) $srcset) . "'
			sizes='" . $sizes_attr . "'
			width='" . esc_attr((string) $width) . "'
			height='" . esc_attr((string) $height) . "'
			$style_attr
		/>";

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
		$img_tag = apply_filters( 'wp_content_img_tag', $img_tag, 'wp_utility_image', (int) $id );

		// Include figure
		if ($include_figure) {
			echo "<figure class='" . esc_attr($class) . "__figure'>
				$img_tag
				$caption_html 
			</figure>";
		} else {
			echo $img_tag;
		}
	}
}
