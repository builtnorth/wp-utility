<?php

/**
 * ------------------------------------------------------------------
 * Class: ImageSetup
 * ------------------------------------------------------------------
 *
 * Add custom image sizes and related functions.
 *
 * @package BuiltStarter
 * @since BuiltStarter 1.0.0
 * 
 **/

namespace BuiltNorth\WPUtility\Utilities;


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
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Method to initialize.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init()
	{
		add_filter('intermediate_image_sizes', array($this, 'remove_default_image_sizes'), 10, 1);
		add_filter('max_srcset_image_width', array($this, 'update_max_srcset_image_width'), 10, 2);
		add_action('after_setup_theme', array($this, 'add_image_sizes'));
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
		$targets = ['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048'];
		return array_diff($sizes, $targets);
	}

	/**
	 * Set Max srcset size
	 *
	 * @return int Max width in pixels.
	 */
	public function update_max_srcset_image_width()
	{
		return 1600;
	}

	/**
	 * Add custom image sizes.
	 * 
	 * @note Consider srcset when addin new images. 
	 * Utiilze current pattern so images scale.
	 */
	public function add_image_sizes()
	{

		// Add support for custom images
		add_theme_support('post-thumbnails');

		// Default image sizes
		$default_sizes = [
			// wide
			'wide_xlarge' => [1600, 99999, false],
			'wide_large'  => [1200, 99999, false],
			'wide_medium' => [800,  99999, false],
			'wide_small'  => [600,  99999, false],
			'wide_xsmall' => [300,  99999, false],

			// square (1:1)
			'square_xlarge' => [1200, 1200, true],
			'square_large'  => [800,  800,  true],
			'square_medium' => [600,  600,  true],
			'square_small'  => [300,  300,  true],
			'square_xsmall' => [150,  150,  true],
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

		return array_merge($sizes, array(
			'wide_xlarge' => __('Extra Large'),
			'wide_large' => __('Large'),
			'wide_medium' => __('Medium'),
			'wide_small' => __('Small'),
			'wide_xsmall' => __('Extra Small'),
		));
	}

	public static function render()
	{
		return self::instance();
	}
}
