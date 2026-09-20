<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\Button;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Components\Button
 */
class ButtonTest extends WPMockTestCase {

	private const DEFAULT_GENERIC_LABELS = [
		'learn more',
		'read more',
		'view more',
		'find out more',
		'see more',
		'continue reading',
		'view details',
		'read more about',
	];

	public function setUp(): void {
		parent::setUp();

		WP_Mock::onFilter('wp_utility_button_block_prefix')
			->with('wp-block-polaris-button')
			->reply('wp-block-polaris-button');

		WP_Mock::userFunction('sanitize_html_class')->andReturnUsing(static fn( $text ) => $text);
		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn( $text ) => $text);
		WP_Mock::userFunction('esc_url')->andReturnUsing(static fn( $url ) => $url);
		WP_Mock::userFunction('esc_html')->andReturnUsing(static fn( $text ) => $text);
	}

	/**
	 * WP_Mock's onFilter()->with() matches on the literal value passed in
	 * (via spl_object_hash for objects, exact value for scalars/arrays) —
	 * there is no Mockery-matcher support here, so every registration below
	 * must predict the exact value apply_filters() will be called with.
	 */
	private function mock_screen_reader_filter_passthrough(?string $result, array $args): void {
		WP_Mock::onFilter('wp_utility_button_screen_reader_text')
			->with($result, $args)
			->reply($result);
	}

	private function mock_generic_labels_passthrough(): void {
		WP_Mock::onFilter('wp_utility_button_generic_link_labels')
			->with(self::DEFAULT_GENERIC_LABELS)
			->reply(self::DEFAULT_GENERIC_LABELS);
	}

	public function test_renders_an_anchor_by_default(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'<span class="wp-block-polaris-button__text">Button Text</span></a>'
		);

		Button::render();
	}

	public function test_button_type_does_not_wrap_text_in_a_span(): void {
		$this->expectOutputString(
			'<button class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'Click Me</button>'
		);

		Button::render('button', null, null, 'default', 'default', 'fill', 'Click Me');
	}

	public function test_link_is_rendered_as_href(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill"' .
			' href="https://example.com/"><span class="wp-block-polaris-button__text">Button Text</span></a>'
		);

		Button::render('a', null, null, 'default', 'default', 'fill', 'Button Text', 'https://example.com/');
	}

	public function test_screen_reader_text_is_escaped_and_appended(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'<span class="wp-block-polaris-button__text">Button Text</span>' .
			'<span class="screen-reader-only">opens in new window</span></a>'
		);

		Button::render('a', null, null, 'default', 'default', 'fill', 'Button Text', null, null, 'opens in new window');
	}

	public function test_empty_text_renders_nothing(): void {
		$this->expectOutputString('');

		Button::render('a', null, null, 'default', 'default', 'fill', '');
	}

	public function test_icon_left_is_placed_before_the_text_by_default(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'<span class="wp-block-polaris-button__icon wp-block-polaris-button__icon--left"><svg></svg></span>' .
			'<span class="wp-block-polaris-button__text">Button Text</span></a>'
		);

		Button::render('a', null, null, 'default', 'default', 'fill', 'Button Text', null, null, null, null, '<svg></svg>');
	}

	public function test_icon_right_is_placed_after_the_text(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'<span class="wp-block-polaris-button__text">Button Text</span>' .
			'<span class="wp-block-polaris-button__icon wp-block-polaris-button__icon--right"><svg></svg></span></a>'
		);

		Button::render('a', null, null, 'default', 'default', 'fill', 'Button Text', null, null, null, null, '<svg></svg>', 'right');
	}

	public function test_disallowed_button_type_falls_back_to_anchor(): void {
		$this->expectOutputString(
			'<a class="wp-block-polaris-button is-style-default is-size-default is-appearance-fill">' .
			'<span class="wp-block-polaris-button__text">Button Text</span></a>'
		);

		Button::render('script', null, null, 'default', 'default', 'fill', 'Button Text');
	}

	// -- resolve_screen_reader_text() ------------------------------------

	public function test_resolve_screen_reader_text_explicit_override_wins(): void {
		$args = ['explicit' => 'Custom text'];
		$this->mock_screen_reader_filter_passthrough(' Custom text', $args);

		$this->assertSame(' Custom text', Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_returns_null_when_nothing_to_say(): void {
		$this->mock_screen_reader_filter_passthrough(null, []);

		$this->assertNull(Button::resolve_screen_reader_text([]));
	}

	public function test_resolve_screen_reader_text_appends_new_window_notice(): void {
		$args = ['opens_in_new_tab' => true];
		WP_Mock::userFunction('__')
			->with('(opens in a new window)')
			->andReturn('(opens in a new window)');
		$this->mock_screen_reader_filter_passthrough(' (opens in a new window)', $args);

		$this->assertSame(
			' (opens in a new window)',
			Button::resolve_screen_reader_text($args)
		);
	}

	public function test_resolve_screen_reader_text_uses_text_domain_when_given(): void {
		$args = [
			'opens_in_new_tab' => true,
			'text_domain'      => 'my-domain',
		];
		WP_Mock::userFunction('__')
			->with('(opens in a new window)', 'my-domain')
			->andReturn('(opens in a new window)');
		$this->mock_screen_reader_filter_passthrough(' (opens in a new window)', $args);

		$this->assertSame(' (opens in a new window)', Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_adds_post_title_for_permalink(): void {
		$args = [
			'post_id'      => 9,
			'is_permalink' => true,
		];
		WP_Mock::userFunction('get_the_title')
			->with(9)
			->andReturn('My Post');
		$this->mock_screen_reader_filter_passthrough(' My Post', $args);

		$this->assertSame(' My Post', Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_adds_post_title_when_link_matches_permalink(): void {
		$args = [
			'post_id' => 9,
			'link'    => 'https://example.com/post/',
		];
		WP_Mock::userFunction('get_the_title')
			->with(9)
			->andReturn('My Post');
		WP_Mock::userFunction('get_permalink')
			->with(9)
			->andReturn('https://example.com/post/');
		WP_Mock::userFunction('untrailingslashit')
			->andReturnUsing(static fn($url) => rtrim($url, '/'));
		$this->mock_screen_reader_filter_passthrough(' My Post', $args);

		$this->assertSame(' My Post', Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_adds_post_title_for_generic_label(): void {
		$args = [
			'post_id' => 9,
			'link'    => 'https://example.com/elsewhere/',
			'text'    => 'Learn More',
		];
		$this->mock_generic_labels_passthrough();
		WP_Mock::userFunction('get_the_title')
			->with(9)
			->andReturn('My Post');
		WP_Mock::userFunction('get_permalink')
			->with(9)
			->andReturn('https://example.com/different/');
		WP_Mock::userFunction('untrailingslashit')
			->andReturnUsing(static fn($url) => rtrim($url, '/'));
		WP_Mock::userFunction('wp_strip_all_tags')
			->andReturnUsing(static fn($text) => $text);
		$this->mock_screen_reader_filter_passthrough(' My Post', $args);

		$this->assertSame(' My Post', Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_skips_post_title_for_a_specific_unrelated_link(): void {
		$args = [
			'post_id' => 9,
			'link'    => 'https://example.com/elsewhere/',
			'text'    => 'Download PDF',
		];
		$this->mock_generic_labels_passthrough();
		WP_Mock::userFunction('get_the_title')
			->with(9)
			->andReturn('My Post');
		WP_Mock::userFunction('get_permalink')
			->with(9)
			->andReturn('https://example.com/different/');
		WP_Mock::userFunction('untrailingslashit')
			->andReturnUsing(static fn($url) => rtrim($url, '/'));
		WP_Mock::userFunction('wp_strip_all_tags')
			->andReturnUsing(static fn($text) => $text);
		$this->mock_screen_reader_filter_passthrough(null, $args);

		$this->assertNull(Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_skips_empty_post_title(): void {
		$args = [
			'post_id'      => 9,
			'is_permalink' => true,
		];
		WP_Mock::userFunction('get_the_title')
			->with(9)
			->andReturn('');
		$this->mock_screen_reader_filter_passthrough(null, $args);

		$this->assertNull(Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_ignores_non_positive_post_id(): void {
		$args = [
			'post_id'      => 0,
			'is_permalink' => true,
		];
		$this->mock_screen_reader_filter_passthrough(null, $args);

		$this->assertNull(Button::resolve_screen_reader_text($args));
	}

	public function test_resolve_screen_reader_text_filter_can_override_result(): void {
		WP_Mock::onFilter('wp_utility_button_screen_reader_text')
			->with(null, [])
			->reply(' Filtered');

		$this->assertSame(' Filtered', Button::resolve_screen_reader_text([]));
	}

	// -- is_generic_link_label() / get_generic_link_labels() -------------

	public function test_is_generic_link_label_matches_case_insensitively(): void {
		$this->mock_generic_labels_passthrough();
		WP_Mock::userFunction('wp_strip_all_tags')
			->andReturnUsing(static fn($text) => $text);

		$this->assertTrue(Button::is_generic_link_label('  Learn More  '));
	}

	public function test_is_generic_link_label_rejects_a_specific_label(): void {
		$this->mock_generic_labels_passthrough();
		WP_Mock::userFunction('wp_strip_all_tags')
			->andReturnUsing(static fn($text) => $text);

		$this->assertFalse(Button::is_generic_link_label('Download the 2024 Report'));
	}

	public function test_is_generic_link_label_rejects_empty_text(): void {
		WP_Mock::userFunction('wp_strip_all_tags')
			->andReturnUsing(static fn($text) => $text);

		$this->assertFalse(Button::is_generic_link_label('   '));
	}

	public function test_get_generic_link_labels_is_filterable(): void {
		WP_Mock::onFilter('wp_utility_button_generic_link_labels')
			->with(self::DEFAULT_GENERIC_LABELS)
			->reply(['custom label']);

		$this->assertSame(['custom label'], Button::get_generic_link_labels());
	}
}
