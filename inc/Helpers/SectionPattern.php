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
		$pattern_align = !empty($attributes['patternAlign']) ? $attributes['patternAlign'] : 'center center';
		
		if (empty($pattern)) {
			return '';
		}

		// Get pattern configuration from FeatureManager if available
		$pattern_config = [];
		$pattern_dir = '/build/assets/background-patterns/';
		
		if (class_exists('\Polaris\Base\Container')) {
			$container = \Polaris\Base\Container::get_instance();
			if ($container->has('feature_manager')) {
				$feature_manager = $container->get('feature_manager');
				$pattern_config = $feature_manager->get_feature(['editor_experience', 'patterns'], []);
				
				// If child theme has no pattern config, try parent theme configuration
				if (empty($pattern_config) && is_child_theme()) {
					// Temporarily switch to parent theme context
					add_filter('stylesheet', function() { return get_template(); }, 999);
					$pattern_config = $feature_manager->get_feature(['editor_experience', 'patterns'], []);
					remove_filter('stylesheet', function() { return get_template(); }, 999);
				}
				
				// Check if patterns are enabled and this pattern is available
				if (!empty($pattern_config)) {
					if (empty($pattern_config['enabled']) || empty($pattern_config['available_patterns'][$pattern])) {
						return '';
					}
					
					$pattern_dir = isset($pattern_config['pattern_directory']) ? '/' . trim($pattern_config['pattern_directory'], '/') . '/' : '/build/assets/background-patterns/';
				}
			}
		}

		// Build SVG path
		$theme_dir = get_stylesheet_directory();
		$svg_path = $theme_dir . $pattern_dir . $pattern . '.svg';
		
		// If pattern not found in child theme, check parent theme
		if (!file_exists($svg_path) && is_child_theme()) {
			$parent_theme_dir = get_template_directory();
			$svg_path = $parent_theme_dir . $pattern_dir . $pattern . '.svg';
			
			if (!file_exists($svg_path)) {
				return '';
			}
		} elseif (!file_exists($svg_path)) {
			return '';
		}

		// Load SVG content
		$svg_content = file_get_contents($svg_path);
		if (empty($svg_content)) {
			return '';
		}

		// Convert alignment value to class name
		$alignment_map = [
			'top left' => 'top-left',
			'top center' => 'top-center',
			'top right' => 'top-right',
			'center left' => 'center-left',
			'center center' => 'center-center',
			'center' => 'center-center',
			'center right' => 'center-right',
			'bottom left' => 'bottom-left',
			'bottom center' => 'bottom-center',
			'bottom right' => 'bottom-right',
		];
		$align_class = isset($alignment_map[$pattern_align]) ? $alignment_map[$pattern_align] : 'center-center';

		// Build output
		ob_start();
		?>
		<div class="section-pattern has-pattern-<?php echo esc_attr($pattern); ?> pattern-align--<?php echo esc_attr($align_class); ?>" aria-hidden="true">
			<?php echo Helper::EscapeSVG($svg_content); ?>
		</div>
		<?php
		return ob_get_clean();
	}
}