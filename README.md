# WP Utility

A comprehensive WordPress utility library providing reusable components, helpers, and utilities for modern WordPress development.

## Requirements

- PHP >= 8.1
- WordPress >= 6.4

## Installation

Install via Composer:

```bash
composer require builtnorth/wp-utility
```

### Basic Setup

In your plugin or theme, initialize WP Utility:

```php
if (class_exists('BuiltNorth\WPUtility\App')) {
    $utility = BuiltNorth\WPUtility\App::instance();
    $utility->boot();
}
```

## Components

Components provide reusable UI elements with consistent APIs.

### AccessibleCard

Creates accessible card components with proper ARIA attributes:

```php
use BuiltNorth\WPUtility\Components\Component;

Component::accessibleCard(
    url: 'https://example.com',
    title: 'Card Title',
    new_tab: true,
    class: 'custom-card-class'
);
```

### Breadcrumbs

Generates semantic breadcrumb navigation:

```php
Component::breadcrumbs(
    show_on_front: true,
    class: 'breadcrumbs',
    separator: '»',
    home_title: 'Home',
    prefix: 'You are here:'
);
```

**Filters:**

- `wp_utility_breadcrumb_open_nav` - Customize opening navigation HTML
- `wp_utility_breadcrumb_close_nav` - Customize closing navigation HTML
- `wp_utility_breadcrumb_item` - Customize individual breadcrumb items
- `wp_utility_breadcrumb_separator` - Customize separator HTML

### Button

Renders flexible button or link elements:

```php
Component::button(
    button_type: 'a',
    class: 'btn',
    extra_class: 'custom-btn',
    style: 'primary',
    size: 'large',
    appearance: 'fill',
    text: 'Click Me',
    link: 'https://example.com',
    target: '_blank',
    screen_reader: 'Opens in new window',
    attributes: ['data-tracking' => 'button-click'],
    icon: '<svg>...</svg>',
    icon_position: 'left'
);
```

**Filter:**

- `wp_utility_button_block_prefix` - Customize button class prefix

### Image

Advanced image rendering with responsive sizes:

```php
Component::image(
    id: 123,
    class: 'featured-image',
    additional_classes: 'rounded shadow',
    custom_alt: 'Custom alt text',
    show_caption: true,
    lazy: true,
    wrap_class: 'image-wrapper',
    include_figure: true,
    size: 'wide_large',
    max_width: '1200px',
    style: 'border-radius: 8px;',
    caption: 'Image caption text',
    alt: 'Alt text override'
);
```

### Pagination

Generates accessible pagination for WordPress queries:

```php
Component::pagination(
    query: $custom_query  // WP_Query object (optional, uses global $wp_query if not provided)
);
```

**Filters:**

- `wp_utility_pagination_args` - Modify pagination arguments
- `wp_utility_pagination_wrapper` - Customize wrapper attributes

## Utilities

Utilities provide data processing and retrieval functions.

### ArchiveUrl

Handles conversion of pretty permalinks to query string URLs for archive pages:

```php
use BuiltNorth\WPUtility\Utilities\Utility;

Utility::archiveUrl();
```

### CountryList

Returns an array of countries with ISO codes:

```php
$countries = Utility::countryList();
// Returns: ['US' => 'United States', 'CA' => 'Canada', ...]
```

### GetTerms

Renders terms for a post:

```php
Utility::getTerms(
    post_id: get_the_ID(),
    taxonomy: 'category',
    taxonomy_link: true,
    first_term_only: false,
    class: 'post-terms'
);
```

### GetTitle

Retrieves appropriate page title across different WordPress contexts:

```php
$title = Utility::getTitle();
```

### LazyLoadFirstBlock

Controls lazy loading behavior for blocks:

```php
$should_lazy = Utility::lazyLoadFirstBlock(
    block: $block,
    non_lazy_parents: ['core/columns', 'core/group'],
    default_lazy: true
);
```

**Filter:**

- `wp_utility_lazy_load_non_lazy_parents` - Modify non-lazy parent blocks

### ReadingTime

Calculates estimated reading time:

```php
$minutes = Utility::readingTime();
```

**Filter:**

- `wp_utility_reading_time_wpm` - Customize words per minute (default: 200)

### StateList

Returns an array of US states:

```php
$states = Utility::stateList();
// Returns: ['AL' => 'Alabama', 'AK' => 'Alaska', ...]
```

## Helpers

Helpers provide utility functions for common tasks.

### EscapeSvg

Safely escapes SVG content for output:

```php
use BuiltNorth\WPUtility\Helpers\Helper;

$safe_svg = Helper::escapeSvg($svg_content);
```

## Setup

Setup classes handle WordPress configuration and initialization.

### ImageSetup

Configures custom image sizes and removes WordPress defaults.

#### Manual Initialization

```php
use BuiltNorth\WPUtility\Setup\ImageSetup;

ImageSetup::setup();
```

#### Default Image Sizes

- **Wide formats:**
    - `wide_xlarge`: 1600px wide
    - `wide_large`: 1200px wide
    - `wide_medium`: 800px wide
    - `wide_small`: 600px wide
    - `wide_xsmall`: 300px wide

- **Square formats (cropped):**
    - `square_xlarge`: 1200x1200
    - `square_large`: 800x800
    - `square_medium`: 600x600
    - `square_small`: 300x300
    - `square_xsmall`: 150x150

#### Filters

- `wp_utility_image_sizes` - Customize image sizes array
- `wp_utility_image_size_names` - Customize display names
- `wp_utility_remove_default_sizes` - Control which default sizes to remove
- `wp_utility_max_srcset_width` - Set maximum srcset width (default: 1600)

#### Example: Custom Image Sizes

```php
add_filter('wp_utility_image_sizes', function($sizes) {
    $sizes['banner'] = [1920, 600, true];
    $sizes['thumbnail_large'] = [400, 400, true];
    return $sizes;
});

add_filter('wp_utility_image_size_names', function($names) {
    $names['banner'] = __('Banner Image');
    $names['thumbnail_large'] = __('Large Thumbnail');
    return $names;
});
```

## Method Naming Conventions

All components, utilities, and helpers support multiple naming conventions:

```php
// camelCase
Component::accessibleCard();
Utility::getTitle();
Helper::escapeSvg();

// PascalCase (PHP is case-insensitive for methods)
Component::AccessibleCard();
Utility::GetTitle();
Helper::EscapeSvg();

// snake_case (via magic methods)
Component::accessible_card();
Utility::get_title();
Helper::escape_svg();
```

## Backward Compatibility

The library maintains backward compatibility through class aliases:

- `BuiltNorth\WPUtility\Component` → `BuiltNorth\WPUtility\Components\Component`
- `BuiltNorth\WPUtility\Utility` → `BuiltNorth\WPUtility\Utilities\Utility`
- `BuiltNorth\WPUtility\Helper` → `BuiltNorth\WPUtility\Helpers\Helper`
- `BuiltNorth\WPUtility\Utilities\ImageSetup` → `BuiltNorth\WPUtility\Setup\ImageSetup`

## Testing

Run the test suite:

```bash
composer test
```

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## License

This package is licensed under the GPL version 2 or later. See [LICENSE.md](LICENSE.md) for details.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
