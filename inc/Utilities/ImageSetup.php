<?php
/**
 * ImageSetup Alias
 *
 * Backward compatibility alias for ImageSetup class to maintain
 * existing implementations after moving to Setup namespace.
 * 
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 * @deprecated Use BuiltNorth\WPUtility\Setup\ImageSetup instead
 */

namespace BuiltNorth\WPUtility\Utilities;

class_alias('BuiltNorth\WPUtility\Setup\ImageSetup', 'BuiltNorth\WPUtility\Utilities\ImageSetup');