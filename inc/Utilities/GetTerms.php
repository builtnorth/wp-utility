<?php

/**
 * ------------------------------------------------------------------
 * Get Terms
 * ------------------------------------------------------------------
 * 
 * This class is used to get and render the terms for a post.
 * 
 * @package BuiltNorth\Utility
 * @since 1.0.0
 */

namespace BuiltNorth\WPUtility\Utilities;

class GetTerms
{
	/**
	 * Get and render the terms
	 *
	 * @param int|null $post_id Post ID
	 * @param string|null $taxonomy Taxonomy name
	 * @param bool $taxonomy_link Whether to link the terms
	 * @param bool $first_term_only Whether to display only the first term
	 * @param string|null $class Additional class for the term list or item
	 * @return void
	 */
	public static function render(
		$post_id = null,
		$taxonomy = null,
		$taxonomy_link = false,
		$first_term_only = false,
		$class = null
	) {
		$terms = get_the_terms($post_id, $taxonomy);

		if (empty($terms) || is_wp_error($terms)) {
			return;
		}

		$terms_to_render = $first_term_only ? array_slice($terms, 0, 1) : $terms;
		$wrapper_tag = $first_term_only ? 'span' : 'ul';
		$item_tag = $first_term_only ? 'span' : 'li';

		$wrapper_class = $class ? "{$class}__terms" : 'query__terms';
		echo "<{$wrapper_tag} class='{$wrapper_class}'>";

		foreach ($terms_to_render as $term) {
			self::renderTerm($term, $taxonomy_link, $class, $item_tag);
		}

		echo "</{$wrapper_tag}>";
	}

	/**
	 * Render a term
	 */
	protected static function renderTerm($term, $taxonomy_link, $class, $tag)
	{
		$name = $term->name;
		$link = get_term_link($term->term_id);
		$term_class = $class ? "{$class}__term" : 'query__term';

		$content = $taxonomy_link
			? "<a class='{$term_class}-link is-interior-link' href='{$link}'>{$name}</a>"
			: $name;

		echo "<{$tag} class='{$term_class}'>{$content}</{$tag}>";
	}
}
