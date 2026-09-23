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
use BuiltNorth\WPUtility\Kit;

if (class_exists(Kit::class)) {
    Kit::instance()->boot();
}
```

## Components

Components provide reusable UI elements with consistent APIs.

### AccessibleCard

Creates accessible card components with proper ARIA attributes:

```php
use BuiltNorth\WPUtility\Component;

Component::accessible_card(
    link: 'https://example.com',
    target: '_blank',
    screen_reader: 'Read more about Card Title',
    class: 'custom-card-class'
);
```

### Breadcrumbs

Generates semantic breadcrumb navigation:

```php
use BuiltNorth\WPUtility\Component;

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
use BuiltNorth\WPUtility\Component;

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
use BuiltNorth\WPUtility\Component;

$screen_reader = Component::resolve_screen_reader_text([
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
use BuiltNorth\WPUtility\Component;

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

### Responsive sizes attribute

Builds a `sizes` attribute that accounts for the theme's content width:

```php
use BuiltNorth\WPUtility\Component;

// Full-width image
$sizes = Component::sizes();

// Half-width column, capped against a known content width
$sizes = Component::sizes(
    desktop_vw: 50,
    mobile_breakpoint: 782,
    content_width: 1200
);

// Restore the content width this resolved against
Component::reset_content_width();
```

### Breadcrumb data

The trail as an array, for callers rendering their own markup:

```php
use BuiltNorth\WPUtility\Component;

foreach (Component::get_breadcrumb_data() as $crumb) {
    // ['title' => ..., 'url' => ...]
}
```

### Post type landing URL

Resolves the public URL for a post type archive:

```php
use BuiltNorth\WPUtility\Component;

$url  = Component::post_type_landing_url('product');
$slug = Component::get_rewrite_slug(get_post_type_object('product'));
```

### Generic link labels

Labels too vague to announce on their own, used when building screen reader text:

```php
use BuiltNorth\WPUtility\Component;

$labels = Component::get_generic_link_labels();       // ['read more', 'learn more', ...]
$vague  = Component::is_generic_link_label('Read more'); // true
```

### Pagination

Generates accessible pagination for WordPress queries:

```php
use BuiltNorth\WPUtility\Component;

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
use BuiltNorth\WPUtility\Utility;

Utility::archive_url();
```

### CountryList

Returns an array of countries with ISO codes:

```php
use BuiltNorth\WPUtility\Utility;

$countries = Utility::country_list();
// Returns: ['US' => 'United States', 'CA' => 'Canada', ...]
```

### GetTerms

Renders terms for a post:

```php
use BuiltNorth\WPUtility\Utility;

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
use BuiltNorth\WPUtility\Utility;

$title = Utility::get_title();
```

### ReadingTime

Calculates estimated reading time:

```php
use BuiltNorth\WPUtility\Utility;

$minutes = Utility::reading_time();
```

**Filter:**

- `wp_utility_reading_time_wpm` - Customize words per minute (default: 200)

### StateList

Returns an array of US states:

```php
use BuiltNorth\WPUtility\Utility;

$states = Utility::state_list();
// Returns: ['AL' => 'Alabama', 'AK' => 'Alaska', ...]
```

### PhoneNumber

Normalizes phone numbers and builds `tel:` links:

```php
use BuiltNorth\WPUtility\Utility;

Utility::to_e164('(555) 123-4567');              // '+15551234567'
Utility::to_e164('20 7123 4567', '44');          // '+442071234567'
Utility::tel_link('555-123-4567');               // <a href="tel:+15551234567">555-123-4567</a>
```

### FormatAddress

Single-line address, or a schema.org `PostalAddress`:

```php
use BuiltNorth\WPUtility\Utility;

Utility::format_address(
    street: '1 Main St',
    city: 'Springfield',
    state: 'IL',
    zip: '62701'
);
// '1 Main St, Springfield, IL 62701'

Utility::address_schema(
    street: '1 Main St',
    city: 'Springfield',
    state: 'IL',
    zip: '62701',
    country: 'US'
);
// ['@type' => 'PostalAddress', ...] or null when empty
```

### BusinessHours

Opening hours as schema.org `OpeningHoursSpecification`:

```php
use BuiltNorth\WPUtility\Utility;

Utility::weekly_hours_schema($weekly_hours);
Utility::special_hours_schema($holiday_entries);
```

Both return `null` when there is nothing to describe.

### ImageSetup

Registers the package's image sizes:

```php
use BuiltNorth\WPUtility\Utility;

Utility::image_setup();
```

## Helpers

Helpers provide utility functions for common tasks.

### EscapeSvg

Safely escapes SVG content for output:

```php
use BuiltNorth\WPUtility\Helper;

$safe_svg = Helper::escape_svg($svg_content);
```

### Meta gating

For blocks that should disappear when the meta field backing them is empty:

```php
use BuiltNorth\WPUtility\Helper;

$post_id = Helper::resolve_post_id($block);

if (! Helper::should_render($attributes, $post_id)) {
    return;
}

Helper::is_post_meta_empty('my_field', $post_id);
Helper::is_valid_icon_meta($stored_value);
Helper::resolve_url_from_meta('my_url_field', $post_id);
```

### Preset colours

Turns a block colour attribute — a preset slug or a hex literal — into CSS:

```php
use BuiltNorth\WPUtility\Helper;

Helper::css_value('primary');
// 'var( --wp--preset--color--primary )'

Helper::css_declaration('icon-color', 'primary');
// '--icon-color: var( --wp--preset--color--primary );'

Helper::preset_class('primary', 'icon-background-color');
// 'has-primary-icon-background-color' (empty string for a hex value)
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

Facade methods are snake_case, matching both the leaf method they forward to and
the rest of the codebase:

```php
use BuiltNorth\WPUtility\Component;
use BuiltNorth\WPUtility\Helper;
use BuiltNorth\WPUtility\Utility;

Component::accessible_card();
Utility::get_title();
Helper::escape_svg();
Helper::css_declaration('color', 'primary');
```

`Helper::css_declaration()` is `Helpers\PresetColor::css_declaration()` — the
facade never renames anything, so there is no second vocabulary to learn.

camelCase names such as `Utility::getTitle()` no longer resolve; they were
translated by a `__callStatic` shim that has been removed.

## Facades

The three facades are the intended entry points, and each gives a consumer the
whole area through a single `use` statement:

- `BuiltNorth\WPUtility\Component` — rendering components
- `BuiltNorth\WPUtility\Helper` — helpers
- `BuiltNorth\WPUtility\Utility` — utilities

They delegate and never implement. Reaching for a leaf class directly
(`Components\Image`, `Helpers\PresetColor`) is supported, but if a facade is
missing something a consumer needs, that is a gap in the facade.

### Changed in 3.0.0

Each facade existed twice — once at the namespace root and once inside its area
directory — with the root copy marked deprecated despite being the one every
consumer imported. The duplicates are gone; the root copies are canonical:

- `Components\Component` → `BuiltNorth\WPUtility\Component`
- `Utilities\Utility` → `BuiltNorth\WPUtility\Utility`
- `Helpers\Helper` → `BuiltNorth\WPUtility\Helper`

Facade methods are snake_case now (see above).

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
