<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\PostTypeLandingUrl;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;
use WP_Post;
use WP_Post_Type;

/**
 * @covers \BuiltNorth\WPUtility\Components\PostTypeLandingUrl
 */
class PostTypeLandingUrlTest extends WPMockTestCase {

	public function test_page_and_attachment_and_empty_post_types_short_circuit(): void {
		$this->assertSame('', PostTypeLandingUrl::resolve('page'));
		$this->assertSame('', PostTypeLandingUrl::resolve('attachment'));
		$this->assertSame('', PostTypeLandingUrl::resolve(''));
	}

	public function test_unregistered_post_type_returns_empty_string(): void {
		WP_Mock::userFunction('get_post_type_object')
			->with('missing')
			->andReturn(null);

		$this->assertSame('', PostTypeLandingUrl::resolve('missing'));
	}

	public function test_non_public_post_type_returns_empty_string(): void {
		$post_type = new WP_Post_Type('internal');
		$post_type->public = false;

		WP_Mock::userFunction('get_post_type_object')
			->with('internal')
			->andReturn($post_type);

		$this->assertSame('', PostTypeLandingUrl::resolve('internal'));
	}

	public function test_post_type_with_archive_uses_the_archive_link(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->public = true;
		$post_type->has_archive = true;

		WP_Mock::userFunction('get_post_type_object')
			->with('services')
			->andReturn($post_type);
		WP_Mock::userFunction('get_post_type_archive_link')
			->with('services')
			->andReturn('https://example.com/services/');

		$this->assertSame('https://example.com/services/', PostTypeLandingUrl::resolve('services'));
	}

	public function test_no_archive_but_a_matching_page_uses_the_page_permalink(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->public = true;
		$post_type->has_archive = false;
		$post_type->rewrite = ['slug' => 'services'];

		$page = new WP_Post(['ID' => 9]);

		WP_Mock::userFunction('get_post_type_object')
			->with('services')
			->andReturn($post_type);
		WP_Mock::userFunction('get_page_by_path')
			->with('services', OBJECT, 'page')
			->andReturn($page);
		WP_Mock::userFunction('get_permalink')
			->with($page)
			->andReturn('https://example.com/services/');

		$this->assertSame('https://example.com/services/', PostTypeLandingUrl::resolve('services'));
	}

	public function test_no_archive_and_no_page_falls_back_to_the_rewrite_slug_url(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->public = true;
		$post_type->has_archive = false;
		$post_type->rewrite = ['slug' => 'our-services'];

		WP_Mock::userFunction('get_post_type_object')
			->with('services')
			->andReturn($post_type);
		WP_Mock::userFunction('get_page_by_path')
			->with('our-services', OBJECT, 'page')
			->andReturn(null);
		WP_Mock::userFunction('home_url')
			->with('our-services/')
			->andReturn('https://example.com/our-services/');
		WP_Mock::userFunction('user_trailingslashit')
			->with('our-services')
			->andReturn('our-services/');

		$this->assertSame('https://example.com/our-services/', PostTypeLandingUrl::resolve('services'));
	}

	public function test_empty_rewrite_slug_returns_empty_string(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->public = true;
		$post_type->has_archive = false;
		$post_type->rewrite = false;
		$post_type->name = '';

		WP_Mock::userFunction('get_post_type_object')
			->with('services')
			->andReturn($post_type);

		$this->assertSame('', PostTypeLandingUrl::resolve('services'));
	}

	public function test_get_rewrite_slug_falls_back_to_post_type_name_when_rewrite_has_no_slug(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->rewrite = ['with_front' => false];

		$this->assertSame('services', PostTypeLandingUrl::get_rewrite_slug($post_type));
	}

	public function test_get_rewrite_slug_falls_back_to_post_type_name_when_rewrite_is_false(): void {
		$post_type = new WP_Post_Type('services');
		$post_type->rewrite = false;

		$this->assertSame('services', PostTypeLandingUrl::get_rewrite_slug($post_type));
	}
}
