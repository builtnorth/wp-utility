<?php

namespace BuiltNorth\Utility\Components\PostDisplay;

class PostGrid extends PostDisplay
{
	public static function render($props)
	{
		extract(self::getCommonProps($props));
		$wrap_class = "grid grid-has-{$column_count}";
		$styles = self::getWrapperAttributes($post_type, $wrap_class);

		ob_start();
?>
		<div <?php echo wp_kses_data($styles); ?>>
			<?php echo self::renderPosts($posts, $card_component, $post_type); ?>
		</div>
<?php
		return ob_get_clean();
	}
}
