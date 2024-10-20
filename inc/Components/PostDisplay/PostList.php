<?php

namespace BuiltNorth\Utility\Components\PostDisplay;

class PostList extends PostDisplay
{
	public static function render($props)
	{
		extract(self::getCommonProps($props));
		$wrap_class = "list list-has-{$column_count}";
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
