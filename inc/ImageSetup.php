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

namespace BuiltNorth\Polaris\Helpers;


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

		// wide
		add_image_size('wide_xlarge', 1600, 99999);
		add_image_size('wide_large',  1200, 99999);
		add_image_size('wide_medium', 800,  99999);
		add_image_size('wide_small',  600,  99999);
		add_image_size('wide_xsmall', 300,  99999);

		// square (1:1)
		add_image_size('square_xlarge', 1200, 1200, true);
		add_image_size('square_large',  800,  800,  true);
		add_image_size('square_medium', 600,  600,  true);
		add_image_size('square_small',  300,  300,  true);
		add_image_size('square_xsmall', 150,  150,  true);
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
}
