<?php

namespace BuiltNorth\Utility\Utilities;

class ReadingTime
{
	/**
	 * Estimated Reading Time
	 * @link https://medium.com/@shaikh.nadeem/how-to-add-reading-time-in-wordpress-without-using-plugin-d2e8af7b1239
	 */
	public static function render()
	{

		$word_count = str_word_count(strip_tags(get_the_content()));
		$readingtime = ceil($word_count / 200);
		return $readingtime;
	}
}
