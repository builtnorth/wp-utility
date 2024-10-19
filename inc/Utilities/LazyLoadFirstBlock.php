<?php

namespace BuiltNorth\Utility\Utilities;

class LazyLoadFirstBlock
{
	/**
	 * Get parent block name
	 * @todo - modify to work for blocks that have a defined parent in block.json
	 */
	public static function render($block)
	{
		$lazy = true; // Default to true

		if (
			isset($block->block_type->parent) &&
			is_array($block->block_type->parent) &&
			!empty($block->block_type->parent)
		) {

			$parent_block = $block->block_type->parent[0];

			// echo $parent_block;

			$hero_area_blocks = [
				'built/hero-area-primary',
				'built/hero-area-secondary',
				'built/hero-area-section'
			];

			if (in_array($parent_block, $hero_area_blocks)) {
				$lazy = false;
			}
		}

		return $lazy;
	}
}
