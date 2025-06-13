<?php

namespace BuiltNorth\Utility\Components;

use BuiltNorth\Utility\Component;
use WP_Query;

class PostFeed
{
	public static function render($attributes, $post_type, $card_component = null)
	{
		$query = new WP_Query(self::build_query_args($attributes, $post_type));

		if (!$query->have_posts()) {
			return self::render_empty($post_type, $attributes);
		}

		$output = self::render_posts($query, $attributes, $post_type);

		// Add pagination if needed (only for auto mode)
		$selection_mode = $attributes['selectionMode'] ?? 'auto';
		if (($attributes['showPagination'] ?? false) && $selection_mode === 'auto' && $query->found_posts > ($attributes['postsPerPage'] ?? 9)) {
			$output .= Component::Pagination($query);
		}

		wp_reset_postdata();
		return $output;
	}

	private static function render_posts($query, $attributes, $post_type)
	{
		$display_type = $attributes['displayAs'] ?? 'grid';
		$column_count = $attributes['columnCount'] ?? 3;
		$selection_mode = $attributes['selectionMode'] ?? 'auto';

		$wrapper_classes = [
			"{$post_type}-query",
			"{$post_type}-query--{$display_type}",
			"{$post_type}-query--{$selection_mode}",
			$display_type,
			"{$display_type}-has-{$column_count}"
		];

		$wrapper_attrs = get_block_wrapper_attributes([
			'class' => implode(' ', $wrapper_classes),
			'data-post-type' => $post_type,
			'data-display-type' => $display_type,
			'data-column-count' => $column_count,
			'data-selection-mode' => $selection_mode
		]);

		ob_start();
?>
		<div <?php echo wp_kses_data($wrapper_attrs); ?>>
			<?php
			while ($query->have_posts()) {
				$query->the_post();
				echo PostCard::render($post_type, $display_type);
			}
			?>
		</div>
<?php
		return ob_get_clean();
	}

	private static function build_query_args($attributes, $post_type)
	{
		$selection_mode = $attributes['selectionMode'] ?? 'auto';
		$selected_posts = $attributes['selectedPosts'] ?? [];

		// Handle manual selection mode
		if ($selection_mode === 'manual' && !empty($selected_posts)) {
			$query_args = [
				'post_type' => $post_type,
				'post__in' => $selected_posts,
				'orderby' => 'post__in', // Maintain order of selected posts
				'posts_per_page' => count($selected_posts), // Show all selected posts
				'post_status' => 'publish',
			];
			return $query_args;
		}

		// Handle automatic selection mode (existing logic)
		//$paged = get_query_var('paged') ?: (get_query_var('page') ?: 1);
		$paged = false;

		$query_args = [
			'post_type' => $post_type,
			'posts_per_page' => $attributes['postsPerPage'] ?? 9,
			'orderby' => $attributes['orderPostsBy'] ?? 'date',
			'order' => $attributes['orderPostsDirection'] ?? 'DESC',
			'post_status' => 'publish',
			'paged' => $paged,
		];

		// Add taxonomy filter if specified (only in auto mode)
		if (!empty($attributes['selectedTaxonomy']) && !empty($attributes['selectedTerms'])) {
			$query_args['tax_query'] = [[
				'taxonomy' => $attributes['selectedTaxonomy'],
				'field' => 'term_id',
				'terms' => $attributes['selectedTerms'],
			]];
		}

		return $query_args;
	}

	private static function render_empty($post_type, $attributes = [])
	{
		$selection_mode = $attributes['selectionMode'] ?? 'auto';
		$selected_posts = $attributes['selectedPosts'] ?? [];

		// Different messages for different modes
		if ($selection_mode === 'manual') {
			if (empty($selected_posts)) {
				return '<div class="notice notice-warning">Please select posts to display using the Manual Selection panel.</div>';
			} else {
				return '<div class="notice notice-info">The selected posts are not available or published.</div>';
			}
		}

		// Default message for auto mode
		$post_type_obj = get_post_type_object($post_type);
		$type_label = $post_type_obj ? strtolower($post_type_obj->labels->name) : 'posts';

		return sprintf(
			'<div class="notice notice-info">No %s found.</div>',
			esc_html($type_label)
		);
	}
}
