<?php

/**
 * Image Setup
 *
 * Configures custom image sizes and related WordPress image settings.
 * Handles registration of custom sizes, removal of defaults, and
 * srcset configuration.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Setup
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Setup;


/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
	die;
}

/**
 * Class Main
 */
class ImageSetup
{
	/**
	 * Singleton.
	 *
	 * @var ImageSetup|null Class object.
	 */
	protected static $instance = null;

	/**
	 * Singleton method.
	 *
	 * @static
	 *
	 * @return  ImageSetup
	 * @since   1.0.0
	 * @access  public
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
			self::$instance->initialize();
		}

		return self::$instance;
	}

	/**
	 * Method to initialize hooks.
	 *
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function initialize()
	{
		add_filter('intermediate_image_sizes', array($this, 'remove_default_image_sizes'), 10, 1);
		add_filter('max_srcset_image_width', array($this, 'update_max_srcset_image_width'), 10, 2);
		add_filter('wp_calculate_image_srcset', array($this, 'cap_srcset_to_requested_size'), 10, 5);
		// If after_setup_theme has already fired (e.g. called at priority > 10),
		// invoke directly; otherwise defer to the action.
		if ( did_action( 'after_setup_theme' ) ) {
			$this->add_image_sizes();
		} else {
			add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		}
		add_filter('image_size_names_choose', array($this, 'image_size_names'));
	}


	/**
	 * Remove WordPress default image sizes
	 *
	 * @param array $sizes Current image sizes.
	 * @return array Modified image sizes.
	 */
	public function remove_default_image_sizes($sizes)
	{
		$default_targets = ['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048'];
		
		/**
		 * Filter the default image sizes to remove.
		 * 
		 * @param array $targets Array of default image size names to remove.
		 */
		$targets = apply_filters('wp_utility_remove_default_sizes', $default_targets);
		
		return array_diff($sizes, $targets);
	}

	/**
	 * Set Max srcset size
	 *
	 * @return int Max width in pixels.
	 */
	public function update_max_srcset_image_width()
	{
		/**
		 * Filter the maximum srcset image width.
		 * 
		 * 1800px covers 1440p displays at DPR 1 and standard 1080p displays.
		 * Must match wide_xlarge width so that size is included in srcset.
		 *
		 * @param int $max_width Maximum width in pixels. Default 1800.
		 */
		return apply_filters('wp_utility_max_srcset_width', 1800);
	}

	/**
	 * Do not offer srcset candidates wider than the requested image size.
	 *
	 * Stops a `wide_large` (1200px) request from also advertising 1800w in srcset.
	 *
	 * @param array<string, array<string, mixed>>|false $sources    Srcset sources.
	 * @param array<int, int|bool>                        $size_array Requested size [width, height, crop].
	 * @param string                                      $image_src  Image URL.
	 * @param array<string, mixed>                        $image_meta Attachment metadata.
	 * @param int                                         $attachment_id Attachment ID.
	 * @return array<string, array<string, mixed>>|false
	 */
	public function cap_srcset_to_requested_size($sources, $size_array, $image_src, $image_meta, $attachment_id)
	{
		if (! is_array($sources) || empty($size_array[0])) {
			return $sources;
		}

		$max_width = (int) $size_array[0];

		// Only cap named intermediate sizes — leave full/original uncapped.
		if ($max_width <= 0 || $max_width >= (int) apply_filters('wp_utility_max_srcset_width', 1800)) {
			return $sources;
		}

		foreach (array_keys($sources) as $width) {
			if ((int) $width > $max_width) {
				unset($sources[$width]);
			}
		}

		return $sources;
	}

	/**
	 * Add custom image sizes.
	 * 
	 * @note Consider srcset when addin new images. 
	 * Utiilze current pattern so images scale.
	 */
	public function add_image_sizes()
	{

		// Add support for custom images if not already defined
		if (!current_theme_supports('post-thumbnails')) {
			add_theme_support('post-thumbnails');
		}

		// Default image sizes
		// Steps: 400 → 600 → 800 → 1200 → 1800 (~1.5× each step)
		// Covers mobile DPR-1 through 1440p desktop DPR-1 / tablet DPR-2.
		// max_srcset_image_width is set to 1800 to match wide_xlarge.
		$default_sizes = [
			'wide_xlarge'  => [1800, 99999, false], // 1440p DPR-1, 900px-wide container DPR-2
			'wide_large'   => [1200, 99999, false], // 1080p/laptop DPR-1, 600px container DPR-2
			'wide_medium'  => [800,  99999, false], // tablet DPR-1, mobile DPR-2
			'wide_small'   => [600,  99999, false], // large mobile DPR-1, small tablet
			'wide_xsmall'  => [400,  99999, false], // small mobile DPR-1
		];

		/**
		 * Filter the image sizes before registration.
		 * 
		 * @param array $sizes Array of image sizes where key is the size name 
		 *                     and value is an array of [width, height, crop].
		 */
		$sizes = apply_filters('wp_utility_image_sizes', $default_sizes);

		// Register each image size
		foreach ($sizes as $name => $dimensions) {
			if (isset($dimensions[0], $dimensions[1])) {
				$crop = isset($dimensions[2]) ? $dimensions[2] : false;
				add_image_size($name, $dimensions[0], $dimensions[1], $crop);
			}
		}
	}

	/**
	 * Assign names to the images defined above.
	 * Now, the block editor can use them.
	 * 
	 * @param $sizes
	 */
	public function image_size_names($sizes)
	{
		$default_names = [
			'wide_xlarge' => __('Extra Large'),
			'wide_large' => __('Large'),
			'wide_medium' => __('Medium'),
			'wide_small' => __('Small'),
			'wide_xsmall' => __('Extra Small'),
		];

		/**
		 * Filter the image size display names.
		 * 
		 * @param array $names Array of image size names to add to the block editor.
		 */
		$custom_names = apply_filters('wp_utility_image_size_names', $default_names);

		return array_merge($sizes, $custom_names);
	}

	/**
	 * Setup the image configuration.
	 * 
	 * @static
	 * @return ImageSetup
	 * @since 1.0.0
	 * @access public
	 */
	public static function setup()
	{
		return self::instance();
	}

	/**
	 * Alias for setup() method for backward compatibility.
	 * 
	 * @deprecated Use setup() instead.
	 * @static
	 * @return ImageSetup
	 * @since 1.0.0
	 * @access public
	 */
	public static function render()
	{
		return self::instance();
	}
}
