<?php
/**
 * YTrip Taxonomies
 *
 * @package YTrip
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class YTrip_Taxonomies {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'), 5);
    }

    /**
     * Register all taxonomies
     */
    public function register_taxonomies() {
        
        // Tour Category
        $category_labels = array(
            'name'                       => _x('Tour Categories', 'taxonomy general name', 'ytrip'),
            'singular_name'              => _x('Category', 'taxonomy singular name', 'ytrip'),
            'search_items'               => __('Search Categories', 'ytrip'),
            'popular_items'              => __('Popular Categories', 'ytrip'),
            'all_items'                  => __('All Categories', 'ytrip'),
            'parent_item'                => __('Parent Category', 'ytrip'),
            'parent_item_colon'          => __('Parent Category:', 'ytrip'),
            'edit_item'                  => __('Edit Category', 'ytrip'),
            'view_item'                  => __('View Category', 'ytrip'),
            'update_item'                => __('Update Category', 'ytrip'),
            'add_new_item'               => __('Add New Category', 'ytrip'),
            'new_item_name'              => __('New Category Name', 'ytrip'),
            'separate_items_with_commas' => __('Separate categories with commas', 'ytrip'),
            'add_or_remove_items'        => __('Add or remove categories', 'ytrip'),
            'choose_from_most_used'      => __('Choose from the most used categories', 'ytrip'),
            'not_found'                  => __('No categories found.', 'ytrip'),
            'no_terms'                   => __('No categories', 'ytrip'),
            'menu_name'                  => __('Categories', 'ytrip'),
            'back_to_items'              => __('&larr; Back to Categories', 'ytrip'),
        );

        register_taxonomy('tour_category', array('tour'), array(
            'labels'            => $category_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
            'rest_base'         => 'tour-categories',
            'rewrite'           => array('slug' => 'tour-category', 'with_front' => false),
        ));

        // Tour Type (Adventure, Cultural, Beach, etc.)
        $type_labels = array(
            'name'                       => _x('Tour Types', 'taxonomy general name', 'ytrip'),
            'singular_name'              => _x('Type', 'taxonomy singular name', 'ytrip'),
            'search_items'               => __('Search Types', 'ytrip'),
            'all_items'                  => __('All Types', 'ytrip'),
            'edit_item'                  => __('Edit Type', 'ytrip'),
            'update_item'                => __('Update Type', 'ytrip'),
            'add_new_item'               => __('Add New Type', 'ytrip'),
            'new_item_name'              => __('New Type Name', 'ytrip'),
            'menu_name'                  => __('Tour Types', 'ytrip'),
        );

        register_taxonomy('tour_type', array('tour'), array(
            'labels'            => $type_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'rest_base'         => 'tour-types',
            'rewrite'           => array('slug' => 'tour-type', 'with_front' => false),
        ));

        // Tour Tags
        $tag_labels = array(
            'name'                       => _x('Tour Tags', 'taxonomy general name', 'ytrip'),
            'singular_name'              => _x('Tag', 'taxonomy singular name', 'ytrip'),
            'search_items'               => __('Search Tags', 'ytrip'),
            'popular_items'              => __('Popular Tags', 'ytrip'),
            'all_items'                  => __('All Tags', 'ytrip'),
            'edit_item'                  => __('Edit Tag', 'ytrip'),
            'update_item'                => __('Update Tag', 'ytrip'),
            'add_new_item'               => __('Add New Tag', 'ytrip'),
            'new_item_name'              => __('New Tag Name', 'ytrip'),
            'separate_items_with_commas' => __('Separate tags with commas', 'ytrip'),
            'add_or_remove_items'        => __('Add or remove tags', 'ytrip'),
            'choose_from_most_used'      => __('Choose from the most used tags', 'ytrip'),
            'not_found'                  => __('No tags found.', 'ytrip'),
            'menu_name'                  => __('Tags', 'ytrip'),
        );

        register_taxonomy('tour_tag', array('tour'), array(
            'labels'            => $tag_labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => false,
            'show_in_nav_menus' => true,
            'show_tagcloud'     => true,
            'show_in_rest'      => true,
            'rest_base'         => 'tour-tags',
            'rewrite'           => array('slug' => 'tour-tag', 'with_front' => false),
        ));

        // Difficulty Level
        $difficulty_labels = array(
            'name'          => _x('Difficulty', 'taxonomy general name', 'ytrip'),
            'singular_name' => _x('Difficulty', 'taxonomy singular name', 'ytrip'),
            'menu_name'     => __('Difficulty', 'ytrip'),
        );

        register_taxonomy('tour_difficulty', array('tour'), array(
            'labels'            => $difficulty_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'difficulty', 'with_front' => false),
        ));

        // Duration
        $duration_labels = array(
            'name'          => _x('Duration', 'taxonomy general name', 'ytrip'),
            'singular_name' => _x('Duration', 'taxonomy singular name', 'ytrip'),
            'menu_name'     => __('Duration', 'ytrip'),
        );

        register_taxonomy('tour_duration', array('tour'), array(
            'labels'            => $duration_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => false,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'duration', 'with_front' => false),
        ));

        // Destination Location
        $location_labels = array(
            'name'          => _x('Locations', 'taxonomy general name', 'ytrip'),
            'singular_name' => _x('Location', 'taxonomy singular name', 'ytrip'),
            'menu_name'     => __('Locations', 'ytrip'),
            'all_items'     => __('All Locations', 'ytrip'),
            'add_new_item'  => __('Add New Location', 'ytrip'),
        );

        register_taxonomy('tour_location', array('tour', 'destination'), array(
            'labels'            => $location_labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'location', 'with_front' => false),
        ));

        // Features/Amenities
        $features_labels = array(
            'name'          => _x('Features', 'taxonomy general name', 'ytrip'),
            'singular_name' => _x('Feature', 'taxonomy singular name', 'ytrip'),
            'menu_name'     => __('Features', 'ytrip'),
        );

        register_taxonomy('tour_feature', array('tour'), array(
            'labels'            => $features_labels,
            'hierarchical'      => false,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => false,
            'show_in_nav_menus' => false,
            'show_in_rest'      => true,
            'rewrite'           => array('slug' => 'feature', 'with_front' => false),
        ));

        // Insert default terms
        $this->insert_default_terms();
    }

    /**
     * Insert default taxonomy terms
     */
    private function insert_default_terms() {
        // Only run once
        if (get_option('ytrip_default_terms_inserted')) {
            return;
        }

        // Default Tour Types
        $tour_types = array(
            'adventure'   => __('Adventure', 'ytrip'),
            'cultural'    => __('Cultural', 'ytrip'),
            'beach'       => __('Beach & Relaxation', 'ytrip'),
            'wildlife'    => __('Wildlife & Safari', 'ytrip'),
            'city'        => __('City Tours', 'ytrip'),
            'cruise'      => __('Cruises', 'ytrip'),
            'hiking'      => __('Hiking & Trekking', 'ytrip'),
            'religious'   => __('Religious & Pilgrimage', 'ytrip'),
            'honeymoon'   => __('Honeymoon', 'ytrip'),
            'family'      => __('Family Tours', 'ytrip'),
        );

        foreach ($tour_types as $slug => $name) {
            if (!term_exists($slug, 'tour_type')) {
                wp_insert_term($name, 'tour_type', array('slug' => $slug));
            }
        }

        // Default Difficulty Levels
        $difficulties = array(
            'easy'      => __('Easy', 'ytrip'),
            'moderate'  => __('Moderate', 'ytrip'),
            'difficult' => __('Difficult', 'ytrip'),
            'extreme'   => __('Extreme', 'ytrip'),
        );

        foreach ($difficulties as $slug => $name) {
            if (!term_exists($slug, 'tour_difficulty')) {
                wp_insert_term($name, 'tour_difficulty', array('slug' => $slug));
            }
        }

        // Default Durations
        $durations = array(
            'half-day'  => __('Half Day', 'ytrip'),
            'full-day'  => __('Full Day', 'ytrip'),
            '2-3-days'  => __('2-3 Days', 'ytrip'),
            '4-7-days'  => __('4-7 Days', 'ytrip'),
            '1-2-weeks' => __('1-2 Weeks', 'ytrip'),
            '2-weeks'   => __('2+ Weeks', 'ytrip'),
        );

        foreach ($durations as $slug => $name) {
            if (!term_exists($slug, 'tour_duration')) {
                wp_insert_term($name, 'tour_duration', array('slug' => $slug));
            }
        }

        // Default Features
        $features = array(
            'wifi'           => __('Free WiFi', 'ytrip'),
            'meals'          => __('Meals Included', 'ytrip'),
            'transport'      => __('Transport Included', 'ytrip'),
            'guide'          => __('Professional Guide', 'ytrip'),
            'hotel'          => __('Hotel Pickup', 'ytrip'),
            'insurance'      => __('Travel Insurance', 'ytrip'),
            'photography'    => __('Photography Service', 'ytrip'),
            'equipment'      => __('Equipment Provided', 'ytrip'),
        );

        foreach ($features as $slug => $name) {
            if (!term_exists($slug, 'tour_feature')) {
                wp_insert_term($name, 'tour_feature', array('slug' => $slug));
            }
        }

        update_option('ytrip_default_terms_inserted', true);
    }
}

// Initialize
new YTrip_Taxonomies();
