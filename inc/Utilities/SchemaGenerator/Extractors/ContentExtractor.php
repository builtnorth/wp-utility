<?php

namespace BuiltNorth\Utility\Utilities\SchemaGenerator\Extractors;

/**
 * Content Extractor
 * 
 * Handles extraction of structured data from HTML content
 */
class ContentExtractor
{
	private $content;
	private $dom;
	private $xpath;

	/**
	 * Constructor
	 *
	 * @param string $content HTML content
	 */
	public function __construct($content)
	{
		$this->content = $content;
		$this->parse_html();
	}

	/**
	 * Parse HTML content
	 */
	private function parse_html()
	{
		$this->dom = new \DOMDocument();
		libxml_use_internal_errors(true);
		$this->dom->loadHTML('<?xml encoding="UTF-8">' . $this->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		libxml_clear_errors();
		$this->xpath = new \DOMXPath($this->dom);
	}

	/**
	 * Extract data based on schema type
	 *
	 * @param string $type Schema type
	 * @param array $options Extraction options
	 * @return array Extracted data
	 */
	public function extract($type, $options = [])
	{
		switch ($type) {
			case 'faq':
				return $this->extract_faq_data($options);
			case 'article':
				return $this->extract_article_data($options);
			case 'product':
				return $this->extract_product_data($options);
			case 'organization':
				return $this->extract_organization_data($options);
			case 'person':
				return $this->extract_person_data($options);
			default:
				return [];
		}
	}

	/**
	 * Extract FAQ data with pattern recognition
	 *
	 * @param array $options Extraction options
	 * @return array FAQ data
	 */
	private function extract_faq_data($options = [])
	{
		$faq_items = [];
		
		// Try multiple patterns to find FAQ content
		$patterns = [
			// Accordion pattern
			'accordion' => [
				'container' => '//div[contains(@class, "accordion-item") or contains(@class, "faq-item") or contains(@class, "wp-block-polaris-accordion-item")]',
				'question' => './/button//span | .//h3 | .//h4 | .//dt | .//span[contains(@class, "title")]',
				'answer' => './/div[contains(@class, "content")] | .//dd | .//p | .//div[contains(@class, "accordion-item__content")]'
			],
			// List pattern
			'list' => [
				'container' => '//li[contains(@class, "faq")] | //dt',
				'question' => './/h3 | .//h4 | .',
				'answer' => './/dd | .//p | .//div'
			],
			// Generic pattern
			'generic' => [
				'container' => '//div[contains(@class, "faq")] | //section[contains(@class, "faq")]',
				'question' => './/h3 | .//h4 | .//h5',
				'answer' => './/p | .//div'
			]
		];

		foreach ($patterns as $pattern_name => $pattern) {
			$containers = $this->xpath->query($pattern['container']);
			
			if ($containers->length > 0) {
				foreach ($containers as $container) {
					$question_element = $this->xpath->query($pattern['question'], $container)->item(0);
					$answer_element = $this->xpath->query($pattern['answer'], $container)->item(0);
					
					if ($question_element && $answer_element) {
						$question = trim($question_element->textContent);
						$answer = trim($answer_element->textContent);
						
						if (!empty($question) && !empty($answer)) {
							$faq_items[] = [
								'@type' => 'Question',
								'name' => wp_strip_all_tags($question),
								'acceptedAnswer' => [
									'@type' => 'Answer',
									'text' => wp_strip_all_tags($answer)
								]
							];
						}
					}
				}
				
				// If we found items with this pattern, break
				if (!empty($faq_items)) {
					break;
				}
			}
		}

		return ['items' => $faq_items];
	}

	/**
	 * Extract article data
	 *
	 * @param array $options Extraction options
	 * @return array Article data
	 */
	private function extract_article_data($options = [])
	{
		$data = [];
		
		// Extract title
		$title_selectors = ['//h1', '//h2', '//title'];
		foreach ($title_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['title'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract content
		$content_selectors = ['//article', '//main', '//div[contains(@class, "content")]', '//div[contains(@class, "entry-content")]'];
		foreach ($content_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['content'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract author
		$author_selectors = ['//span[contains(@class, "author")]', '//span[contains(@class, "byline")]'];
		foreach ($author_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['author'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract date
		$date_selectors = ['//time[contains(@class, "published")]', '//time', '//span[contains(@class, "date")]'];
		foreach ($date_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['date'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		return $data;
	}

	/**
	 * Extract product data
	 *
	 * @param array $options Extraction options
	 * @return array Product data
	 */
	private function extract_product_data($options = [])
	{
		$data = [];
		
		// Extract product name
		$name_selectors = ['//h1[contains(@class, "product")]', '//h1', '//h2'];
		foreach ($name_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['name'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract price
		$price_selectors = ['//span[contains(@class, "price")]', '//span[contains(text(), "$")]'];
		foreach ($price_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['price'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract description
		$description_selectors = ['//div[contains(@class, "description")]', '//p[contains(@class, "description")]'];
		foreach ($description_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['description'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		return $data;
	}

	/**
	 * Extract organization data
	 *
	 * @param array $options Extraction options
	 * @return array Organization data
	 */
	private function extract_organization_data($options = [])
	{
		$data = [];
		
		// Extract organization name
		$name_selectors = ['//h1', '//h2', '//span[contains(@class, "company")]'];
		foreach ($name_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['name'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract logo
		$logo_selectors = ['//img[contains(@class, "logo")]', '//img[contains(@alt, "logo")]'];
		foreach ($logo_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['logo'] = $element->getAttribute('src');
				break;
			}
		}
		
		return $data;
	}

	/**
	 * Extract person data
	 *
	 * @param array $options Extraction options
	 * @return array Person data
	 */
	private function extract_person_data($options = [])
	{
		$data = [];
		
		// Extract person name
		$name_selectors = ['//h1', '//h2', '//span[contains(@class, "name")]'];
		foreach ($name_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['name'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		// Extract job title
		$job_selectors = ['//span[contains(@class, "job")]', '//span[contains(@class, "title")]'];
		foreach ($job_selectors as $selector) {
			$element = $this->xpath->query($selector)->item(0);
			if ($element) {
				$data['job_title'] = wp_strip_all_tags(trim($element->textContent));
				break;
			}
		}
		
		return $data;
	}
} 