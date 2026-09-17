<?php

declare(strict_types=1);

namespace BuiltNorth\WPUtility\Tests\Unit\Components;

use BuiltNorth\WPUtility\Components\Image;
use BuiltNorth\WPUtility\Tests\WPMockTestCase;
use WP_Mock;

/**
 * @covers \BuiltNorth\WPUtility\Components\Image::render
 *
 * WP_Mock's onFilter()->with() matches on the literal value passed in, with
 * no Mockery-matcher support, so the wp_content_img_tag registration below
 * predicts the exact <img> markup Image::render() builds before filtering.
 *
 * Rather than hand-type render()'s whitespace-sensitive heredoc (a stray
 * missing trailing space is invisible in a diff but fails the exact-string
 * match), build_img_tag() mirrors its string construction line-for-line so
 * the expected value is generated the same way the source generates it.
 */
class ImageRenderTest extends WPMockTestCase {

	public function setUp(): void {
		parent::setUp();

		WP_Mock::userFunction('esc_attr')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('esc_html')->andReturnUsing(static fn($text) => $text);
		WP_Mock::userFunction('esc_url')->andReturnUsing(static fn($text) => $text);
	}

	private function mock_attachment(
		int $id,
		string $src = 'https://example.com/image.jpg',
		string $srcset = 'https://example.com/image.jpg 800w',
		string $alt = '',
		string $caption = '',
		array $image_src = ['https://example.com/image.jpg', 800, 600],
		string $mime_type = 'image/jpeg'
	): void {
		WP_Mock::userFunction('wp_get_attachment_image_url')
			->with($id, \Mockery::any())
			->andReturn($src);
		WP_Mock::userFunction('wp_get_attachment_image_srcset')
			->with($id, \Mockery::any())
			->andReturn($srcset);
		WP_Mock::userFunction('get_post_meta')
			->with($id, '_wp_attachment_image_alt', true)
			->andReturn($alt);
		WP_Mock::userFunction('wp_get_attachment_caption')
			->with($id)
			->andReturn($caption);
		WP_Mock::userFunction('wp_get_attachment_image_src')
			->with($id, \Mockery::any())
			->andReturn($image_src);
		WP_Mock::userFunction('get_post_mime_type')
			->with($id)
			->andReturn($mime_type);
	}

	private function mock_passthrough_filter(string $img_tag, int $id): void {
		WP_Mock::onFilter('wp_content_img_tag')
			->with($img_tag, 'wp_utility_image', $id)
			->reply($img_tag);
	}

	/**
	 * Mirrors Image::render()'s <img> string construction exactly.
	 */
	private function build_img_tag(
		string $class,
		string $additional_classes,
		string $final_alt,
		string $src,
		string $srcset,
		string $sizes_attr,
		string $width,
		string $height,
		bool $lazy,
		string $style_attr = ''
	): string {
		$lazy_attr = ($lazy ? 'loading=lazy decoding=async' : 'loading=eager decoding=sync fetchpriority="high"') . ' ';

		return "<img
			" . $lazy_attr . "
			class='" . $class . '__img ' . $additional_classes . "'
			alt='" . $final_alt . "'
			src='" . $src . "'
			srcset='" . $srcset . "'
			sizes='" . $sizes_attr . "'
			width='" . $width . "'
			height='" . $height . "'
			$style_attr
		/>";
	}

	private function build_figure(string $class, string $img_tag, string $caption_html = '', string $wrap_class = 'standard'): string {
		$figure_class = trim($class . '__figure ' . $wrap_class);

		return "<figure class='" . $figure_class . "'>
				" . $img_tag . "
				" . $caption_html . ' ' . "
			</figure>";
	}

	public function test_empty_id_renders_and_echoes_nothing(): void {
		$this->expectOutputString('');

		$this->assertSame('', Image::render(null));
	}

	public function test_lazy_image_wrapped_in_a_figure_by_default(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1);
	}

	public function test_eager_image_uses_eager_loading_attributes_and_no_auto_sizes_prefix(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'(max-width: 1200px) 100vw, 1200px',
			'800', '600', false
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, null, null, false);
	}

	public function test_include_figure_false_echoes_only_the_img_tag(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($img_tag);

		Image::render(1, null, null, null, null, true, 'standard', false);
	}

	public function test_alt_parameter_wins_over_custom_alt_and_attachment_alt(): void {
		$this->mock_attachment(1, alt: 'attachment alt');

		$img_tag = $this->build_img_tag(
			'image', '', 'explicit alt',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, 'custom alt', null, true, 'standard', true, 'full', '1200px', null, '', 'explicit alt');
	}

	public function test_custom_alt_used_when_no_explicit_alt_given(): void {
		$this->mock_attachment(1, alt: 'attachment alt');

		$img_tag = $this->build_img_tag(
			'image', '', 'custom alt',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, 'custom alt');
	}

	public function test_svg_without_dimensions_omits_width_and_height(): void {
		$this->mock_attachment(1, image_src: ['https://example.com/image.svg', 0, 0], mime_type: 'image/svg+xml');

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'', '', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1);
	}

	public function test_explicit_sizes_overrides_the_max_width_derived_default(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, 50vw',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, null, null, true, 'standard', true, 'full', '1200px', null, '', '', '50vw');
	}

	public function test_string_style_is_added_as_an_inline_style_attribute(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true,
			" style='color: red'"
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, null, null, true, 'standard', true, 'full', '1200px', 'color: red');
	}

	public function test_array_style_is_joined_and_empty_entries_filtered(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true,
			" style='color: red; height: 10px'"
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, null, null, true, 'standard', true, 'full', '1200px', ['color: red', '', 'height: 10px']);
	}

	public function test_caption_is_rendered_when_show_caption_is_true(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString(
			$this->build_figure('image', $img_tag, '<figcaption class="image__caption">A caption</figcaption>')
		);

		Image::render(1, null, null, null, true, true, 'standard', true, 'full', '1200px', null, 'A caption');
	}

	public function test_caption_is_not_rendered_when_show_caption_is_false(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, null, null, false, true, 'standard', true, 'full', '1200px', null, 'A caption');
	}

	public function test_custom_class_is_used_for_class_and_caption_prefixes(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'card', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('card', $img_tag));

		Image::render(1, 'card');
	}

	public function test_additional_classes_are_appended_to_the_img_class(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', 'extra classes', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag));

		Image::render(1, null, 'extra classes');
	}

	public function test_wrap_class_is_appended_to_the_figure_class(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag, '', 'cover'));

		Image::render(1, null, null, null, null, true, 'cover');
	}

	public function test_empty_wrap_class_does_not_leave_a_trailing_space(): void {
		$this->mock_attachment(1);

		$img_tag = $this->build_img_tag(
			'image', '', '',
			'https://example.com/image.jpg', 'https://example.com/image.jpg 800w',
			'auto, (max-width: 1200px) 100vw, 1200px',
			'800', '600', true
		);
		$this->mock_passthrough_filter($img_tag, 1);

		$this->expectOutputString($this->build_figure('image', $img_tag, '', ''));

		Image::render(1, null, null, null, null, true, '');
	}
}
