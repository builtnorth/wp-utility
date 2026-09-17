<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\AccessibleCard;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;

/**
 * @covers \BuiltNorth\WPUtility\Components\AccessibleCard
 */
class AccessibleCardTest extends WPMockTestCase {

	public function test_renders_a_link_with_default_screen_reader_text(): void {
		$this->expectOutputString(
			'<a class="accessible-card-link" href="https://example.com/post/">' .
			'<span class="screen-reader-only">Read more about ...</span></a>'
		);

		AccessibleCard::render('https://example.com/post/');
	}

	public function test_target_is_added_as_an_attribute(): void {
		$this->expectOutputString(
			'<a class="accessible-card-link" href="https://example.com/post/"target="_blank">' .
			'<span class="screen-reader-only">Read more about ...</span></a>'
		);

		AccessibleCard::render('https://example.com/post/', '_blank');
	}

	public function test_class_is_prefixed_onto_the_link_class(): void {
		$this->expectOutputString(
			'<a class="card__accessible-card-link accessible-card-link" href="https://example.com/post/">' .
			'<span class="screen-reader-only">Read more about ...</span></a>'
		);

		AccessibleCard::render('https://example.com/post/', null, 'Read more about ...', 'card');
	}

	public function test_custom_screen_reader_text_is_used(): void {
		$this->expectOutputString(
			'<a class="accessible-card-link" href="https://example.com/post/">' .
			'<span class="screen-reader-only">View My Post</span></a>'
		);

		AccessibleCard::render('https://example.com/post/', null, 'View My Post');
	}
}
