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
		$post_types = $props['postType'] ?? ['post'];
		$column_count = $attributes['columnCount'] ?? 3;
		$display_type = $attributes['displayAs'] ?? 'grid';

		// Ensure post_types is always an array
		$post_types = (array) $post_types;

		return compact('attributes', 'posts', 'card_component', 'post_types', 'column_count', 'display_type');
	}

	protected static function getWrapperAttributes($post_types, $display_type, $column_count)
	{
		$post_type_classes = implode(' ', array_map(function ($pt) use ($display_type) {
			return "{$pt}-query {$pt}-query--{$display_type}";
		}, $post_types));

		return get_block_wrapper_attributes([
			'class' => "{$post_type_classes} {$display_type} {$display_type}-has-{$column_count}"
		]);
	}

	protected static function renderPosts($posts, $card_component, $post_types)
	{
		ob_start();
		if (!empty($posts)) {
			foreach ($posts as $post) {
				$post_type = get_post_type($post);
				if (in_array($post_type, $post_types)) {
					echo call_user_func($card_component, $post, $post_type);
				}
			}
		}
		return ob_get_clean();
	}

	abstract public static function render($props);
}
