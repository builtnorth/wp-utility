<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\Breadcrumbs;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Components\Breadcrumbs
 */
class BreadcrumbsTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('home_url')->andReturnUsing(
			static function ( string $path = '/' ): string {
				return 'https://example.com' . ( str_starts_with( $path, '/' ) ? $path : '/' . $path );
			}
		);
	}

	public function tearDown(): void {
		unset( $GLOBALS['post'] );
		parent::tearDown();
	}

	/**
	 * A static front page also satisfies is_page(); without the front-page
	 * guard it was appended a second time as the "current" item, yielding a
	 * "Home → Home" trail in the BreadcrumbList schema.
	 */
	public function test_front_page_trail_is_just_home(): void {
		WP_Mock::userFunction('is_front_page')->andReturn(true);

		$this->assertSame(
			[ [ 'text' => 'Home', 'url' => 'https://example.com/' ] ],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_top_level_page_trail_is_home_then_page(): void {
		$GLOBALS['post']              = new \stdClass();
		$GLOBALS['post']->ID          = 7;
		$GLOBALS['post']->post_parent = 0;

		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(true);
		WP_Mock::userFunction('get_the_title')->andReturn('About');
		WP_Mock::userFunction('get_permalink')->andReturn('https://example.com/about/');

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'About', 'url' => 'https://example.com/about/' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_nested_page_trail_includes_ancestors(): void {
		$GLOBALS['post']              = new \stdClass();
		$GLOBALS['post']->ID          = 30;
		$GLOBALS['post']->post_parent = 10;

		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(true);
		WP_Mock::userFunction('get_post_ancestors')
			->with(30)
			// WP returns closest-first; Breadcrumbs reverses to root-first.
			->andReturn([10, 20]);
		WP_Mock::userFunction('get_the_title')
			->andReturnUsing(static function ( $id = null ) {
				return match ( (int) $id ) {
					10 => 'Parent',
					20 => 'Grandparent',
					default => 'Child',
				};
			});
		WP_Mock::userFunction('get_permalink')
			->andReturnUsing(static function ( $id = null ) {
				return match ( (int) $id ) {
					10 => 'https://example.com/parent/',
					20 => 'https://example.com/grandparent/',
					default => 'https://example.com/child/',
				};
			});

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Grandparent', 'url' => 'https://example.com/grandparent/' ],
				[ 'text' => 'Parent', 'url' => 'https://example.com/parent/' ],
				[ 'text' => 'Child', 'url' => 'https://example.com/child/' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_single_post_trail_includes_blog_page_when_set(): void {
		$GLOBALS['post']            = new \stdClass();
		$GLOBALS['post']->ID        = 55;
		$GLOBALS['post']->post_type = 'post';

		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(true);
		WP_Mock::userFunction('get_post_type_object')->andReturn(null);
		WP_Mock::userFunction('get_option')
			->with('page_for_posts')
			->andReturn(12);
		WP_Mock::userFunction('get_the_title')
			->andReturnUsing(static function ( $id = null ) {
				return (int) $id === 12 ? 'Blog' : 'Hello World';
			});
		WP_Mock::userFunction('get_permalink')
			->andReturnUsing(static function ( $id = null ) {
				return (int) $id === 12
					? 'https://example.com/blog/'
					: 'https://example.com/hello-world/';
			});

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Blog', 'url' => 'https://example.com/blog/' ],
				[ 'text' => 'Hello World', 'url' => 'https://example.com/hello-world/' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_category_archive_trail_is_home_blog_then_term(): void {
		$category       = new \stdClass();
		$category->name = 'News';

		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(false);
		WP_Mock::userFunction('is_archive')->andReturn(true);
		WP_Mock::userFunction('is_category')->andReturn(true);
		WP_Mock::userFunction('get_queried_object')->andReturn($category);
		WP_Mock::userFunction('get_option')
			->with('page_for_posts')
			->andReturn(12);
		WP_Mock::userFunction('get_permalink')
			->with(12)
			->andReturn('https://example.com/blog/');
		WP_Mock::userFunction('get_term_link')
			->with($category)
			->andReturn('https://example.com/category/news/');

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Blog', 'url' => 'https://example.com/blog/' ],
				[ 'text' => 'News', 'url' => 'https://example.com/category/news/' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_search_trail_includes_search_results(): void {
		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(false);
		WP_Mock::userFunction('is_archive')->andReturn(false);
		WP_Mock::userFunction('is_search')->andReturn(true);
		WP_Mock::userFunction('get_search_link')
			->andReturn('https://example.com/?s=widgets');

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Search Results', 'url' => 'https://example.com/?s=widgets' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_404_trail_includes_not_found(): void {
		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(false);
		WP_Mock::userFunction('is_archive')->andReturn(false);
		WP_Mock::userFunction('is_search')->andReturn(false);
		WP_Mock::userFunction('is_404')->andReturn(true);

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Page Not Found', 'url' => 'https://example.com/404' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_blog_home_trail_includes_blog_page(): void {
		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(false);
		WP_Mock::userFunction('is_archive')->andReturn(false);
		WP_Mock::userFunction('is_search')->andReturn(false);
		WP_Mock::userFunction('is_404')->andReturn(false);
		WP_Mock::userFunction('is_home')->andReturn(true);
		WP_Mock::userFunction('get_option')
			->with('page_for_posts')
			->andReturn(12);
		WP_Mock::userFunction('get_permalink')
			->with(12)
			->andReturn('https://example.com/blog/');

		$this->assertSame(
			[
				[ 'text' => 'Home', 'url' => 'https://example.com/' ],
				[ 'text' => 'Blog', 'url' => 'https://example.com/blog/' ],
			],
			Breadcrumbs::get_breadcrumb_data()
		);
	}

	public function test_render_outputs_nothing_on_front_page_by_default(): void {
		WP_Mock::userFunction('is_front_page')->andReturn(true);

		$this->expectOutputString('');
		Breadcrumbs::render();
	}

	public function test_render_escapes_linked_crumb_text(): void {
		$GLOBALS['post']              = new \stdClass();
		$GLOBALS['post']->ID          = 30;
		$GLOBALS['post']->post_parent = 10;

		WP_Mock::userFunction('is_front_page')->andReturn(false);
		WP_Mock::userFunction('is_single')->andReturn(false);
		WP_Mock::userFunction('is_page')->andReturn(true);
		WP_Mock::userFunction('is_archive')->andReturn(false);
		WP_Mock::userFunction('is_search')->andReturn(false);
		WP_Mock::userFunction('is_404')->andReturn(false);
		WP_Mock::userFunction('is_home')->andReturn(false);
		WP_Mock::userFunction('get_home_url')->andReturn('https://example.com/');
		WP_Mock::userFunction('get_post_ancestors')
			->with(30)
			->andReturn([10]);
		WP_Mock::userFunction('get_the_title')
			->andReturnUsing(static function ( $id = null ) {
				return (int) $id === 10 ? '<em>Parent</em>' : 'Child';
			});
		WP_Mock::userFunction('get_permalink')
			->andReturnUsing(static function ( $id = null ) {
				return (int) $id === 10
					? 'https://example.com/parent/'
					: 'https://example.com/child/';
			});
		WP_Mock::userFunction('esc_attr')->andReturnUsing(
			static fn( $text ) => str_replace( [ '<', '>' ], [ '&lt;', '&gt;' ], (string) $text )
		);
		WP_Mock::userFunction('esc_url')->andReturnUsing(static fn( $url ) => $url);
		WP_Mock::userFunction('esc_html')->andReturnUsing(
			static fn( $text ) => str_replace( [ '<', '>' ], [ '&lt;', '&gt;' ], (string) $text )
		);
		WP_Mock::userFunction('apply_filters')->andReturnUsing(
			static function ( $hook, $value ) {
				return $value;
			}
		);

		ob_start();
		Breadcrumbs::render(false, 'crumbs', '&raquo;', 'Home');
		$output = ob_get_clean();

		$this->assertStringContainsString(
			'><a class="crumbs__link crumbs__link--parent parent-10" href="https://example.com/parent/" title="&lt;em&gt;Parent&lt;/em&gt;">&lt;em&gt;Parent&lt;/em&gt;</a>',
			$output
		);
		$this->assertDoesNotMatchRegularExpression( '/<a[^>]*>[^<]*<em>/', $output );
	}
}
