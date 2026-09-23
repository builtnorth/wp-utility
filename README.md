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
if (class_exists('BuiltNorth\WPUtility\Kit')) {
    $utility = BuiltNorth\WPUtility\Kit::instance();
    $utility->boot();
}
```

## Components

Components provide reusable UI elements with consistent APIs.

### AccessibleCard

Creates accessible card components with proper ARIA attributes:

```php
use BuiltNorth\WPUtility\Facade\Component;

Component::accessible_card(
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
    prefix: 'You are here:',
    nav_attributes: '', // Optional. Block wrapper attrs for <nav> (e.g. get_block_wrapper_attributes()).
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

**Filters:**

- `wp_utility_button_block_prefix` - Customize button class prefix
- `wp_utility_button_screen_reader_text` - Filter resolved screen reader text
- `wp_utility_button_generic_link_labels` - Generic labels that receive post-title context in loops

**Screen reader text:**

```php
$screen_reader = Button::resolve_screen_reader_text([
    'explicit' => 'about our services',
    'text' => 'Learn more',
    'link' => $url,
    'post_id' => get_the_ID(),
    'is_permalink' => false,
    'opens_in_new_tab' => true,
    'text_domain' => 'my-plugin',
]);

Component::button(
    // ...
    screen_reader: $screen_reader,
);
```

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
use BuiltNorth\WPUtility\Facade\Utility;

Utility::archive_url();
```

### CountryList

Returns an array of countries with ISO codes:

```php
$countries = Utility::country_list();
// Returns: ['US' => 'United States', 'CA' => 'Canada', ...]
```

### GetTerms

Renders terms for a post:

```php
Utility::get_terms(
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
$title = Utility::get_title();
```

### ReadingTime

Calculates estimated reading time:

```php
$minutes = Utility::reading_time();
```

**Filter:**

- `wp_utility_reading_time_wpm` - Customize words per minute (default: 200)

### StateList

Returns an array of US states:

```php
$states = Utility::state_list();
// Returns: ['AL' => 'Alabama', 'AK' => 'Alaska', ...]
```

## Helpers

Helpers provide utility functions for common tasks.

### EscapeSvg

Safely escapes SVG content for output:

```php
use BuiltNorth\WPUtility\Facade\Helper;

$safe_svg = Helper::escape_svg($svg_content);
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

Facade methods are camelCase:

```php
Component::accessible_card();
Utility::get_title();
Helper::escape_svg();
```

PHP method names are case-insensitive, so `Component::AccessibleCard()` resolves
to the same method. snake_case names such as `Component::accessible_card()` are
**not** supported — they relied on a `__callStatic` shim that has been removed.

Leaf classes keep their own snake_case method names
(`PresetColor::css_declaration()`); the facade exposes them under camelCase
equivalents (`Helper::css_declaration()`).

## Facades

The three facades are the intended entry points, and each gives a consumer the
whole area through a single `use` statement:

- `BuiltNorth\WPUtility\Facade\Component` — rendering components
- `BuiltNorth\WPUtility\Facade\Helper` — helpers
- `BuiltNorth\WPUtility\Facade\Utility` — utilities

They delegate and never implement. Reaching for a leaf class directly
(`Components\Image`, `Helpers\PresetColor`) is supported, but if a facade is
missing something a consumer needs, that is a gap in the facade.

### Moved in 3.0.0

- `BuiltNorth\WPUtility\Component` and `Components\Component` → `Facade\Component`
- `BuiltNorth\WPUtility\Utility` and `Utilities\Utility` → `Facade\Utility`
- `BuiltNorth\WPUtility\Helper` and `Helpers\Helper` → `Facade\Helper`

`BuiltNorth\WPUtility\Utilities\ImageSetup` still forwards to
`BuiltNorth\WPUtility\Setup\ImageSetup`.

## Testing

Run the test suite:

```bash
composer test
```

## Contributing

See [CONTRIBUTING.md](docs/CONTRIBUTING.md) for details on how to contribute to this project.

## License

This package is licensed under the GPL version 2 or later. See [LICENSE.md](LICENSE.md) for details.

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
