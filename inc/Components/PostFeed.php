<?php

namespace BuiltNorth\Utility\Components;

use BuiltNorth\Utility\Component;
use WP_Query;

class PostFeed
{
	private const DEFAULT_POSTS_PER_PAGE = 9;
	private const DEFAULT_COLUMN_COUNT = 3;
	private const DEFAULT_DISPLAY_TYPE = 'grid';
	private const DEFAULT_SELECTION_MODE = 'auto';
	private const DEFAULT_ORDER_BY = 'date';
	private const DEFAULT_ORDER_DIRECTION = 'DESC';

	public static function render($attributes, $post_type, $card_component = null)
	{
		$query = new WP_Query(self::build_query_args($attributes, $post_type));

		if (!$query->have_posts()) {
			return self::render_empty_state($post_type, $attributes);
		}

		$output = self::render_posts($query, $attributes, $post_type);

		// Enqueue slider assets if needed
		if (isset($attributes['displayAs']) && $attributes['displayAs'] === 'slider') {
			wp_enqueue_script('swiper-slider');
		}

		wp_reset_postdata();
		return $output;
	}

	private static function render_posts($query, $attributes, $post_type)
	{
		$display_type = $attributes['displayAs'] ?? self::DEFAULT_DISPLAY_TYPE;
		$column_count = $attributes['columnCount'] ?? self::DEFAULT_COLUMN_COUNT;
		$selection_mode = $attributes['selectionMode'] ?? self::DEFAULT_SELECTION_MODE;
		$template_part_slug = $attributes['templatePartSlug'] ?? '';

		$wrapper_classes = [
			"post-query post-query--{$post_type}",
			"post-query--{$display_type}"
		];

		if ($display_type === 'grid') {
			$wrapper_classes[] = "grid";
			$wrapper_classes[] = "grid-has-{$column_count}";
		}

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
			<?php if ($display_type === 'slider') : ?>
				<swiper-container 
					slides-per-view="1" 
					loop="true" 
					pagination="true" 
					navigation="true" 
					space-between="32px" 
					grab-cursor="true" 
					breakpoints='{
						"768": {
							"slidesPerView": <?php echo $column_count; ?>
						}
					}'
				>
			<?php endif; ?>
			
			<?php
			while ($query->have_posts()) {
				$query->the_post();
				
				if ($display_type === 'slider') {
					echo '<swiper-slide>';
				}
				
				echo PostCard::render($post_type, $display_type, $template_part_slug);
				
				if ($display_type === 'slider') {
					echo '</swiper-slide>';
				}
			}
			?>
			
			<?php if ($display_type === 'slider') : ?>
				</swiper-container>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function build_query_args($attributes, $post_type)
	{
		$selection_mode = $attributes['selectionMode'] ?? self::DEFAULT_SELECTION_MODE;
		$selected_posts = $attributes['selectedPosts'] ?? [];

		// Handle manual selection mode
		if ($selection_mode === 'manual' && !empty($selected_posts)) {
			return [
				'post_type' => $post_type,
				'post__in' => $selected_posts,
				'orderby' => 'post__in', // Maintain order of selected posts
				'posts_per_page' => count($selected_posts), // Show all selected posts
				'post_status' => 'publish',
			];
		}

		// Handle automatic selection mode
		$query_args = [
			'post_type' => $post_type,
			'posts_per_page' => $attributes['postsPerPage'] ?? self::DEFAULT_POSTS_PER_PAGE,
			'orderby' => $attributes['orderPostsBy'] ?? self::DEFAULT_ORDER_BY,
			'order' => $attributes['orderPostsDirection'] ?? self::DEFAULT_ORDER_DIRECTION,
			'post_status' => 'publish',
			'paged' => false, // Disabled pagination for now
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

	private static function render_empty_state($post_type, $attributes = [])
	{
		$selection_mode = $attributes['selectionMode'] ?? self::DEFAULT_SELECTION_MODE;
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