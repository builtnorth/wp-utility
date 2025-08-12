<?php
/**
 * Reading Time Utility
 *
 * Calculates estimated reading time for content based on word count
 * and configurable reading speed.
 *
 * @package BuiltNorth\WPUtility
 * @subpackage Utilities
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Utilities;

class ReadingTime
{
	/**
	 * Estimated Reading Time
	 * @link https://medium.com/@shaikh.nadeem/how-to-add-reading-time-in-wordpress-without-using-plugin-d2e8af7b1239
	 */
	public static function render()
	{
		$word_count = str_word_count(strip_tags(get_the_content()));
		
		/**
		 * Filter the words per minute for reading time calculation.
		 * 
		 * @param int $words_per_minute Words per minute. Default 200.
		 */
		$words_per_minute = apply_filters('wp_utility_reading_time_wpm', 200);
		
		$readingtime = ceil($word_count / $words_per_minute);
		return $readingtime;
	}
}
