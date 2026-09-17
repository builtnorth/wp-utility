<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\Pagination;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;
use WP_Query;

/**
 * @covers \BuiltNorth\WPUtility\Components\Pagination
 *
 * WP_Mock's onFilter()->with() matches on the literal value passed in, with
 * no Mockery-matcher support, so every registration below predicts the exact
 * $args array Pagination::render() builds before calling apply_filters().
 */
class PaginationTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('__')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('get_query_var')->andReturn(0);
		WP_Mock::userFunction('get_post_type_object')->andReturn(null);
		WP_Mock::userFunction('get_pagenum_link')
			->with(1)
			->andReturn('https://example.com/blog/');
	}

	private function expected_args(string $base, int $total): array {
		return [
			'base' => $base . '%_%',
			'format' => '?paged=%#%',
			'current' => 1,
			'total' => $total,
			'mid_size' => 3,
			'type' => 'list',
			'before_page_number' => '<span class="sr-only">Page </span>',
		];
	}

	public function test_single_page_renders_nothing(): void {
		$query = new WP_Query();
		$query->max_num_pages = 1;

		$expected = $this->expected_args('https://example.com/blog/', 1);
		WP_Mock::onFilter('wp_utility_pagination_args')
			->with($expected, $query)
			->reply($expected);
		WP_Mock::userFunction('paginate_links')->andReturn('');

		$this->assertSame('', Pagination::render($query));
	}

	public function test_multiple_pages_wraps_links_in_an_aria_labelled_nav(): void {
		$query = new WP_Query();
		$query->max_num_pages = 3;

		$expected = $this->expected_args('https://example.com/blog/', 3);
		WP_Mock::onFilter('wp_utility_pagination_args')
			->with($expected, $query)
			->reply($expected);
		WP_Mock::onFilter('wp_utility_pagination_wrapper')
			->with(['class' => 'pagination', 'label' => 'Navigate Between Archive Pages'], $query)
			->reply(['class' => 'pagination', 'label' => 'Navigate Between Archive Pages']);
		WP_Mock::userFunction('paginate_links')->andReturn('<ul><li>1</li></ul>');

		$this->assertSame(
			'<div class="pagination" aria-label="Navigate Between Archive Pages"><ul><li>1</li></ul></div>',
			Pagination::render($query)
		);
	}

	public function test_falls_back_to_the_global_wp_query_when_none_given(): void {
		$GLOBALS['wp_query'] = new WP_Query();
		$GLOBALS['wp_query']->max_num_pages = 1;

		$expected = $this->expected_args('https://example.com/blog/', 1);
		WP_Mock::onFilter('wp_utility_pagination_args')
			->with($expected, $GLOBALS['wp_query'])
			->reply($expected);
		WP_Mock::userFunction('paginate_links')->andReturn('');

		$this->assertSame('', Pagination::render());

		unset($GLOBALS['wp_query']);
	}

	public function test_custom_post_type_archive_uses_the_archive_link_as_base(): void {
		$query = new WP_Query(['post_type' => 'project']);
		$query->max_num_pages = 1;

		$post_type_object = new \stdClass();

		WP_Mock::userFunction('get_post_type_object')
			->with('project')
			->andReturn($post_type_object);
		WP_Mock::userFunction('is_post_type_archive')
			->with('project')
			->andReturn(true);
		WP_Mock::userFunction('get_post_type_archive_link')
			->with('project')
			->andReturn('https://example.com/projects/');

		$expected = $this->expected_args('https://example.com/projects/', 1);
		WP_Mock::onFilter('wp_utility_pagination_args')
			->with($expected, $query)
			->reply($expected);
		WP_Mock::userFunction('paginate_links')->andReturn('');

		$this->assertSame('', Pagination::render($query));
	}

	public function test_array_post_type_uses_the_first_entry(): void {
		$query = new WP_Query(['post_type' => ['project', 'case_study']]);
		$query->max_num_pages = 1;

		WP_Mock::userFunction('get_post_type_object')
			->with('project')
			->andReturn(null);

		$expected = $this->expected_args('https://example.com/blog/', 1);
		WP_Mock::onFilter('wp_utility_pagination_args')
			->with($expected, $query)
			->reply($expected);
		WP_Mock::userFunction('paginate_links')->andReturn('');

		$this->assertSame('', Pagination::render($query));
	}
}
