<?php

namespace BuiltNorth\Utility\Components;

class PostGrid
{
	public static function render($props)
	{
		$attributes = $props['attributes'] ?? [];
		$posts = $props['posts'] ?? [];
		$card_component = $props['CardComponent'] ?? null;
		$post_type = $props['postType'] ?? 'post';

		$column_count = $attributes['columnCount'] ?? 3;
		$wrap_class = "grid grid-has-{$column_count}";

		// Add Classes & Styles to Block Wrapper Attributes
		$styles = get_block_wrapper_attributes(['class' =>  'query-' . $post_type . ' ' . $wrap_class]);



		ob_start();
?>
		<div <?php echo wp_kses_data($styles); ?>>
			<?php if (!empty($posts)) : ?>
				<?php foreach ($posts as $post) : ?>
					<?php echo call_user_func($card_component, $post, $post_type); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
<?php
		return ob_get_clean();
	}
}
