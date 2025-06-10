<?php

namespace BuiltNorth\Utility\Components;

/**
 * PostCard Component
 * 
 * Handles the rendering of individual post cards with efficient template loading
 * and caching mechanisms.
 */
class PostCard
{
	/**
	 * Render a single post card
	 *
	 * @param array $props {
	 *     @type array  $attributes   Block attributes
	 *     @type string $post_type    Post type
	 *     @type string $display_type Display type (grid, list, slider)
	 *     @type string $theme        Theme name for template lookup
	 * }
	 * @return string Rendered post card HTML
	 */
	public static function render($props)
	{
		$post_type = $props['post_type'] ?? 'post';
		$display_type = $props['display_as'] ?? 'grid';
		$theme = $props['theme'] ?? 'polaris-blocks';

		// Get the template
		$template = self::getTemplate($post_type, $display_type, $theme);

		ob_start();
?>
		<article class="post-card post-card--<?php echo esc_attr($post_type); ?> post-card--<?php echo esc_attr($display_type); ?>">
			<?php
			if (!$template) {
				echo self::renderFallback();
			} else {
				// Render the template
				$blocks = parse_blocks($template->content);
				foreach ($blocks as $block) {
					echo render_block($block);
				}
			}
			?>
		</article>
	<?php
		return ob_get_clean();
	}

	/**
	 * Get the appropriate template for the post card
	 */
	protected static function getTemplate($post_type, $display_type, $theme)
	{
		// Try display-specific template first
		if ($display_type !== 'grid') {
			$template_slug = $post_type . '-card-' . $display_type;
			$template = get_block_template($template_slug, 'wp_template_part');

			if (!$template) {
				$template = get_block_template($template_slug, 'wp_template_part', array('theme' => $theme));
			}

			if ($template) {
				return $template;
			}
		}

		// Try standard card template
		$template_slug = $post_type . '-card';
		$template = get_block_template($template_slug, 'wp_template_part');

		if (!$template) {
			$template = get_block_template($template_slug, 'wp_template_part', array('theme' => $theme));
		}

		// Fallback to generic post-card template
		if (!$template) {
			$template = get_block_template('post-card', 'wp_template_part', array('theme' => $theme));
		}

		return $template;
	}

	/**
	 * Render a fallback card when no template is found
	 */
	protected static function renderFallback()
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
