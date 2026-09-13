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

		WP_Mock::userFunction('home_url')->andReturn('https://example.com/');
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

		unset($GLOBALS['post']);
	}
}
