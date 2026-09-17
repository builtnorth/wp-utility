<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\GetTitle;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\GetTitle
 */
class GetTitleTest extends WPMockTestCase {

	/**
	 * Stub every conditional GetTitle::render() consults so each test only
	 * has to override the branch it cares about, in the same true/false
	 * order the source checks them.
	 */
	private function mock_conditionals(array $overrides = []): void {
		$defaults = [
			'is_home'            => false,
			'is_singular'        => false,
			'is_tax'             => false,
			'is_archive'         => false,
			'is_category'        => false,
			'is_tag'             => false,
			'is_author'          => false,
			'is_post_type_archive' => false,
			'is_search'          => false,
			'is_404'             => false,
		];

		WP_Mock::userFunction('wp_reset_query')->andReturn(null);

		foreach (array_merge($defaults, $overrides) as $function => $value) {
			WP_Mock::userFunction($function)->andReturn($value);
		}
	}

	public function test_home_with_page_for_posts_uses_that_page_title(): void {
		$this->mock_conditionals(['is_home' => true]);

		WP_Mock::userFunction('get_option')
			->with('page_for_posts')
			->andReturn(42);
		WP_Mock::userFunction('get_the_title')
			->with(42)
			->andReturn('Blog');

		$this->assertSame('Blog', GetTitle::render());
	}

	public function test_singular_uses_the_post_title(): void {
		$this->mock_conditionals(['is_singular' => true]);

		WP_Mock::userFunction('get_the_title')
			->with()
			->andReturn('My Post');

		$this->assertSame('My Post', GetTitle::render());
	}

	public function test_taxonomy_archive_uses_single_term_title(): void {
		$this->mock_conditionals(['is_tax' => true]);

		WP_Mock::userFunction('single_term_title')
			->with('', false)
			->andReturn('Term Name');

		$this->assertSame('Term Name', GetTitle::render());
	}

	public function test_generic_archive_uses_the_archive_title(): void {
		$this->mock_conditionals(['is_archive' => true]);

		WP_Mock::userFunction('get_the_archive_title')
			->with('', false)
			->andReturn('Archives');

		$this->assertSame('Archives', GetTitle::render());
	}

	public function test_post_type_archive_uses_post_type_archive_title(): void {
		$this->mock_conditionals(['is_post_type_archive' => true]);

		WP_Mock::userFunction('post_type_archive_title')
			->with('', false)
			->andReturn('Projects');

		$this->assertSame('Projects', GetTitle::render());
	}

	public function test_search_includes_the_query_in_quotes(): void {
		$this->mock_conditionals(['is_search' => true]);

		WP_Mock::userFunction('get_search_query')->andReturn('hello world');
		WP_Mock::userFunction('__')->andReturnUsing(static fn($text) => $text);

		$this->assertSame('Search Results For: "hello world"', GetTitle::render());
	}

	public function test_404_returns_page_not_found(): void {
		$this->mock_conditionals(['is_404' => true]);

		WP_Mock::userFunction('__')->andReturnUsing(static fn($text) => $text);

		$this->assertSame('Page Not Found', GetTitle::render());
	}

	public function test_no_matching_context_falls_back_to_debug_string(): void {
		$this->mock_conditionals();

		WP_Mock::userFunction('__')->andReturnUsing(static fn($text) => $text);

		$this->assertSame('Check built_get_title() Function', GetTitle::render());
	}
}
