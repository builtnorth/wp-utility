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
			'use' => array(
				'href' => true,
				'xlink:href' => true,
				'x' => true,
				'y' => true,
				'width' => true,
				'height' => true,
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
		return wp_kses($svg_content, $allowed_svg);
	}
}