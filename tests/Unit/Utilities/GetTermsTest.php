<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\GetTerms;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\GetTerms
 */
class GetTermsTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('esc_html')->andReturnUsing(static fn( $text ) => $text);
		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn( $text ) => $text);
		WP_Mock::userFunction('esc_url')->andReturnUsing(static fn( $url ) => $url);
	}

	private function make_term(int $id, string $name): object {
		$term = new \stdClass();
		$term->term_id = $id;
		$term->name    = $name;

		return $term;
	}

	public function test_no_terms_renders_nothing(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([]);
		WP_Mock::userFunction('is_wp_error')->andReturn(false);

		$this->expectOutputString('');
		GetTerms::render(1, 'category');
	}

	public function test_wp_error_renders_nothing(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn(new \WP_Error('no_terms', 'none'));
		WP_Mock::userFunction('is_wp_error')->andReturn(true);

		$this->expectOutputString('');
		GetTerms::render(1, 'category');
	}

	public function test_renders_all_terms_as_an_unordered_list(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([
			$this->make_term(1, 'News'),
			$this->make_term(2, 'Updates'),
		]);
		WP_Mock::userFunction('is_wp_error')->andReturn(false);
		WP_Mock::userFunction('get_term_link')->andReturn('https://example.com/term/');

		$this->expectOutputString(
			'<ul class="query__terms">' .
			'<li class="query__term">News</li>' .
			'<li class="query__term">Updates</li>' .
			'</ul>'
		);
		GetTerms::render(1, 'category');
	}

	public function test_first_term_only_renders_a_single_span(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([
			$this->make_term(1, 'News'),
			$this->make_term(2, 'Updates'),
		]);
		WP_Mock::userFunction('is_wp_error')->andReturn(false);
		WP_Mock::userFunction('get_term_link')->andReturn('https://example.com/term/');

		$this->expectOutputString(
			'<span class="query__terms"><span class="query__term">News</span></span>'
		);
		GetTerms::render(1, 'category', false, true);
	}

	public function test_taxonomy_link_wraps_name_in_an_anchor(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([
			$this->make_term(5, 'News'),
		]);
		WP_Mock::userFunction('is_wp_error')->andReturn(false);
		WP_Mock::userFunction('get_term_link')
			->with(5)
			->andReturn('https://example.com/news/');

		$this->expectOutputString(
			'<ul class="query__terms">' .
			'<li class="query__term"><a class="query__term-link is-interior-link" href="https://example.com/news/">News</a></li>' .
			'</ul>'
		);
		GetTerms::render(1, 'category', true);
	}

	public function test_taxonomy_link_falls_back_to_plain_name_when_link_is_wp_error(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([
			$this->make_term(5, 'News'),
		]);
		WP_Mock::userFunction('get_term_link')
			->with(5)
			->andReturn(new \WP_Error('invalid', 'bad'));
		WP_Mock::userFunction('is_wp_error')
			->andReturnUsing(static fn( $value ) => $value instanceof \WP_Error);

		$this->expectOutputString(
			'<ul class="query__terms"><li class="query__term">News</li></ul>'
		);
		GetTerms::render(1, 'category', true);
	}

	public function test_custom_class_prefixes_wrapper_and_item_classes(): void {
		WP_Mock::userFunction('get_the_terms')->andReturn([
			$this->make_term(1, 'News'),
		]);
		WP_Mock::userFunction('is_wp_error')->andReturn(false);
		WP_Mock::userFunction('get_term_link')->andReturn('https://example.com/term/');

		$this->expectOutputString(
			'<ul class="card__terms"><li class="card__term">News</li></ul>'
		);
		GetTerms::render(1, 'category', false, false, 'card');
	}
}
