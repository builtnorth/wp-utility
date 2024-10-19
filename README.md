# WP Utility

Composer package containing utility items for use in WordPress themes and plugins.

## Requirements

-   PHP >= 8.1
-   WordPress >= 6.4

## Installation

This library is meant to be dropped into a theme or plugin via composer.

1. In your WordPress project directory, run: `composer require builtnorth/wp-utility`.
2. In your main plugin file or theme's functions.php, add:

```php
use BuiltNorth\Utility;

if (class_exists('BuiltNorth\Utility\Init')) {
    Utility\Init::instance();
}
```

## Features

-   Images Setup:
    -   Removes default/ core image sizes.
    -   Adds custom image sizes via `add_image_size`. Image sizes are optimized for use with responsive images.
    -   Adds custom image sizes to the block editor image size select dropdown.
    -   Sets a reasonable max image width.
    -   @todo allow customization/overriding of all of these settings.
-   Components:

    -   Responsive image helper

        ```
        Component::Image(
        	id: $image_id,
        	size: $image_size,
        	max_width: $max_width,
        	wrap_class: 'cover',
        	class: null,
        	show_caption: $image_caption,
        	lazy: $lazy,
        );
        ```

## Disclaimer

This software is provided "as is", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.

Use of this library is at your own risk. The authors and contributors of this project are not responsible for any damage to your website or any loss of data that may result from the use of this library.

While we strive to keep this library up-to-date and secure, we make no guarantees about its performance, reliability, or suitability for any particular purpose. Users are advised to thoroughly test the library in a safe environment before deploying it to a live site.

By using this library, you acknowledge that you have read this disclaimer and agree to its terms.
