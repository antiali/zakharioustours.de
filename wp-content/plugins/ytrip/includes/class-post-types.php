<?php
/**
 * YTrip Post Types
 *
 * @package YTrip
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class YTrip_Post_Types {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_types'), 5);
        add_action('init', array($this, 'register_post_statuses'), 10);
        add_filter('post_updated_messages', array($this, 'post_updated_messages'));
        add_filter('bulk_post_updated_messages', array($this, 'bulk_post_updated_messages'), 10, 2);
    }

    /**
     * Register post types
     */
    public function register_post_types() {
        
        // Tour Post Type
        $tour_labels = array(
            'name'                  => _x('Tours', 'Post type general name', 'ytrip'),
            'singular_name'         => _x('Tour', 'Post type singular name', 'ytrip'),
            'menu_name'             => _x('Tours', 'Admin Menu text', 'ytrip'),
            'name_admin_bar'        => _x('Tour', 'Add New on Toolbar', 'ytrip'),
            'add_new'               => __('Add New', 'ytrip'),
            'add_new_item'          => __('Add New Tour', 'ytrip'),
            'new_item'              => __('New Tour', 'ytrip'),
            'edit_item'             => __('Edit Tour', 'ytrip'),
            'view_item'             => __('View Tour', 'ytrip'),
            'all_items'             => __('All Tours', 'ytrip'),
            'search_items'          => __('Search Tours', 'ytrip'),
            'parent_item_colon'     => __('Parent Tours:', 'ytrip'),
            'not_found'             => __('No tours found.', 'ytrip'),
            'not_found_in_trash'    => __('No tours found in Trash.', 'ytrip'),
            'featured_image'        => _x('Tour Cover Image', 'Overrides the "Featured Image"', 'ytrip'),
            'set_featured_image'    => _x('Set cover image', 'Overrides "Set featured image"', 'ytrip'),
            'remove_featured_image' => _x('Remove cover image', 'Overrides "Remove featured image"', 'ytrip'),
            'use_featured_image'    => _x('Use as cover image', 'Overrides "Use as featured image"', 'ytrip'),
            'archives'              => _x('Tour archives', 'The post type archive label', 'ytrip'),
            'insert_into_item'      => _x('Insert into tour', 'Overrides "Insert into post"', 'ytrip'),
            'uploaded_to_this_item' => _x('Uploaded to this tour', 'Overrides "Uploaded to this post"', 'ytrip'),
            'filter_items_list'     => _x('Filter tours list', 'Screen reader text', 'ytrip'),
            'items_list_navigation' => _x('Tours list navigation', 'Screen reader text', 'ytrip'),
            'items_list'            => _x('Tours list', 'Screen reader text', 'ytrip'),
        );

        $tour_args = array(
            'labels'              => $tour_labels,
            'description'         => __('Tour packages and experiences.', 'ytrip'),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'tour', 'with_front' => false),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-airplane',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes', 'comments'),
            'show_in_rest'        => true,
            'rest_base'           => 'tours',
        );

        register_post_type('tour', $tour_args);

        // Destination Post Type
        $destination_labels = array(
            'name'               => _x('Destinations', 'Post type general name', 'ytrip'),
            'singular_name'      => _x('Destination', 'Post type singular name', 'ytrip'),
            'menu_name'          => _x('Destinations', 'Admin Menu text', 'ytrip'),
            'add_new'            => __('Add New', 'ytrip'),
            'add_new_item'       => __('Add New Destination', 'ytrip'),
            'new_item'           => __('New Destination', 'ytrip'),
            'edit_item'          => __('Edit Destination', 'ytrip'),
            'view_item'          => __('View Destination', 'ytrip'),
            'all_items'          => __('All Destinations', 'ytrip'),
            'search_items'       => __('Search Destinations', 'ytrip'),
            'not_found'          => __('No destinations found.', 'ytrip'),
            'not_found_in_trash' => __('No destinations found in Trash.', 'ytrip'),
        );

        $destination_args = array(
            'labels'              => $destination_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=tour',
            'query_var'           => true,
            'rewrite'             => array('slug' => 'destination', 'with_front' => false),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => true,
            'menu_icon'           => 'dashicons-location',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
            'show_in_rest'        => true,
            'rest_base'           => 'destinations',
        );

        register_post_type('destination', $destination_args);

        // Booking Post Type
        $booking_labels = array(
            'name'               => _x('Bookings', 'Post type general name', 'ytrip'),
            'singular_name'      => _x('Booking', 'Post type singular name', 'ytrip'),
            'menu_name'          => _x('Bookings', 'Admin Menu text', 'ytrip'),
            'add_new'            => __('Add New', 'ytrip'),
            'add_new_item'       => __('Add New Booking', 'ytrip'),
            'new_item'           => __('New Booking', 'ytrip'),
            'edit_item'          => __('Edit Booking', 'ytrip'),
            'view_item'          => __('View Booking', 'ytrip'),
            'all_items'          => __('All Bookings', 'ytrip'),
            'search_items'       => __('Search Bookings', 'ytrip'),
            'not_found'          => __('No bookings found.', 'ytrip'),
            'not_found_in_trash' => __('No bookings found in Trash.', 'ytrip'),
        );

        $booking_args = array(
            'labels'              => $booking_labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=tour',
            'query_var'           => false,
            'capability_type'     => 'post',
            'capabilities'        => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-calendar-alt',
            'supports'            => array('title'),
            'show_in_rest'        => true,
            'rest_base'           => 'bookings',
        );

        register_post_type('booking', $booking_args);

        // Coupon Post Type
        $coupon_labels = array(
            'name'               => _x('Coupons', 'Post type general name', 'ytrip'),
            'singular_name'      => _x('Coupon', 'Post type singular name', 'ytrip'),
            'menu_name'          => _x('Coupons', 'Admin Menu text', 'ytrip'),
            'add_new'            => __('Add New', 'ytrip'),
            'add_new_item'       => __('Add New Coupon', 'ytrip'),
            'edit_item'          => __('Edit Coupon', 'ytrip'),
            'view_item'          => __('View Coupon', 'ytrip'),
            'all_items'          => __('All Coupons', 'ytrip'),
            'search_items'       => __('Search Coupons', 'ytrip'),
            'not_found'          => __('No coupons found.', 'ytrip'),
        );

        $coupon_args = array(
            'labels'              => $coupon_labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=tour',
            'query_var'           => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_icon'           => 'dashicons-tickets-alt',
            'supports'            => array('title'),
            'show_in_rest'        => true,
        );

        register_post_type('coupon', $coupon_args);
    }

    /**
     * Register post statuses
     */
    public function register_post_statuses() {
        
        // Booking statuses
        register_post_status('ytrip-pending', array(
            'label'                     => _x('Pending Payment', 'Booking status', 'ytrip'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'ytrip'),
        ));

        register_post_status('ytrip-confirmed', array(
            'label'                     => _x('Confirmed', 'Booking status', 'ytrip'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'ytrip'),
        ));

        register_post_status('ytrip-completed', array(
            'label'                     => _x('Completed', 'Booking status', 'ytrip'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'ytrip'),
        ));

        register_post_status('ytrip-cancelled', array(
            'label'                     => _x('Cancelled', 'Booking status', 'ytrip'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'ytrip'),
        ));

        register_post_status('ytrip-refunded', array(
            'label'                     => _x('Refunded', 'Booking status', 'ytrip'),
            'public'                    => false,
            'exclude_from_search'       => true,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'ytrip'),
        ));
    }

    /**
     * Custom post updated messages
     */
    public function post_updated_messages($messages) {
        global $post;

        $messages['tour'] = array(
            0  => '',
            1  => __('Tour updated.', 'ytrip'),
            2  => __('Custom field updated.', 'ytrip'),
            3  => __('Custom field deleted.', 'ytrip'),
            4  => __('Tour updated.', 'ytrip'),
            5  => isset($_GET['revision']) ? sprintf(__('Tour restored to revision from %s', 'ytrip'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __('Tour published.', 'ytrip'),
            7  => __('Tour saved.', 'ytrip'),
            8  => __('Tour submitted.', 'ytrip'),
            9  => sprintf(__('Tour scheduled for: <strong>%1$s</strong>.', 'ytrip'), date_i18n(__('M j, Y @ G:i', 'ytrip'), strtotime($post->post_date))),
            10 => __('Tour draft updated.', 'ytrip'),
        );

        $messages['destination'] = array(
            0  => '',
            1  => __('Destination updated.', 'ytrip'),
            4  => __('Destination updated.', 'ytrip'),
            6  => __('Destination published.', 'ytrip'),
            7  => __('Destination saved.', 'ytrip'),
        );

        $messages['booking'] = array(
            0  => '',
            1  => __('Booking updated.', 'ytrip'),
            4  => __('Booking updated.', 'ytrip'),
            6  => __('Booking created.', 'ytrip'),
            7  => __('Booking saved.', 'ytrip'),
        );

        return $messages;
    }

    /**
     * Bulk post updated messages
     */
    public function bulk_post_updated_messages($bulk_messages, $bulk_counts) {
        $bulk_messages['tour'] = array(
            'updated'   => _n('%s tour updated.', '%s tours updated.', $bulk_counts['updated'], 'ytrip'),
            'locked'    => _n('%s tour not updated, somebody is editing it.', '%s tours not updated, somebody is editing them.', $bulk_counts['locked'], 'ytrip'),
            'deleted'   => _n('%s tour permanently deleted.', '%s tours permanently deleted.', $bulk_counts['deleted'], 'ytrip'),
            'trashed'   => _n('%s tour moved to the Trash.', '%s tours moved to the Trash.', $bulk_counts['trashed'], 'ytrip'),
            'untrashed' => _n('%s tour restored from the Trash.', '%s tours restored from the Trash.', $bulk_counts['untrashed'], 'ytrip'),
        );

        return $bulk_messages;
    }
}

// Initialize
new YTrip_Post_Types();
