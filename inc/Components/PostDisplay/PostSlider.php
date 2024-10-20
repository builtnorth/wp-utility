<?php

namespace BuiltNorth\Utility\Components\PostDisplay;

class PostSlider extends PostDisplay
{
	public static function render($props)
	{
		extract(self::getCommonProps($props));
		$posts_per_page = $attributes['postsPerPage'] ?? count($posts);
		$styles = self::getWrapperAttributes($post_type, $display_type, $column_count);

		ob_start();
?>
		<div <?php echo wp_kses_data($styles); ?>>
			<div class="swiper-container"
				data-slides-per-view="<?php echo esc_attr($column_count); ?>"
				data-loop="true"
				data-pagination="true"
				data-navigation="true"
				data-space-between="32">
				<div class="swiper-wrapper">
					<?php if (!empty($posts)) : ?>
						<?php foreach ($posts as $post) : ?>
							<div class="swiper-slide">
								<?php echo call_user_func($card_component, $post, $post_type); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
			</div>
		</div>
<?php
		return ob_get_clean();
	}
}
