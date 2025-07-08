# SchemaGenerator Utility

A universal, content-agnostic structured data generator for WordPress sites. Automatically detects content types and patterns to generate JSON-LD schema markup for SEO optimization.

## Overview

The SchemaGenerator utility provides a flexible, pattern-based approach to generating structured data from various content sources. It works across different WordPress setups, themes, and content types without requiring specific HTML structures.

## Features

- **Content-Agnostic**: Works with HTML, JSON, arrays, post meta, and mixed data
- **Auto-Detection**: Automatically detects content types and patterns
- **Pattern Recognition**: Finds FAQ, article, and product patterns in any HTML structure
- **Multiple Schema Types**: Supports FAQ, Article, Product, Organization, and Person schemas
- **Universal**: Works with Gutenberg blocks, traditional themes, and custom HTML
- **Extensible**: Easy to add new schema types and extraction patterns

## Architecture

```
SchemaGenerator/
├── SchemaGenerator.php          # Main orchestrator class
├── Extractors/                 # Content extraction logic
│   ├── ContentExtractor.php    # HTML content extraction
│   ├── JsonExtractor.php       # JSON data extraction
│   ├── PostMetaExtractor.php   # WordPress post extraction
│   └── ArrayExtractor.php      # Array data extraction
├── Generators/                 # Schema generation logic
│   ├── FaqGenerator.php        # FAQ schema generation
│   ├── ArticleGenerator.php    # Article schema generation
│   ├── ProductGenerator.php    # Product schema generation
│   └── BaseGenerator.php       # Abstract base class
├── Detectors/                  # Auto-detection logic
│   ├── ContentTypeDetector.php # Auto-detect content type
│   └── PatternDetector.php     # Auto-detect patterns
└── README.md                   # This documentation
```

## Usage

### Basic Usage

```php
use BuiltNorth\Utility\Utility;

// Generate FAQ schema from HTML content
$schema = Utility::schema_generator($html_content, 'faq');

// Generate article schema from post ID
$schema = Utility::schema_generator($post_id, 'article');

// Generate product schema from JSON data
$schema = Utility::schema_generator($json_data, 'product');
```

### Output Schema

```php
// Output JSON-LD script tag
if (!empty($schema)) {
    echo '<script type="application/ld+json">' . $schema . '</script>';
}

// Or use the utility method
SchemaGenerator::output_schema_script($schema);
```

### Content Types

#### HTML Content

```php
$html = '<div class="faq-item"><h3>Question?</h3><p>Answer.</p></div>';
$schema = Utility::schema_generator($html, 'faq');
```

#### JSON Data

```php
$json = '{"questions": ["Q1", "Q2"], "answers": ["A1", "A2"]}';
$schema = Utility::schema_generator($json, 'faq');
```

#### WordPress Post

```php
$schema = Utility::schema_generator(get_the_ID(), 'article');
```

#### Array Data

```php
$data = [
    'title' => 'Article Title',
    'content' => 'Article content...',
    'author' => 'Author Name'
];
$schema = Utility::schema_generator($data, 'article');
```

#### Mixed Data

```php
$mixed = [
    'title' => get_the_title(),
    'content' => get_the_content(),
    'author' => get_the_author()
];
$schema = Utility::schema_generator($mixed, 'article');
```

## Schema Types

### FAQ Schema

Automatically detects FAQ patterns in HTML:

- Accordion structures (`accordion-item`, `faq-item`)
- List structures (`<li>`, `<dt>/<dd>`)
- Generic FAQ structures (any `faq` class)

### Article Schema

Extracts article data from:

- WordPress posts and pages
- Custom HTML structures
- Mixed data sources

### Product Schema

Generates product markup for:

- WooCommerce products
- Custom product pages
- Product data arrays

### Organization Schema

For business/company pages with:

- Company name
- Website URL
- Logo

### Person Schema

For team member/author pages with:

- Person name
- Job title
- Company affiliation

## Pattern Recognition

The utility uses intelligent pattern recognition to find content:

### FAQ Patterns

1. **Accordion Pattern**: `accordion-item`, `faq-item` classes
2. **List Pattern**: `<li>`, `<dt>/<dd>` structures
3. **Generic Pattern**: Any `faq` class or heading structure

### Article Patterns

1. **WordPress Pattern**: Standard post structure
2. **Custom Pattern**: `<article>`, `<main>` elements
3. **Heading Pattern**: `<h1>`, `<h2>` titles

### Product Patterns

1. **WooCommerce Pattern**: Standard WooCommerce structure
2. **Custom Pattern**: Product-specific classes
3. **Price Pattern**: Price-containing elements

## Auto-Detection

### Content Type Detection

- **Array**: Detects PHP arrays
- **JSON**: Detects valid JSON strings
- **Post ID**: Detects numeric post IDs
- **HTML**: Detects HTML content (default)

### Pattern Detection

- **FAQ**: Detects question/answer patterns
- **Article**: Detects title/content patterns
- **Product**: Detects name/price patterns

## Examples

### Gutenberg Block Usage

```php
// In accordion block render.php
if (!empty($attributes['faqSchema']) && $attributes['faqSchema']) {
    $faq_schema = Utility::schema_generator($content, 'faq');
    if (!empty($faq_schema)) {
        echo '<script type="application/ld+json">' . $faq_schema . '</script>';
    }
}
```

### Traditional Theme Usage

```php
// In single.php or page.php
$schema = Utility::schema_generator(get_the_ID(), 'article');
SchemaGenerator::output_schema_script($schema);
```

### Custom FAQ Page

```php
// Any HTML structure
$html = '<div class="faq-item"><h3>Question?</h3><p>Answer.</p></div>';
$schema = Utility::schema_generator($html, 'faq');
```

### WooCommerce Product

```php
// In single product template
$schema = Utility::schema_generator($product_data, 'product');
```

## Extending

### Adding New Schema Types

1. Create a new generator in `Generators/`
2. Extend `BaseGenerator` class
3. Implement required methods
4. Register in `SchemaGenerator.php`

### Adding New Extractors

1. Create a new extractor in `Extractors/`
2. Implement extraction logic
3. Register in `SchemaGenerator.php`

### Adding New Patterns

1. Add patterns to `PatternDetector.php`
2. Update extraction logic
3. Test with various content types

## Benefits

1. **Universal**: Works with any WordPress setup
2. **Smart**: Auto-detects content types and patterns
3. **Flexible**: Supports multiple data sources
4. **Extensible**: Easy to add new schema types
5. **Simple**: One-line usage for most cases
6. **Maintainable**: Modular, testable architecture
7. **SEO-Friendly**: Generates proper JSON-LD markup

## Requirements

- PHP 8.1+
- WordPress 6.0+
- BuiltNorth\Utility package

## License

GPL-2.0-or-later
