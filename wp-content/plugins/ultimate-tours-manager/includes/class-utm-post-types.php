<?php
/**
 * UTM Post Types Class
 *
 * Register custom post types
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Post_Types {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'register_tour_post_type'));
        add_action('init', array($this, 'register_booking_post_type'));
        add_action('init', array($this, 'register_destination_post_type'));
        add_action('init', array($this, 'register_agent_post_type'));
        
        // Add columns
        add_filter('manage_edit-tour_columns', array($this, 'tour_columns'));
        add_action('manage_tour_posts_custom_column', array($this, 'tour_column_data'), 10, 2);
        add_filter('manage_edit-booking_columns', array($this, 'booking_columns'));
        add_action('manage_booking_posts_custom_column', array($this, 'booking_column_data'), 10, 2);
        
        // Sortable columns
        add_filter('manage_edit-tour_sortable_columns', array($this, 'tour_sortable_columns'));
        add_filter('manage_edit-booking_sortable_columns', array($this, 'booking_sortable_columns'));
        
        // Row actions
        add_filter('post_row_actions', array($this, 'tour_row_actions'), 10, 2);
        add_filter('post_row_actions', array($this, 'booking_row_actions'), 10, 2);
        
        // Bulk actions
        add_filter('bulk_actions-edit-tour', array($this, 'tour_bulk_actions'));
        add_filter('bulk_actions-edit-booking', array($this, 'booking_bulk_actions'));
        
        // Handle bulk actions
        add_filter('handle_bulk_actions-edit-tour', array($this, 'handle_tour_bulk_actions'), 10, 3);
        add_filter('handle_bulk_actions-edit-booking', array($this, 'handle_booking_bulk_actions'), 10, 3);
    }
    
    /**
     * Register Tour Post Type
     */
    public function register_tour_post_type() {
        $labels = array(
            'name' => __('Tours', 'ultimate-tours-manager'),
            'singular_name' => __('Tour', 'ultimate-tours-manager'),
            'menu_name' => __('Tours', 'ultimate-tours-manager'),
            'add_new' => __('Add New', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Tour', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Tour', 'ultimate-tours-manager'),
            'new_item' => __('New Tour', 'ultimate-tours-manager'),
            'view_item' => __('View Tour', 'ultimate-tours-manager'),
            'view_items' => __('View Tours', 'ultimate-tours-manager'),
            'search_items' => __('Search Tours', 'ultimate-tours-manager'),
            'not_found' => __('No tours found', 'ultimate-tours-manager'),
            'not_found_in_trash' => __('No tours found in trash', 'ultimate-tours-manager'),
            'all_items' => __('All Tours', 'ultimate-tours-manager'),
            'featured_image' => __('Tour Featured Image', 'ultimate-tours-manager'),
            'set_featured_image' => __('Set tour featured image', 'ultimate-tours-manager'),
            'remove_featured_image' => __('Remove tour featured image', 'ultimate-tours-manager'),
            'use_featured_image' => __('Use as tour featured image', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour packages and packages', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => get_option('utm_tour_slug', 'tour'),
                'with_front' => true,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-site-alt3',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'author',
                'custom-fields',
            ),
            'taxonomies' => array('destination', 'tour-type', 'tour-feature'),
            'show_in_rest' => true,
            'rest_base' => 'tours',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('tour', $args);
    }
    
    /**
     * Register Booking Post Type
     */
    public function register_booking_post_type() {
        $labels = array(
            'name' => __('Bookings', 'ultimate-tours-manager'),
            'singular_name' => __('Booking', 'ultimate-tours-manager'),
            'menu_name' => __('Bookings', 'ultimate-tours-manager'),
            'add_new' => __('Add New', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Booking', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Booking', 'ultimate-tours-manager'),
            'new_item' => __('New Booking', 'ultimate-tours-manager'),
            'view_item' => __('View Booking', 'ultimate-tours-manager'),
            'view_items' => __('View Bookings', 'ultimate-tours-manager'),
            'search_items' => __('Search Bookings', 'ultimate-tours-manager'),
            'not_found' => __('No bookings found', 'ultimate-tours-manager'),
            'not_found_in_trash' => __('No bookings found in trash', 'ultimate-tours-manager'),
            'all_items' => __('All Bookings', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour bookings', 'ultimate-tours-manager'),
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=tour',
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => array('title', 'author'),
            'show_in_rest' => true,
            'rest_base' => 'bookings',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('booking', $args);
    }
    
    /**
     * Register Destination Post Type
     */
    public function register_destination_post_type() {
        $labels = array(
            'name' => __('Destinations', 'ultimate-tours-manager'),
            'singular_name' => __('Destination', 'ultimate-tours-manager'),
            'menu_name' => __('Destinations', 'ultimate-tours-manager'),
            'add_new' => __('Add New', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Destination', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Destination', 'ultimate-tours-manager'),
            'new_item' => __('New Destination', 'ultimate-tours-manager'),
            'view_item' => __('View Destination', 'ultimate-tours-manager'),
            'view_items' => __('View Destinations', 'ultimate-tours-manager'),
            'search_items' => __('Search Destinations', 'ultimate-tours-manager'),
            'not_found' => __('No destinations found', 'ultimate-tours-manager'),
            'not_found_in_trash' => __('No destinations found in trash', 'ultimate-tours-manager'),
            'all_items' => __('All Destinations', 'ultimate-tours-manager'),
            'featured_image' => __('Destination Featured Image', 'ultimate-tours-manager'),
            'set_featured_image' => __('Set destination featured image', 'ultimate-tours-manager'),
            'remove_featured_image' => __('Remove destination featured image', 'ultimate-tours-manager'),
            'use_featured_image' => __('Use as destination featured image', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour destinations', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => get_option('utm_destination_slug', 'destination'),
                'with_front' => true,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-location-alt',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'author',
            ),
            'show_in_rest' => true,
            'rest_base' => 'destinations',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('destination', $args);
    }
    
    /**
     * Register Agent Post Type
     */
    public function register_agent_post_type() {
        $labels = array(
            'name' => __('Agents', 'ultimate-tours-manager'),
            'singular_name' => __('Agent', 'ultimate-tours-manager'),
            'menu_name' => __('Agents', 'ultimate-tours-manager'),
            'add_new' => __('Add New', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Agent', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Agent', 'ultimate-tours-manager'),
            'new_item' => __('New Agent', 'ultimate-tours-manager'),
            'view_item' => __('View Agent', 'ultimate-tours-manager'),
            'view_items' => __('View Agents', 'ultimate-tours-manager'),
            'search_items' => __('Search Agents', 'ultimate-tours-manager'),
            'not_found' => __('No agents found', 'ultimate-tours-manager'),
            'not_found_in_trash' => __('No agents found in trash', 'ultimate-tours-manager'),
            'all_items' => __('All Agents', 'ultimate-tours-manager'),
            'featured_image' => __('Agent Photo', 'ultimate-tours-manager'),
            'set_featured_image' => __('Set agent photo', 'ultimate-tours-manager'),
            'remove_featured_image' => __('Remove agent photo', 'ultimate-tours-manager'),
            'use_featured_image' => __('Use as agent photo', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour agents', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'agent',
                'with_front' => true,
                'feeds' => true,
            ),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 7,
            'menu_icon' => 'dashicons-groups',
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'author',
                'custom-fields',
            ),
            'show_in_rest' => true,
            'rest_base' => 'agents',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        
        register_post_type('agent', $args);
    }
    
    /**
     * Tour custom columns
     */
    public function tour_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['featured_image'] = __('Image', 'ultimate-tours-manager');
        $new_columns['title'] = $columns['title'];
        $new_columns['destination'] = __('Destination', 'ultimate-tours-manager');
        $new_columns['tour_type'] = __('Type', 'ultimate-tours-manager');
        $new_columns['duration'] = __('Duration', 'ultimate-tours-manager');
        $new_columns['price'] = __('Price', 'ultimate-tours-manager');
        $new_columns['reviews'] = __('Reviews', 'ultimate-tours-manager');
        $new_columns['bookings'] = __('Bookings', 'ultimate-tours-manager');
        $new_columns['status'] = __('Status', 'ultimate-tours-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Tour column data
     */
    public function tour_column_data($column, $post_id) {
        switch ($column) {
            case 'featured_image':
                $thumbnail = get_the_post_thumbnail($post_id, array(60, 60));
                if ($thumbnail) {
                    echo $thumbnail;
                } else {
                    echo '<span class="dashicons dashicons-format-image"></span>';
                }
                break;
                
            case 'destination':
                $destinations = get_the_terms($post_id, 'destination');
                if (!empty($destinations) && !is_wp_error($destinations)) {
                    $output = array();
                    foreach ($destinations as $destination) {
                        $output[] = sprintf(
                            '<a href="%s">%s</a>',
                            admin_url('edit.php?post_type=tour&destination=' . $destination->slug),
                            $destination->name
                        );
                    }
                    echo implode(', ', $output);
                }
                break;
                
            case 'tour_type':
                $types = get_the_terms($post_id, 'tour-type');
                if (!empty($types) && !is_wp_error($types)) {
                    $output = array();
                    foreach ($types as $type) {
                        $output[] = sprintf(
                            '<a href="%s">%s</a>',
                            admin_url('edit.php?post_type=tour&tour-type=' . $type->slug),
                            $type->name
                        );
                    }
                    echo implode(', ', $output);
                }
                break;
                
            case 'duration':
                $duration = get_post_meta($post_id, '_tour_duration', true);
                $unit = get_post_meta($post_id, '_tour_duration_unit', true);
                echo esc_html($duration . ' ' . $unit);
                break;
                
            case 'price':
                $price = get_post_meta($post_id, '_tour_price', true);
                $currency = get_option('utm_currency', 'USD');
                echo esc_html($currency . ' ' . number_format($price, 2));
                break;
                
            case 'reviews':
                $rating = get_post_meta($post_id, '_tour_average_rating', true);
                $count = get_post_meta($post_id, '_tour_review_count', true);
                echo '<span class="tour-rating">★ ' . number_format($rating, 1) . '</span>';
                echo ' <span class="tour-reviews-count">(' . $count . ')</span>';
                break;
                
            case 'bookings':
                $bookings = get_post_meta($post_id, '_tour_booking_count', true);
                echo esc_html($bookings);
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_tour_status', true);
                $status = $status ? $status : 'active';
                $status_label = array(
                    'active' => __('Active', 'ultimate-tours-manager'),
                    'inactive' => __('Inactive', 'ultimate-tours-manager'),
                    'sold_out' => __('Sold Out', 'ultimate-tours-manager'),
                    'coming_soon' => __('Coming Soon', 'ultimate-tours-manager'),
                );
                echo sprintf(
                    '<span class="tour-status status-%s">%s</span>',
                    $status,
                    isset($status_label[$status]) ? $status_label[$status] : $status
                );
                break;
        }
    }
    
    /**
     * Booking custom columns
     */
    public function booking_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['booking_number'] = __('Booking #', 'ultimate-tours-manager');
        $new_columns['tour'] = __('Tour', 'ultimate-tours-manager');
        $new_columns['customer'] = __('Customer', 'ultimate-tours-manager');
        $new_columns['date'] = __('Date', 'ultimate-tours-manager');
        $new_columns['guests'] = __('Guests', 'ultimate-tours-manager');
        $new_columns['total'] = __('Total', 'ultimate-tours-manager');
        $new_columns['status'] = __('Status', 'ultimate-tours-manager');
        $new_columns['payment'] = __('Payment', 'ultimate-tours-manager');
        
        return $new_columns;
    }
    
    /**
     * Booking column data
     */
    public function booking_column_data($column, $post_id) {
        switch ($column) {
            case 'booking_number':
                $booking_number = get_post_meta($post_id, '_booking_number', true);
                echo esc_html($booking_number);
                break;
                
            case 'tour':
                $tour_id = get_post_meta($post_id, '_tour_id', true);
                if ($tour_id) {
                    $tour_title = get_the_title($tour_id);
                    echo sprintf(
                        '<a href="%s">%s</a>',
                        admin_url('post.php?post=' . $tour_id . '&action=edit'),
                        esc_html($tour_title)
                    );
                }
                break;
                
            case 'customer':
                $first_name = get_post_meta($post_id, '_first_name', true);
                $last_name = get_post_meta($post_id, '_last_name', true);
                $email = get_post_meta($post_id, '_email', true);
                echo sprintf(
                    '<a href="mailto:%s">%s %s</a>',
                    esc_attr($email),
                    esc_html($first_name),
                    esc_html($last_name)
                );
                break;
                
            case 'date':
                $booking_date = get_post_meta($post_id, '_booking_date', true);
                $booking_time = get_post_meta($post_id, '_booking_time', true);
                echo esc_html(date_i18n(get_option('date_format'), strtotime($booking_date)) . ' ' . $booking_time);
                break;
                
            case 'guests':
                $adults = get_post_meta($post_id, '_adults', true);
                $children = get_post_meta($post_id, '_children', true);
                $infants = get_post_meta($post_id, '_infants', true);
                echo sprintf(
                    __('%d Adults, %d Children, %d Infants', 'ultimate-tours-manager'),
                    $adults,
                    $children,
                    $infants
                );
                break;
                
            case 'total':
                $total = get_post_meta($post_id, '_total_price', true);
                $currency = get_option('utm_currency', 'USD');
                echo esc_html($currency . ' ' . number_format($total, 2));
                break;
                
            case 'status':
                $status = get_post_meta($post_id, '_booking_status', true);
                $status = $status ? $status : 'pending';
                $status_labels = array(
                    'pending' => __('Pending', 'ultimate-tours-manager'),
                    'confirmed' => __('Confirmed', 'ultimate-tours-manager'),
                    'cancelled' => __('Cancelled', 'ultimate-tours-manager'),
                    'completed' => __('Completed', 'ultimate-tours-manager'),
                );
                echo sprintf(
                    '<span class="booking-status status-%s">%s</span>',
                    $status,
                    isset($status_labels[$status]) ? $status_labels[$status] : $status
                );
                break;
                
            case 'payment':
                $payment_status = get_post_meta($post_id, '_payment_status', true);
                $payment_status = $payment_status ? $payment_status : 'unpaid';
                $payment_labels = array(
                    'unpaid' => __('Unpaid', 'ultimate-tours-manager'),
                    'paid' => __('Paid', 'ultimate-tours-manager'),
                    'refunded' => __('Refunded', 'ultimate-tours-manager'),
                    'partial' => __('Partial', 'ultimate-tours-manager'),
                );
                echo sprintf(
                    '<span class="payment-status status-%s">%s</span>',
                    $payment_status,
                    isset($payment_labels[$payment_status]) ? $payment_labels[$payment_status] : $payment_status
                );
                break;
        }
    }
    
    /**
     * Tour sortable columns
     */
    public function tour_sortable_columns($columns) {
        $columns['price'] = 'price';
        $columns['reviews'] = 'rating';
        $columns['bookings'] = 'bookings';
        $columns['date'] = 'date';
        
        return $columns;
    }
    
    /**
     * Booking sortable columns
     */
    public function booking_sortable_columns($columns) {
        $columns['booking_number'] = 'booking_number';
        $columns['date'] = 'booking_date';
        $columns['total'] = 'total';
        
        return $columns;
    }
    
    /**
     * Tour row actions
     */
    public function tour_row_actions($actions, $post) {
        if ($post->post_type === 'tour') {
            $actions['duplicate'] = sprintf(
                '<a href="%s" title="%s">%s</a>',
                wp_nonce_url(admin_url('admin.php?action=utm_duplicate_tour&amp;post=' . $post->ID), 'utm_duplicate_tour_' . $post->ID),
                __('Duplicate this tour', 'ultimate-tours-manager'),
                __('Duplicate', 'ultimate-tours-manager')
            );
            
            $actions['view_bookings'] = sprintf(
                '<a href="%s">%s</a>',
                admin_url('edit.php?post_type=booking&amp;tour_id=' . $post->ID),
                __('View Bookings', 'ultimate-tours-manager')
            );
        }
        
        return $actions;
    }
    
    /**
     * Booking row actions
     */
    public function booking_row_actions($actions, $post) {
        if ($post->post_type === 'booking') {
            $actions['invoice'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(admin_url('admin.php?action=utm_generate_invoice&amp;post=' . $post->ID), 'utm_generate_invoice_' . $post->ID),
                __('Generate Invoice', 'ultimate-tours-manager')
            );
            
            $actions['send_email'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(admin_url('admin.php?action=utm_send_confirmation_email&amp;post=' . $post->ID), 'utm_send_confirmation_email_' . $post->ID),
                __('Send Email', 'ultimate-tours-manager')
            );
        }
        
        return $actions;
    }
    
    /**
     * Tour bulk actions
     */
    public function tour_bulk_actions($actions) {
        $actions['utm_activate'] = __('Activate', 'ultimate-tours-manager');
        $actions['utm_deactivate'] = __('Deactivate', 'ultimate-tours-manager');
        $actions['utm_mark_sold_out'] = __('Mark as Sold Out', 'ultimate-tours-manager');
        $actions['utm_duplicate'] = __('Duplicate', 'ultimate-tours-manager');
        
        return $actions;
    }
    
    /**
     * Booking bulk actions
     */
    public function booking_bulk_actions($actions) {
        $actions['utm_confirm'] = __('Confirm', 'ultimate-tours-manager');
        $actions['utm_cancel'] = __('Cancel', 'ultimate-tours-manager');
        $actions['utm_complete'] = __('Complete', 'ultimate-tours-manager');
        $actions['utm_send_reminders'] = __('Send Reminders', 'ultimate-tours-manager');
        
        return $actions;
    }
    
    /**
     * Handle tour bulk actions
     */
    public function handle_tour_bulk_actions($redirect_to, $doaction, $post_ids) {
        if (!in_array($doaction, array('utm_activate', 'utm_deactivate', 'utm_mark_sold_out', 'utm_duplicate'))) {
            return $redirect_to;
        }
        
        $count = 0;
        
        foreach ($post_ids as $post_id) {
            switch ($doaction) {
                case 'utm_activate':
                    update_post_meta($post_id, '_tour_status', 'active');
                    $count++;
                    break;
                    
                case 'utm_deactivate':
                    update_post_meta($post_id, '_tour_status', 'inactive');
                    $count++;
                    break;
                    
                case 'utm_mark_sold_out':
                    update_post_meta($post_id, '_tour_status', 'sold_out');
                    $count++;
                    break;
                    
                case 'utm_duplicate':
                    // Duplicate tour logic
                    $count++;
                    break;
            }
        }
        
        $redirect_to = add_query_arg('bulk_' . $doaction . '_count', $count, $redirect_to);
        return $redirect_to;
    }
    
    /**
     * Handle booking bulk actions
     */
    public function handle_booking_bulk_actions($redirect_to, $doaction, $post_ids) {
        if (!in_array($doaction, array('utm_confirm', 'utm_cancel', 'utm_complete', 'utm_send_reminders'))) {
            return $redirect_to;
        }
        
        $count = 0;
        
        foreach ($post_ids as $post_id) {
            switch ($doaction) {
                case 'utm_confirm':
                    update_post_meta($post_id, '_booking_status', 'confirmed');
                    $count++;
                    break;
                    
                case 'utm_cancel':
                    update_post_meta($post_id, '_booking_status', 'cancelled');
                    $count++;
                    break;
                    
                case 'utm_complete':
                    update_post_meta($post_id, '_booking_status', 'completed');
                    $count++;
                    break;
                    
                case 'utm_send_reminders':
                    // Send reminder logic
                    $count++;
                    break;
            }
        }
        
        $redirect_to = add_query_arg('bulk_' . $doaction . '_count', $count, $redirect_to);
        return $redirect_to;
    }
}
