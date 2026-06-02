<?php

declare( strict_types=1 );

/**
 * Preload critical theme fonts declared in theme.json / style variations.
 *
 * Style variations in this stack use font-display: optional. Without early
 * preload hints the browser may skip custom fonts entirely on first paint.
 * This is theme typography — not a page-cache concern — so it lives in wp-utility.
 *
 * @package BuiltNorth\WPUtility\Setup
 */

namespace BuiltNorth\WPUtility\Setup;

defined( 'ABSPATH' ) || defined( 'WP_CLI' ) || exit;

final class FontPreloader {

	public static function register(): void {
		add_action( 'wp_head', [ self::class, 'output_preloads' ], 2 );
	}

	public static function output_preloads(): void {
		if ( is_admin() ) {
			return;
		}

		$enabled = (bool) apply_filters( 'wp_utility_font_preload_enabled', true );
		/** @deprecated 2.4 Use wp_utility_font_preload_enabled. */
		$enabled = (bool) apply_filters( 'polaris_performance_font_preload_enabled', $enabled );

		if ( ! $enabled ) {
			return;
		}

		$urls = self::resolve_critical_font_urls();

		$urls = (array) apply_filters( 'wp_utility_font_preload_urls', $urls );
		/** @deprecated 2.4 Use wp_utility_font_preload_urls. */
		$urls = (array) apply_filters( 'polaris_performance_font_preload_urls', $urls );

		foreach ( array_unique( array_filter( $urls ) ) as $url ) {
			echo '<link rel="preload" href="' . esc_url( $url ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		}
	}

	/**
	 * @return string[]
	 */
	private static function resolve_critical_font_urls(): array {
		$settings = wp_get_global_settings();

		$family_map = self::build_family_map(
			$settings['typography']['fontFamilies']['theme'] ?? []
		);

		if ( empty( $family_map ) ) {
			return [];
		}

		$urls = [];
		$seen = [];

		foreach ( array_keys( $family_map ) as $slug ) {
			foreach ( [ '400', '600' ] as $weight ) {
				$url = self::find_woff2_url( $family_map, $slug, $weight );
				if ( $url && ! isset( $seen[ $url ] ) ) {
					$seen[ $url ] = true;
					$urls[]       = $url;
				}
			}
		}

		return $urls;
	}

	/**
	 * @param array<int, array> $families
	 * @return array<string, array[]>
	 */
	private static function build_family_map( array $families ): array {
		$map = [];
		foreach ( $families as $family ) {
			$slug = $family['slug'] ?? '';
			if ( $slug !== '' ) {
				$map[ $slug ] = $family['fontFace'] ?? [];
			}
		}
		return $map;
	}

	/**
	 * @param array<string, array[]> $family_map
	 */
	private static function find_woff2_url( array $family_map, string $slug, string $weight ): ?string {
		$faces = $family_map[ $slug ] ?? [];

		foreach ( $faces as $face ) {
			if ( (string) ( $face['fontWeight'] ?? '' ) === $weight && self::is_normal_style( $face ) ) {
				$url = self::resolve_face_url( $face['src'] ?? [] );
				if ( $url ) {
					return $url;
				}
			}
		}

		$w = (int) $weight;
		foreach ( $faces as $face ) {
			$fw = trim( (string) ( $face['fontWeight'] ?? '' ) );
			if ( strpos( $fw, ' ' ) !== false && self::is_normal_style( $face ) ) {
				[ $min, $max ] = array_map( 'intval', explode( ' ', $fw, 2 ) );
				if ( $w >= $min && $w <= $max ) {
					$url = self::resolve_face_url( $face['src'] ?? [] );
					if ( $url ) {
						return $url;
					}
				}
			}
		}

		foreach ( $faces as $face ) {
			if ( self::is_normal_style( $face ) ) {
				$url = self::resolve_face_url( $face['src'] ?? [] );
				if ( $url ) {
					return $url;
				}
			}
		}

		return null;
	}

	/**
	 * @param string|string[] $src
	 */
	private static function resolve_face_url( $src ): ?string {
		$raw = is_array( $src ) ? ( $src[0] ?? '' ) : (string) $src;
		$raw = trim( $raw );

		if ( $raw === '' ) {
			return null;
		}

		if ( str_starts_with( $raw, 'file:./' ) ) {
			$relative = substr( $raw, 7 );

			foreach ( [ get_stylesheet_directory(), get_template_directory() ] as $base_dir ) {
				if ( file_exists( $base_dir . '/' . $relative ) ) {
					$base_url = ( get_stylesheet_directory() === $base_dir )
						? get_stylesheet_directory_uri()
						: get_template_directory_uri();
					return $base_url . '/' . $relative;
				}
			}

			return null;
		}

		if ( filter_var( $raw, FILTER_VALIDATE_URL ) ) {
			return $raw;
		}

		return null;
	}

	private static function is_normal_style( array $face ): bool {
		$style = strtolower( trim( $face['fontStyle'] ?? 'normal' ) );
		return $style === 'normal' || $style === '';
	}
}
