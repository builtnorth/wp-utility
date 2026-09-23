<?php

/**
 * Package entry point.
 *
 * @package WPUtility
 **/

namespace BuiltNorth\WPUtility;


/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
	die;
}

/**
 * Kit: single entry point for the package.
 *
 * Named for what it is rather than for its role. `App` is the name every
 * consuming plugin and theme uses for its own root class, so importing this
 * package's entry point forced an alias at the call site — Polaris core's
 * `Dependencies` carried `use BuiltNorth\WPUtility\App as Utility;`,
 * aliasing it alongside two other packages' `App` classes, all three in the
 * same file.
 *
 * `Kit` rather than `Utility`: this namespace already has a
 * {@see Utility} class (a back-compat stub over {@see Utilities\Utility})
 * which consuming block render files import directly, so that name was not
 * available. `Kit` names the loader that boots the `Utility`, `Component` and
 * `Helper` facades.
 */
class Kit
{
	/**
	 * Holds the single instance of this class.
	 *
	 * @var Kit|null
	 */
	protected static $instance = null;

	/**
	 * Get the single instance of this class.
	 *
	 * @return Kit
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct()
	{
		// Constructor now does nothing - initialization moved to boot()
	}

	/**
	 * Boot the utility package.
	 *
	 * Only registers what actually needs a hook. The Component, Helper and
	 * Utility facades and every leaf class are stateless and static, so there is
	 * nothing to instantiate at boot: the predecessor of this method built three
	 * facade instances and looked for an `init()` none of them declared.
	 */
	public function boot()
	{
		if (class_exists(Blocks\MetaGatedBlockSupport::class)) {
			Blocks\MetaGatedBlockSupport::init();
		}
	}
}
