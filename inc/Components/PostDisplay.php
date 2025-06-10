<?php

/**
 * PostDisplay handles the container/wrapper for displaying multiple posts
 * Works in conjunction with PostCard for individual post rendering
 */

namespace BuiltNorth\Utility\Components;

class PostDisplay
{
	/**
	 * Render the post display container with posts
	 *
	 * @param array $props Display properties
	 * @return string Rendered HTML
	 */
	public static function render($props)
	{
		$common_props = self::getCommonProps($props);
		extract($common_props);

		ob_start();
?>
		<div <?php echo wp_kses_data(self::getWrapperAttributes($common_props)); ?>>
			<?php echo self::renderPosts($common_props); ?>
		</div>
<?php
		return ob_get_clean();
	}

	/**
	 * Get and normalize common properties
	 */
	protected static function getCommonProps($props)
	{
		return [
			'attributes' => $props['attributes'] ?? [],
			'query' => $props['query'] ?? null,
			'post_type' => $props['postType'] ?? 'post',
			'column_count' => $props['columnCount'] ?? 3,
			'display_type' => $props['displayAs'] ?? 'grid',
			'theme' => $props['theme'] ?? 'polaris-blocks'
		];
	}

	/**
	 * Get wrapper attributes including classes and data attributes
	 */
	protected static function getWrapperAttributes($props)
	{
		$post_type = $props['post_type'];
		$display_type = $props['display_type'];
		$column_count = $props['column_count'];

		$classes = [
			"{$post_type}-query",
			"{$post_type}-query--{$display_type}",
			$display_type,
			"{$display_type}-has-{$column_count}"
		];

		$attributes = [
			'class' => implode(' ', array_filter($classes)),
			'data-post-type' => $post_type,
			'data-display-type' => $display_type,
			'data-column-count' => $column_count
		];

		return get_block_wrapper_attributes($attributes);
	}

	/**
	 * Render all posts using PostCard component
	 */
	protected static function renderPosts($props)
	{
		$query = $props['query'];
		if (!$query || !$query->have_posts()) {
			return self::renderEmpty($props['post_type']);
		}

		ob_start();

		while ($query->have_posts()) {
			$query->the_post();

			// Setup card props
			$card_props = [
				'post_type' => $props['post_type'],
				'display_as' => $props['display_type'],
				'theme' => $props['theme']
			];

			// Render individual card
			echo PostCard::render($card_props);
		}

		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Render empty state message
	 */
	protected static function renderEmpty($post_type)
	{
		$post_type_obj = get_post_type_object($post_type);
		$type_label = $post_type_obj ? strtolower($post_type_obj->labels->name) : 'posts';

		return sprintf(
			'<div class="notice notice-info">No %s found.</div>',
			esc_html($type_label)
		);
	}
}
