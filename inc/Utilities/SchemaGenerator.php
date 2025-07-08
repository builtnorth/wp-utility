<?php

namespace BuiltNorth\Utility\Utilities;

use BuiltNorth\Utility\Utilities\SchemaGenerator\Detectors\ContentTypeDetector;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Detectors\PatternDetector;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Extractors\ContentExtractor;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\FaqGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\ArticleGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\ProductGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\OrganizationGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\PersonGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\AggregateRatingGenerator;
use BuiltNorth\Utility\Utilities\SchemaGenerator\Generators\ReviewGenerator;

/**
 * Schema Generator
 * 
 * Universal, content-agnostic structured data generator for WordPress sites.
 * Automatically detects content types and patterns to generate JSON-LD schema markup.
 */
class SchemaGenerator
{
	/**
	 * Generate schema from various content sources
	 *
	 * @param mixed $content Content to extract schema from
	 * @param string $type Schema type (faq, article, product, etc.)
	 * @param array $options Generation options
	 * @return string JSON-LD schema markup or empty string
	 */
	public static function render($content = '', $type = 'faq', $options = [])
	{
		if (empty($content)) {
			return '';
		}

		// Auto-detect content type if not specified
		$content_type = $options['content_type'] ?? ContentTypeDetector::detect($content);
		
		// Extract data based on content type
		$data = self::extract_data($content, $type, $content_type, $options);
		
		// Generate schema from extracted data
		return self::generate_schema($data, $type, $options);
	}

	/**
	 * Extract data from content based on content type
	 *
	 * @param mixed $content Content to extract from
	 * @param string $type Schema type
	 * @param string $content_type Content type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_data($content, $type, $content_type, $options = [])
	{
		switch ($content_type) {
			case 'html':
				return self::extract_from_html($content, $type, $options);
			case 'json':
				return self::extract_from_json($content, $type, $options);
			case 'array':
				return self::extract_from_array($content, $type, $options);
			case 'post_meta':
				return self::extract_from_post_meta($content, $type, $options);
			case 'mixed':
				return self::extract_from_mixed($content, $type, $options);
			default:
				return self::extract_from_html($content, $type, $options);
		}
	}

	/**
	 * Extract data from HTML content
	 *
	 * @param string $content HTML content
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_from_html($content, $type, $options = [])
	{
		$extractor = new ContentExtractor($content);
		return $extractor->extract($type, $options);
	}

	/**
	 * Extract data from JSON content
	 *
	 * @param string $json JSON string
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_from_json($json, $type, $options = [])
	{
		$data = json_decode($json, true);
		return json_last_error() === JSON_ERROR_NONE ? $data : [];
	}

	/**
	 * Extract data from array content
	 *
	 * @param array $data Array data
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_from_array($data, $type, $options = [])
	{
		return $data;
	}

	/**
	 * Extract data from WordPress post meta
	 *
	 * @param int $post_id Post ID
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_from_post_meta($post_id, $type, $options = [])
	{
		return [
			'title' => get_the_title($post_id),
			'content' => get_the_content(null, false, $post_id),
			'author' => get_the_author_meta('display_name', get_post_field('post_author', $post_id)),
			'date' => get_the_date('c', $post_id),
			'url' => get_permalink($post_id),
			'meta' => get_post_meta($post_id)
		];
	}

	/**
	 * Extract data from mixed content sources
	 *
	 * @param array $mixed_data Mixed data sources
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	private static function extract_from_mixed($mixed_data, $type, $options = [])
	{
		return $mixed_data;
	}

	/**
	 * Generate schema from extracted data
	 *
	 * @param array $data Extracted data
	 * @param string $type Schema type
	 * @param array $options Generation options
	 * @return string JSON-LD schema markup
	 */
	private static function generate_schema($data, $type, $options = [])
	{
		$generators = [
			'faq' => FaqGenerator::class,
			'article' => ArticleGenerator::class,
			'product' => ProductGenerator::class,
			'organization' => OrganizationGenerator::class,
			'person' => PersonGenerator::class,
			'aggregate_rating' => AggregateRatingGenerator::class,
			'review' => ReviewGenerator::class
		];

		if (isset($generators[$type])) {
			$generator_class = $generators[$type];
			return $generator_class::generate($data, $options);
		}

		return '';
	}

	/**
	 * Output JSON-LD schema script tag
	 *
	 * @param string $schema JSON-LD schema markup
	 * @return void
	 */
	public static function output_schema_script($schema)
	{
		if (!empty($schema)) {
			echo '<script type="application/ld+json">' . $schema . '</script>';
		}
	}

	/**
	 * Detect patterns in content
	 *
	 * @param string $content Content to analyze
	 * @param string $type Schema type
	 * @return array Detected patterns
	 */
	public static function detect_patterns($content, $type)
	{
		switch ($type) {
			case 'faq':
				return PatternDetector::detect_faq_patterns($content);
			case 'article':
				return PatternDetector::detect_article_patterns($content);
			case 'product':
				return PatternDetector::detect_product_patterns($content);
			case 'organization':
				return PatternDetector::detect_organization_patterns($content);
			case 'person':
				return PatternDetector::detect_person_patterns($content);
			default:
				return [];
		}
	}

	/**
	 * Get the best pattern for a schema type
	 *
	 * @param string $schema_type Schema type
	 * @param array $detected_patterns Detected patterns
	 * @return string Best pattern to use
	 */
	public static function get_best_pattern($schema_type, $detected_patterns)
	{
		return PatternDetector::get_best_pattern($schema_type, $detected_patterns);
	}
} 