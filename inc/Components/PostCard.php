<?php

namespace BuiltNorth\Utility\Components;

/**
 * PostCard Component
 * 
 * Renders individual post cards with template lookup and fallback.
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
		$permalink = get_permalink();
		$title = get_the_title();

		ob_start();
?>
		
			<?php
			$template_found = self::render_template_part($post_type, $template_part_slug);

			if (!$template_found) {
				echo self::render_fallback();
			}
		
			?>
		
	<?php
		return ob_get_clean();
	}

	/**
	 * Render template part for the post type
	 *
	 * @param string $post_type Post type
	 * @param string $template_part_slug Optional custom template part slug
	 * @return bool Whether template was found and rendered
	 */
	private static function render_template_part($post_type, $template_part_slug = '')
	{
		// Use custom template part slug if provided, otherwise use default naming convention
		$slug = !empty($template_part_slug) ? $template_part_slug : strtolower($post_type) . '-card';
		$block = '<!-- wp:template-part {"slug":"' . esc_attr($slug) . '"} /-->';

		// Try to render the template part, but catch any errors
		try {
			$output = do_blocks($block);

			// Remove the outer <section class="wp-block-template-part">...</section>
			$output = preg_replace(
				'/^<section class="wp-block-template-part[^"]*">(.*)<\\/section>$/s',
				'$1',
				trim($output)
			);

			if (trim($output)) {
				echo $output;
				return true;
			}
		} catch (Exception $e) {
			// If template part fails, fall back to default card
			return false;
		}

		return false;
	}

	/**
	 * Render a fallback card when no template is found
	 */
	private static function render_fallback()
	{
		ob_start();
	?>
		<div class="post-card__content post-card__content--fallback">
			<h2 class="post-card__title"><?php the_title(); ?></h2>
			<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
			<a href="<?php the_permalink(); ?>" class="post-card__link">Read More</a>
		</div>
<?php
		return ob_get_clean();
	}
}
