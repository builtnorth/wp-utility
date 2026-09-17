<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Utilities;

use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use BuiltNorth\WPUtility\Utilities\LazyLoadFirstBlock;
use WP_Mock;
use WP_Mock\InvokedFilterValue;

/**
 * @covers \BuiltNorth\WPUtility\Utilities\LazyLoadFirstBlock
 */
class LazyLoadFirstBlockTest extends WPMockTestCase {

	private function make_block(?string $parent_block = null): object {
		$block = new \stdClass();
		$block->block_type = new \stdClass();
		$block->block_type->parent = $parent_block !== null ? [$parent_block] : [];

		return $block;
	}

	public function test_defaults_to_lazy_when_block_has_no_parent(): void {
		$block = $this->make_block();

		WP_Mock::onFilter('wp_utility_lazy_load_non_lazy_parents')
			->with([], $block)
			->reply([]);

		$this->assertTrue(LazyLoadFirstBlock::render($block));
	}

	public function test_disables_lazy_loading_for_a_listed_parent(): void {
		$block = $this->make_block('core/query');

		WP_Mock::onFilter('wp_utility_lazy_load_non_lazy_parents')
			->with(['core/query'], $block)
			->reply(new InvokedFilterValue(static fn($parents) => $parents));

		$this->assertFalse(LazyLoadFirstBlock::render($block, ['core/query']));
	}

	public function test_stays_lazy_for_a_parent_not_in_the_list(): void {
		$block = $this->make_block('core/columns');

		WP_Mock::onFilter('wp_utility_lazy_load_non_lazy_parents')
			->with(['core/query'], $block)
			->reply(new InvokedFilterValue(static fn($parents) => $parents));

		$this->assertTrue(LazyLoadFirstBlock::render($block, ['core/query']));
	}

	public function test_custom_default_lazy_state_is_respected(): void {
		$block = $this->make_block();

		WP_Mock::onFilter('wp_utility_lazy_load_non_lazy_parents')
			->with([], $block)
			->reply([]);

		$this->assertFalse(LazyLoadFirstBlock::render($block, [], false));
	}

	public function test_render_with_callback_defers_to_callable_for_listed_parent(): void {
		$block = $this->make_block('core/query');

		$result = LazyLoadFirstBlock::renderWithCallback(
			$block,
			static fn($parent_block) => $parent_block !== 'core/query'
		);

		$this->assertFalse($result);
	}

	public function test_render_with_callback_keeps_default_when_no_callback_given(): void {
		$block = $this->make_block('core/query');

		$this->assertTrue(LazyLoadFirstBlock::renderWithCallback($block));
	}

	public function test_render_with_callback_keeps_default_when_block_has_no_parent(): void {
		$block = $this->make_block();

		$result = LazyLoadFirstBlock::renderWithCallback(
			$block,
			static fn() => false
		);

		$this->assertTrue($result);
	}
}
