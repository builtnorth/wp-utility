<?php

namespace BuiltNorth\Utility\Components;

class Pagination
{
	public static function render(
		$style = null,
		$text = 'Button Text',
		$link = '#',
		$target = null,
		$screen_reader = null
	) {

		global $wp_query;
		$big = 999999999; // need an unlikely integer
		$translated = __('Page', 'mytextdomain'); // Supply translatable string

		$paginate_links = paginate_links(array(
			'base'                => str_replace($big, '%#%', get_pagenum_link($big)),
			'current'             => max(1, get_query_var('paged')),
			'total'               => $wp_query->max_num_pages,
			'mid_size'            => 3,
			'type'                => 'list',
			'before_page_number'  => '<span class="sr-only">' . $translated . ' </span>',
		));

		// Display the pagination if more than one page is found
		if ($paginate_links) : ?>
			<div class="pagination" aria-label="Navigate Between Archive Pages">
				<?php echo $paginate_links; ?>
			</div>
<?php
		endif;
	}
}
