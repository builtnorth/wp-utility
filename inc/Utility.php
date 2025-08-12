<?php
/**
 * Utility Alias
 *
 * Backward compatibility alias for Utility class to maintain
 * existing implementations after namespace reorganization.
 * 
 * @package BuiltNorth\WPUtility
 * @since 1.0.0
 * @deprecated Use BuiltNorth\WPUtility\Utilities\Utility instead
 */

namespace BuiltNorth\WPUtility;

class_alias('BuiltNorth\WPUtility\Utilities\Utility', 'BuiltNorth\WPUtility\Utility');