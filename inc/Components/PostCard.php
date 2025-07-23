<?php

namespace BuiltNorth\WPUtility\Components;

/**
 * PostCard Component
 * 
 * Renders individual post cards with template lookup only.
 */
class PostCard
{
	/**
	 * Render a single post card
	 *
	 * @param string $post_type Post type
	 * @param string $display_type Display type (grid, list, slider)
	 * @param string $template_part_slug Optional custom template part slug
	 * @return string Rendered post card HTML
	 */
	public static function render($post_type = 'post', $display_type = 'grid', $template_part_slug = 'post-card')
	{
		self::render_template_part($post_type, $template_part_slug);
	}

	/**
	 * Render template part for the post type
	 *
	 * @param string $post_type Post type
	 * @param string $template_part_slug Optional custom template part slug
	 * @return void
	 */
	private static function render_template_part($post_type, $template_part_slug = '')
	{
		// Use custom template part slug if provided, otherwise use default naming convention
		$slug = !empty($template_part_slug) ? $template_part_slug : strtolower($post_type) . '-card';
		$block = '<!-- wp:template-part {"slug":"' . esc_attr($slug) . '"} /-->';

		$output = do_blocks($block);
		echo $output;
	}
}