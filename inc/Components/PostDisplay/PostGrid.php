<?php

namespace BuiltNorth\Utility\Components\PostDisplay;

class PostGrid extends PostDisplay
{
	public static function render($props)
	{
		extract(self::getCommonProps($props));
		$styles = self::getWrapperAttributes($post_type, $display_type, $column_count);

		ob_start();
?>
		<div <?php echo wp_kses_data($styles); ?>>
			<?php echo self::renderPosts($posts, $card_component, $post_type); ?>
		</div>
<?php
		return ob_get_clean();
	}
}
