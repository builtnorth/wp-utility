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
	 * @return string Rendered post card HTML
	 */
	public static function render($post_type = 'post', $display_type = 'grid')
	{
		$template = self::get_template($post_type, $display_type);

		ob_start();
?>
		<article class="post-card post-card--<?php echo esc_attr($post_type); ?> post-card--<?php echo esc_attr($display_type); ?>">
			<?php
			if ($template) {
				$blocks = parse_blocks($template->content);
				foreach ($blocks as $block) {
					echo render_block($block);
				}
			} else {
				echo self::render_fallback();
			}
			?>
		</article>
	<?php
		return ob_get_clean();
	}

	/**
	 * Get the appropriate template for the post card
	 */
	private static function get_template($post_type, $display_type)
	{
		$template_slugs = [];

		// Try display-specific template first (if not grid)
		if ($display_type !== 'grid') {
			$template_slugs[] = "{$post_type}-card-{$display_type}";
		}

		// Try standard card template
		$template_slugs[] = "{$post_type}-card";

		// Fallback to generic post-card template
		$template_slugs[] = 'post-card';

		foreach ($template_slugs as $slug) {
			$template = get_block_template($slug, 'wp_template_part');
			if ($template) {
				return $template;
			}
		}

		return null;
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
