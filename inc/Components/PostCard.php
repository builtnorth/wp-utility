<?php

namespace BuiltNorth\Utility\Components;

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
	public static function render($post_type = 'post', $display_type = 'grid', $template_part_slug = '')
	{
		ob_start();
		self::render_template_part($post_type, $template_part_slug);
		return ob_get_clean();
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

		try {
			$output = do_blocks($block);
			// Remove the outer <section class="wp-block-template-part">...</section>
			$output = preg_replace(
				'/^<section class="wp-block-template-part[^"]*">(.*)<\/section>$/s',
				'$1',
				trim($output)
			);
			if (trim($output)) {
				echo $output;
			}
		} catch (\Exception $e) {
			// Do nothing if template part fails
		}
	}
}
