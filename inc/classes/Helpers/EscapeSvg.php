<?php
/**
 * Escape SVG Helper
 *
 * Provides secure SVG content sanitization to prevent XSS attacks
 * while preserving valid SVG markup.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Helpers
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Helpers;

class EscapeSvg
{
	/**
	 * Safely escape SVG content for output.
	 * 
	 * @param string $svg_content The SVG content to escape.
	 * @return string Escaped SVG content safe for output.
	 */
	public static function render($svg_content)
	{
		// Return empty string if no content
		if (empty($svg_content)) {
			return '';
		}

		// Define allowed SVG elements and attributes
		$allowed_svg = array(
			'svg' => array(
				'class' => true,
				'aria-hidden' => true,
				'aria-labelledby' => true,
				'role' => true,
				'xmlns' => true,
				'width' => true,
				'height' => true,
				'viewbox' => true,
				'preserveaspectratio' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
			),
			'g' => array(
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'transform' => true,
			),
			'title' => array('title' => true),
			'desc' => array(),
			'path' => array(
				'd' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'stroke-linecap' => true,
				'stroke-linejoin' => true,
				'opacity' => true,
				'transform' => true,
			),
			'circle' => array(
				'cx' => true,
				'cy' => true,
				'r' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'opacity' => true,
				'transform' => true,
			),
			'rect' => array(
				'x' => true,
				'y' => true,
				'width' => true,
				'height' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'rx' => true,
				'ry' => true,
				'opacity' => true,
				'transform' => true,
			),
			'polygon' => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'opacity' => true,
				'transform' => true,
			),
			'polyline' => array(
				'points' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'opacity' => true,
				'transform' => true,
			),
			'line' => array(
				'x1' => true,
				'y1' => true,
				'x2' => true,
				'y2' => true,
				'stroke' => true,
				'stroke-width' => true,
				'opacity' => true,
				'transform' => true,
			),
			'ellipse' => array(
				'cx' => true,
				'cy' => true,
				'rx' => true,
				'ry' => true,
				'fill' => true,
				'stroke' => true,
				'stroke-width' => true,
				'opacity' => true,
				'transform' => true,
			),
			'defs' => array(),
			'clippath' => array('id' => true),
			'mask' => array('id' => true),
			'pattern' => array(
				'id' => true,
				'x' => true,
				'y' => true,
				'width' => true,
				'height' => true,
				'patternunits' => true,
			),
			'lineargradient' => array(
				'id' => true,
				'x1' => true,
				'y1' => true,
				'x2' => true,
				'y2' => true,
				'gradientunits' => true,
			),
			'radialgradient' => array(
				'id' => true,
				'cx' => true,
				'cy' => true,
				'r' => true,
				'fx' => true,
				'fy' => true,
				'gradientunits' => true,
			),
			'stop' => array(
				'offset' => true,
				'stop-color' => true,
				'stop-opacity' => true,
			),
		);

		// Use wp_kses with our allowed SVG tags
		return self::restore_attribute_case(wp_kses($svg_content, $allowed_svg));
	}

	/**
	 * Restore camelCase names that wp_kses() lowercases.
	 *
	 * wp_kses() lowercases every element and attribute name, which is correct
	 * for HTML but wrong for SVG: SVG is case-sensitive, so `viewBox` becomes
	 * `viewbox` and browsers ignore it. Losing the viewport leaves the graphic
	 * with no intrinsic size, so it renders blank anywhere it is drawn as a
	 * CSS mask or background image.
	 *
	 * Only names already on the allowlist above are restored — this puts back
	 * the correct casing, it does not widen what is permitted.
	 *
	 * @param string $svg Sanitized SVG markup.
	 * @return string SVG markup with SVG-correct casing.
	 */
	protected static function restore_attribute_case($svg)
	{
		if (!is_string($svg) || $svg === '') {
			return '';
		}

		$attributes = [
			'viewbox'             => 'viewBox',
			'preserveaspectratio' => 'preserveAspectRatio',
			'gradientunits'       => 'gradientUnits',
			'gradienttransform'   => 'gradientTransform',
			'patternunits'        => 'patternUnits',
			'patterntransform'    => 'patternTransform',
			'clippathunits'       => 'clipPathUnits',
			'maskunits'           => 'maskUnits',
			'maskcontentunits'    => 'maskContentUnits',
			'stopcolor'           => 'stop-color',
			'stopopacity'         => 'stop-opacity',
		];

		foreach ($attributes as $lower => $proper) {
			$result = preg_replace('/\b' . $lower . '=/i', $proper . '=', $svg);
			$svg    = is_string($result) ? $result : $svg;
		}

		$elements = [
			'lineargradient' => 'linearGradient',
			'radialgradient' => 'radialGradient',
			'clippath'       => 'clipPath',
			'foreignobject'  => 'foreignObject',
		];

		foreach ($elements as $lower => $proper) {
			// foreignObject is not on the allowlist, so it never survives to
			// here; it is listed only so the mapping stays complete if the
			// allowlist ever changes.
			$result = preg_replace('/<(\/?)' . $lower . '\b/i', '<$1' . $proper, $svg);
			$svg    = is_string($result) ? $result : $svg;
		}

		return $svg;
	}
}