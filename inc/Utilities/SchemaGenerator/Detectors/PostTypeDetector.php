<?php

declare(strict_types=1);

namespace BuiltNorth\Utility\Utilities\SchemaGenerator\Detectors;

/**
 * Post Type Detector
 * 
 * Detects appropriate schema types for posts based on post type with plugin override capability
 * 
 * @package BuiltNorth\Utility
 * @since 1.0.0
 */
class PostTypeDetector
{
    /**
     * Get schema type for a post with plugin override capability
     *
     * @param int $post_id Post ID
     * @return string Schema type
     */
    public static function render($post_id)
    {
        // Allow plugins to override schema type
        $schema_type = apply_filters('wp_utility_schema_type_for_post', null, $post_id);
        
        if ($schema_type) {
            return $schema_type;
        }
        
        // Basic detection based on post type
        $post_type = get_post_type($post_id);
        
        return self::get_schema_type_from_post_type($post_type);
    }

    /**
     * Get schema type from post type
     *
     * @param string $post_type Post type
     * @return string Schema type
     */
    private static function get_schema_type_from_post_type($post_type)
    {
        switch ($post_type) {
            case 'product':
            case 'woocommerce_product':
                return 'Product';
            case 'service':
            case 'services':
                return 'Service';
            case 'software':
            case 'app':
            case 'application':
                return 'SoftwareApplication';
            case 'restaurant':
            case 'food':
                return 'Restaurant';
            case 'hotel':
            case 'accommodation':
                return 'Hotel';
            case 'event':
            case 'events':
                return 'Event';
            case 'organization':
            case 'company':
                return 'Organization';
            case 'person':
            case 'team_member':
            case 'staff':
                return 'Person';
            case 'article':
            case 'post':
            case 'blog':
                return 'Article';
            case 'page':
            default:
                return 'Product'; // Default fallback
        }
    }
} 