<?php

namespace BuiltNorth\Utility\Components\PostDisplay;

class PostSlider extends PostDisplay
{
	public static function render($props)
	{
		extract(self::getCommonProps($props));
		$posts_per_page = $attributes['postsPerPage'] ?? count($posts);
		$styles = self::getWrapperAttributes($post_types, $display_type, $column_count);

		ob_start();
?>
		<div <?php echo wp_kses_data($styles); ?>>
			<swiper-container
				slides-per-view="<?php echo esc_attr($column_count); ?>"
				navigation="true"
				pagination="true"
				space-between="32">
				<?php if (!empty($posts)) : ?>
					<?php foreach ($posts as $post) : ?>
						<swiper-slide>
							<?php echo self::renderPosts([$post], $card_component, $post_types); ?>
						</swiper-slide>
					<?php endforeach; ?>
				<?php endif; ?>
			</swiper-container>
		</div>
<?php
		return ob_get_clean();
	}

	public static function enqueue_swiper_files()
	{
		wp_enqueue_script('swiper-element', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js', array(), null, true);
	}
}

// Add action to enqueue Swiper files
add_action('wp_enqueue_scripts', array('BuiltNorth\Utility\Components\PostDisplay\PostSlider', 'enqueue_swiper_files'));
