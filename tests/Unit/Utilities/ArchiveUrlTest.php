<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\ArchiveUrl;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\ArchiveUrl
 *
 * Only the early-return branches are exercised here. The redirect branch
 * calls wp_safe_redirect() followed by a bare `exit`, which cannot be
 * intercepted under WP_Mock/Patchwork without terminating the test process,
 * so it is intentionally left uncovered by this unit test.
 */
class ArchiveUrlTest extends WPMockTestCase {

	public function tearDown(): void {
		unset($GLOBALS['wp_query']);
		parent::tearDown();
	}

	public function test_render_does_nothing_off_a_taxonomy_page(): void {
		WP_Mock::userFunction('is_tax')->andReturn(false);

		ArchiveUrl::render();

		$this->assertConditionsMet();
	}

	public function test_render_does_nothing_when_post_type_is_not_registered(): void {
		$GLOBALS['wp_query'] = new \stdClass();
		$GLOBALS['wp_query']->query_vars = ['post_type' => 'missing_type'];

		$term = new \stdClass();
		$term->taxonomy = 'category';
		$term->slug     = 'news';

		WP_Mock::userFunction('is_tax')->andReturn(true);
		WP_Mock::userFunction('get_queried_object')->andReturn($term);
		WP_Mock::userFunction('get_post_type_object')
			->with('missing_type')
			->andReturn(null);

		ArchiveUrl::render();

		$this->assertConditionsMet();
	}
}
