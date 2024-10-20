<?php

/**
 * PostDisplay is an abstract class that provides common functionality for displaying posts.
 * It is used as a base class for PostGrid, PostList, and PostSlider.
 */

namespace BuiltNorth\Utility\Components\PostDisplay;

abstract class PostDisplay
{
	protected static function getCommonProps($props)
	{
		$attributes = $props['attributes'] ?? [];
		$posts = $props['posts'] ?? [];
		$card_component = $props['CardComponent'] ?? null;
		$post_type = $props['postType'] ?? 'post';
		$column_count = $attributes['columnCount'] ?? 3;
		$display_type = $attributes['displayAs'] ?? 'grid';


		return compact('attributes', 'posts', 'card_component', 'post_type', 'column_count', 'display_type');
	}

	protected static function getWrapperAttributes($post_type, $display_type, $column_count)
	{
		return get_block_wrapper_attributes(['class' => "{$post_type}-query {$post_type}-query--{$display_type} {$display_type} {$display_type}-has-{$column_count}"]);
	}

	protected static function renderPosts($posts, $card_component, $post_type)
	{
		ob_start();
		if (!empty($posts)) {
			foreach ($posts as $post) {
				echo call_user_func($card_component, $post, $post_type);
			}
		}
		return ob_get_clean();
	}

	abstract public static function render($props);
}
