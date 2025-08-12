<?php
/**
 * Helper Alias
 *
 * Backward compatibility alias for Helper class to maintain
 * existing implementations after namespace reorganization.
 * 
 * @package BuiltNorth\WPUtility
 * @since 1.0.0
 * @deprecated Use BuiltNorth\WPUtility\Helpers\Helper instead
 */

namespace BuiltNorth\WPUtility;

class_alias('BuiltNorth\WPUtility\Helpers\Helper', 'BuiltNorth\WPUtility\Helper');