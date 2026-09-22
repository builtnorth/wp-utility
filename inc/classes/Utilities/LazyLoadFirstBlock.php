<?php
/**
 * Lazy Load First Block Utility
 *
 * Controls lazy loading behavior for blocks based on their parent
 * context and position within the page.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Utilities;

class LazyLoadFirstBlock
{
	/**
	 * Determine if lazy loading should be applied to a block
	 * 
	 * @param object $block The block object to check
	 * @param array $non_lazy_parents Optional array of parent block names that should disable lazy loading
	 * @param bool $default_lazy Optional default lazy loading state (default: true)
	 * @return bool Whether lazy loading should be applied
	 */
	public static function render($block, array $non_lazy_parents = [], bool $default_lazy = true)
	{
		$lazy = $default_lazy;

		/**
		 * Filter the list of parent blocks that should disable lazy loading.
		 * 
		 * @param array $non_lazy_parents Array of parent block names.
		 * @param object $block The current block object.
		 */
		$non_lazy_parents = apply_filters('wp_utility_lazy_load_non_lazy_parents', $non_lazy_parents, $block);

		if (
			isset($block->block_type->parent) &&
			is_array($block->block_type->parent) &&
			!empty($block->block_type->parent)
		) {
			$parent_block = $block->block_type->parent[0];

			if (!empty($non_lazy_parents) && in_array($parent_block, $non_lazy_parents)) {
				$lazy = false;
			}
		}

		return $lazy;
	}

	/**
	 * Check if lazy loading should be applied with callback logic
	 * 
	 * @param object $block The block object to check
	 * @param callable|null $should_lazy_load Optional callback to determine lazy loading
	 * @param bool $default_lazy Optional default lazy loading state (default: true)
	 * @return bool Whether lazy loading should be applied
	 */
	public static function renderWithCallback($block, ?callable $should_lazy_load = null, bool $default_lazy = true)
	{
		$lazy = $default_lazy;

		if (
			isset($block->block_type->parent) &&
			is_array($block->block_type->parent) &&
			!empty($block->block_type->parent) &&
			is_callable($should_lazy_load)
		) {
			$parent_block = $block->block_type->parent[0];
			$lazy = $should_lazy_load($parent_block, $block);
		}

		return $lazy;
	}
}
