<?php
/**
 * YTrip - Post Types Registration
 * 
 * Registers custom post types for tours, bookings, etc.
 * @package YTrip
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class YTrip_Post_Types {
    
    public static function instance() {
        static $instance = null;
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
    }
    
    public function register_post_types() {
        // Tour Post Type
        register_post_type('ytrip_tour', array(
            'labels' => array(
                'name' => __('Tours', 'ytrip'),
                'singular_name' => __('Tour', 'ytrip'),
                'menu_name' => __('Tours', 'ytrip'),
                'add_new' => __('Add New', 'ytrip'),
                'add_new_item' => __('Add New Tour', 'ytrip'),
                'edit' => __('Edit', 'ytrip'),
                'edit_item' => __('Edit Tour', 'ytrip'),
                'new_item' => __('New Tour', 'ytrip'),
                'view' => __('View', 'ytrip'),
                'view_item' => __('View Tour', 'ytrip'),
                'search_items' => __('Search Tours', 'ytrip'),
                'not_found' => __('No tours found', 'ytrip'),
                'not_found_in_trash' => __('No tours found in trash', 'ytrip'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-airplane',
            'menu_position' => 20,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
            'rewrite' => array('slug' => 'tours'),
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'tours',
        ));
        
        // Booking Post Type
        register_post_type('ytrip_booking', array(
            'labels' => array(
                'name' => __('Bookings', 'ytrip'),
                'singular_name' => __('Booking', 'ytrip'),
                'menu_name' => __('Bookings', 'ytrip'),
                'add_new' => __('Add New', 'ytrip'),
                'add_new_item' => __('Add New Booking', 'ytrip'),
                'edit' => __('Edit', 'ytrip'),
                'edit_item' => __('Edit Booking', 'ytrip'),
                'new_item' => __('New Booking', 'ytrip'),
                'view' => __('View', 'ytrip'),
                'view_item' => __('View Booking', 'ytrip'),
                'search_items' => __('Search Bookings', 'ytrip'),
                'not_found' => __('No bookings found', 'ytrip'),
                'not_found_in_trash' => __('No bookings found in trash', 'ytrip'),
            ),
            'public' => false, // Private post type
            'show_ui' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'menu_position' => 25,
            'supports' => array('title'),
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rest_base' => 'bookings',
        ));
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize
function ytrip_post_types() {
    return YTrip_Post_Types::instance();
}
