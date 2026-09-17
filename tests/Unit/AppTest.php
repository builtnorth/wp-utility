<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit;

use BuiltNorth\WPUtility\App;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * @covers \BuiltNorth\WPUtility\App
 */
class AppTest extends WPMockTestCase {

	public function test_instance_always_returns_the_same_object(): void {
		$this->assertSame(App::instance(), App::instance());
	}

	/**
	 * boot() loads Utility/Component/Helper (all empty-bodied classes with
	 * no init() method) and then, since Blocks\MetaGatedBlockSupport really
	 * is autoloadable in this test run, calls its real init() — which
	 * registers two block-support filters. Asserting those registrations
	 * happen is the only externally observable side effect boot() has.
	 */
	public function test_boot_registers_the_meta_gated_block_support_filters(): void {
		$this->expect_filter_added(
			'register_block_type_args',
			['BuiltNorth\WPUtility\Blocks\MetaGatedBlockSupport', 'register_attributes'],
			10,
			2
		);
		$this->expect_filter_added(
			'render_block',
			['BuiltNorth\WPUtility\Blocks\MetaGatedBlockSupport', 'filter_render_block'],
			10,
			3
		);

		App::instance()->boot();

		$this->assertConditionsMet();
	}
}
