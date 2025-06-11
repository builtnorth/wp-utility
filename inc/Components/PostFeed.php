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
			return self::render_empty($post_type);
		}

		$output = self::render_posts($query, $attributes, $post_type);

		// Add pagination if needed
		if (($attributes['showPagination'] ?? false) && $query->found_posts > ($attributes['postsPerPage'] ?? 9)) {
			$output .= Component::Pagination($query);
		}

		wp_reset_postdata();
		return $output;
	}

	private static function render_posts($query, $attributes, $post_type)
	{
		$display_type = $attributes['displayAs'] ?? 'grid';
		$column_count = $attributes['columnCount'] ?? 3;

		$wrapper_classes = [
			"{$post_type}-query",
			"{$post_type}-query--{$display_type}",
			$display_type,
			"{$display_type}-has-{$column_count}"
		];

		$wrapper_attrs = get_block_wrapper_attributes([
			'class' => implode(' ', $wrapper_classes),
			'data-post-type' => $post_type,
			'data-display-type' => $display_type,
			'data-column-count' => $column_count
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

		// Add taxonomy filter if specified
		if (!empty($attributes['selectedTaxonomy']) && !empty($attributes['selectedTerms'])) {
			$query_args['tax_query'] = [[
				'taxonomy' => $attributes['selectedTaxonomy'],
				'field' => 'term_id',
				'terms' => $attributes['selectedTerms'],
			]];
		}

		return $query_args;
	}

	private static function render_empty($post_type)
	{
		$post_type_obj = get_post_type_object($post_type);
		$type_label = $post_type_obj ? strtolower($post_type_obj->labels->name) : 'posts';

		return sprintf(
			'<div class="notice notice-info">No %s found.</div>',
			esc_html($type_label)
		);
	}
}
