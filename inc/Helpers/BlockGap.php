<?php

namespace BuiltNorth\Utility\Helpers;

class BlockGap
{
	/**
	 * Extract blockGap from block attributes and convert to CSS variable.
	 * 
	 * @param array $attributes Block attributes array.
	 * @return string CSS gap style or empty string.
	 */
	public static function render($attributes)
	{
		$block_gap_style = '';

		// Use the exact same logic that works in the render file
		if (!empty($attributes['style']['spacing']['blockGap'])) {
			$block_gap = $attributes['style']['spacing']['blockGap'];
			// Convert "var:preset|spacing|50" to "var(--wp--preset--spacing--50)"
			if (strpos($block_gap, 'var:preset|spacing|') === 0) {
				$spacing_value = str_replace('var:preset|spacing|', '', $block_gap);
				$block_gap_style = 'gap: var(--wp--preset--spacing--' . $spacing_value . ');';
			}
		} else {
			// Apply default blockGap from theme.json
			$block_gap_style = 'gap: var(--wp--preset--spacing--30);';
		}

		return $block_gap_style;
	}
}
