<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Setup;

use BuiltNorth\WPUtility\Setup\ImageSetup;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Setup\ImageSetup
 *
 * ImageSetup::instance() is a singleton with a private constructor, so
 * initialize() (and its add_filter/add_action calls) only runs once for the
 * life of the process — the first test to touch instance() pays that cost,
 * and every test below shares the same instance. Each test only relies on
 * the instance methods being callable, not on hook-registration ordering.
 */
class ImageSetupTest extends WPMockTestCase {

	private ImageSetup $image_setup;

	public function setUp(): void {
		parent::setUp();

		ImageSetup::reset_registered_widths();

		WP_Mock::userFunction('add_filter')->andReturn(null);
		WP_Mock::userFunction('add_action')->andReturn(null);
		// False so initialize() only defers add_image_sizes() to the
		// after_setup_theme action instead of calling it immediately —
		// singleton construction happens once, on whichever test runs
		// first, and each test only wants to exercise the method it names.
		WP_Mock::userFunction('did_action')->andReturn(false);

		$this->image_setup = ImageSetup::instance();
	}

	public function tearDown(): void {
		ImageSetup::reset_registered_widths();

		parent::tearDown();
	}

	public function test_instance_always_returns_the_same_object(): void {
		$this->assertSame(ImageSetup::instance(), ImageSetup::instance());
	}

	public function test_setup_and_render_both_return_the_singleton(): void {
		$this->assertSame(ImageSetup::instance(), ImageSetup::setup());
		$this->assertSame(ImageSetup::instance(), ImageSetup::render());
	}

	public function test_remove_default_image_sizes_strips_the_default_set(): void {
		WP_Mock::onFilter('wp_utility_remove_default_sizes')
			->with(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048'])
			->reply(['thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048']);

		$result = $this->image_setup->remove_default_image_sizes([
			'thumbnail',
			'medium',
			'wide_xlarge',
		]);

		$this->assertSame(['wide_xlarge'], array_values($result));
	}

	public function test_update_max_srcset_image_width_defaults_to_1800(): void {
		WP_Mock::onFilter('wp_utility_max_srcset_width')
			->with(1800)
			->reply(1800);

		$this->assertSame(1800, $this->image_setup->update_max_srcset_image_width());
	}

	public function test_cap_srcset_ignores_non_array_sources(): void {
		$this->assertFalse($this->image_setup->cap_srcset_to_requested_size(false, [600], 'src', [], 1));
	}

	public function test_cap_srcset_ignores_empty_requested_width(): void {
		$sources = [600 => ['url' => 'a'], 1200 => ['url' => 'b']];

		$this->assertSame(
			$sources,
			$this->image_setup->cap_srcset_to_requested_size($sources, [0], 'src', [], 1)
		);
	}

	public function test_cap_srcset_ignores_a_width_at_or_above_the_max(): void {
		$sources = [600 => ['url' => 'a'], 1800 => ['url' => 'b']];

		WP_Mock::onFilter('wp_utility_max_srcset_width')
			->with(1800)
			->reply(1800);

		$this->assertSame(
			$sources,
			$this->image_setup->cap_srcset_to_requested_size($sources, [1800], 'src', [], 1)
		);
	}

	public function test_cap_srcset_leaves_sources_untouched_for_an_unregistered_width(): void {
		$sources = [600 => ['url' => 'a'], 1200 => ['url' => 'b']];

		WP_Mock::userFunction('wp_get_registered_image_subsizes')->andReturn([]);
		WP_Mock::onFilter('wp_utility_max_srcset_width')
			->with(1800)
			->reply(1800);

		$this->assertSame(
			$sources,
			$this->image_setup->cap_srcset_to_requested_size($sources, [600], 'src', [], 1)
		);
	}

	public function test_cap_srcset_strips_candidates_wider_than_a_registered_requested_width(): void {
		$sources = [
			400  => ['url' => 'a'],
			600  => ['url' => 'b'],
			1200 => ['url' => 'c'],
		];

		WP_Mock::userFunction('wp_get_registered_image_subsizes')->andReturn([
			['width' => 400],
			['width' => 600],
			['width' => 1200],
		]);
		WP_Mock::onFilter('wp_utility_max_srcset_width')
			->with(1800)
			->reply(1800);

		$result = $this->image_setup->cap_srcset_to_requested_size($sources, [600], 'src', [], 1);

		$this->assertSame([400 => ['url' => 'a'], 600 => ['url' => 'b']], $result);
	}

	public function test_add_image_sizes_registers_the_default_five_sizes(): void {
		WP_Mock::userFunction('current_theme_supports')
			->with('post-thumbnails')
			->andReturn(false);
		WP_Mock::userFunction('add_theme_support')
			->with('post-thumbnails')
			->andReturn(null);
		WP_Mock::onFilter('wp_utility_image_sizes')
			->with([
				'wide_xlarge'  => [1800, 99999, false],
				'wide_large'   => [1200, 99999, false],
				'wide_medium'  => [800,  99999, false],
				'wide_small'   => [600,  99999, false],
				'wide_xsmall'  => [400,  99999, false],
			])
			->reply([
				'wide_xlarge'  => [1800, 99999, false],
				'wide_large'   => [1200, 99999, false],
				'wide_medium'  => [800,  99999, false],
				'wide_small'   => [600,  99999, false],
				'wide_xsmall'  => [400,  99999, false],
			]);

		WP_Mock::userFunction('add_image_size')
			->times(5)
			->andReturn(null);

		$this->image_setup->add_image_sizes();

		$this->assertConditionsMet();
	}

	public function test_add_image_sizes_skips_adding_theme_support_when_already_supported(): void {
		WP_Mock::userFunction('current_theme_supports')
			->with('post-thumbnails')
			->andReturn(true);
		WP_Mock::onFilter('wp_utility_image_sizes')
			->withAnyArgs()
			->reply([]);

		$this->image_setup->add_image_sizes();

		$this->assertConditionsMet();
	}

	public function test_image_size_names_merges_default_names_onto_existing(): void {
		$default_names = [
			'wide_xlarge' => 'Extra Large',
			'wide_large'  => 'Large',
			'wide_medium' => 'Medium',
			'wide_small'  => 'Small',
			'wide_xsmall' => 'Extra Small',
		];

		WP_Mock::userFunction('__')->andReturnUsing(static fn($text) => $text);
		WP_Mock::onFilter('wp_utility_image_size_names')
			->with($default_names)
			->reply($default_names);

		$result = $this->image_setup->image_size_names(['thumbnail' => 'Thumbnail']);

		$this->assertSame([
			'thumbnail'   => 'Thumbnail',
			'wide_xlarge' => 'Extra Large',
			'wide_large'  => 'Large',
			'wide_medium' => 'Medium',
			'wide_small'  => 'Small',
			'wide_xsmall' => 'Extra Small',
		], $result);
	}
}
