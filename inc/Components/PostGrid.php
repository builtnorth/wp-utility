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

		ob_start();
?>
		<div class="<?php echo esc_attr($wrap_class); ?>">
			<?php if (!empty($posts)) : ?>
				<?php foreach ($posts as $post) : ?>
					<div key="<?php echo esc_attr($post->ID); ?>">
						<?php echo call_user_func($card_component, $post, $post_type); ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
<?php
		return ob_get_clean();
	}
}
