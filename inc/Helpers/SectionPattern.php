<?php

namespace BuiltNorth\WPUtility\Helpers;

use BuiltNorth\WPUtility\Helper;

class SectionPattern
{
	/**
	 * Render section pattern from theme SVG files.
	 * 
	 * @param array $attributes Block attributes array.
	 * @return string Rendered pattern HTML or empty string.
	 */
	public static function render($attributes)
	{
		$pattern = !empty($attributes['pattern']) ? $attributes['pattern'] : '';
		
		if (empty($pattern)) {
			return '';
		}

		// Get pattern configuration from FeatureManager if available
		if (class_exists('\Polaris\Base\Container')) {
			$container = \Polaris\Base\Container::get_instance();
			if ($container->has('feature_manager')) {
				$feature_manager = $container->get('feature_manager');
				$pattern_config = $feature_manager->get_feature(['blocks', 'editor_experience', 'patterns'], []);
				
				// Check if patterns are enabled and this pattern is available
				if (empty($pattern_config['enabled']) || empty($pattern_config['available_patterns'][$pattern])) {
					return '';
				}
				
				$pattern_dir = isset($pattern_config['pattern_directory']) ? '/' . trim($pattern_config['pattern_directory'], '/') . '/' : '/src/assets/background-patterns/';
			} else {
				// Fallback if FeatureManager not available
				$pattern_dir = '/src/assets/background-patterns/';
			}
		} else {
			// Fallback if Polaris not available
			$pattern_dir = '/src/assets/background-patterns/';
		}

		// Build SVG path
		$theme_dir = get_stylesheet_directory();
		$svg_path = $theme_dir . $pattern_dir . $pattern . '.svg';
		
		if (!file_exists($svg_path)) {
			return '';
		}

		// Load SVG content
		$svg_content = file_get_contents($svg_path);
		if (empty($svg_content)) {
			return '';
		}

		// Build output
		ob_start();
		?>
		<div class="section-pattern has-pattern-<?php echo esc_attr($pattern); ?>" aria-hidden="true">
			<?php echo Helper::EscapeSVG($svg_content); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}