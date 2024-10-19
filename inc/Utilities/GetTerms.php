<?php

namespace BuiltNorth\Utility\Utilities;

class GetTerms
{
	/**
	 * Get the terms
	 */
	public static function render(
		$taxonomy = null,
		$taxonomy_link = false,
		$first_term_only = false
	) {

		// Get the terms
		$terms = get_the_terms(get_the_ID(), $taxonomy);

		// Make sure some exist
		if (!empty($terms) && !is_wp_error($terms)) :

			// Check if only firt term is requested
			if ($first_term_only) :

				// First term only
				$first_term = $terms[0];
				$name = $first_term->name;
				$link = get_term_link($first_term->term_id);

				// Add link if set
				if ($taxonomy_link) {
					echo '<span class="term-list__item"><a class="is-interior-link" href="' . $link . '">' . $name . '</a></span>';
				} else {
					echo '<span class="term-list__item">' . $name . '</span>';
				}

			else :

				echo '<ul class="term-list">';
				foreach ($terms as $term) :
					// Variables
					$name = $term->name;
					$link = get_term_link($term->term_id);

					// Add link if set
					if ($taxonomy_link) {
						echo '<li class="term-list__item"><a class="is-interior-link" href="' . $link . '">' . $name . '</a></li>';
					} else {
						echo '<li class="term-list__item">' . $name . '</li>';
					}

				endforeach;
				echo '</ul>';
			endif;
		endif;
	}
}
