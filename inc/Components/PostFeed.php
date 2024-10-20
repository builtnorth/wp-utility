<?php

namespace BuiltNorth\Utility\Components;

use BuiltNorth\Utility\Components\Pagination;

use WP_Query;

class PostFeed
{
	public static function render($attributes, $post_type, $card_component)
	{
		$query_args = self::build_query_args($attributes, $post_type);
		$posts = new WP_Query($query_args);

		if (!$posts->have_posts()) {
			return self::render_placeholder();
		}

		$display_component = $attributes['displayAs'] === 'slider' ? 'Slider' : 'PostGrid';
		$component_class = "BuiltNorth\\Utility\\Components\\{$display_component}";

		$props = [
			'attributes' => $attributes,
			'posts' => $posts->posts,
			'CardComponent' => $card_component,
			'postType' => $post_type
		];

		$output = $component_class::render($props);

		if (!$attributes['displayAs'] && $posts->found_posts > ($attributes['postsPerPage'] ?? 9)) {
			$output .= Component::Pagination();
		}

		wp_reset_postdata();

		return $output;
	}

	private static function build_query_args($attributes, $post_type)
	{
		$query_args = [
			'post_type' => $post_type,
			'posts_per_page' => $attributes['postsPerPage'] ?? 9,
			'orderby' => $attributes['orderPostsBy'] ?? 'date',
			'order' => $attributes['orderPostsDirection'] ?? 'DESC',
			'post_status' => 'publish',
		];

		if (!empty($attributes['selectedTaxonomy']) && !empty($attributes['selectedTerms'])) {
			$tax_query = [
				[
					'taxonomy' => $attributes['selectedTaxonomy'],
					'field' => 'term_id',
					'terms' => $attributes['selectedTerms'],
				]
			];
			$query_args['tax_query'] = $tax_query;
		}

		return $query_args;
	}

	private static function render_placeholder()
	{
		return '<div class="placeholder">' .
			__('No posts found. Please add some, check the taxonomy filter settings, or remove this block.', 'built') .
			'</div>';
	}
}
