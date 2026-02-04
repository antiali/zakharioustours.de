<?php
/**
 * UTM AJAX Handler Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Ajax {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Public AJAX actions
        add_action('wp_ajax_utm_filter_tours', array($this, 'filter_tours'));
        add_action('wp_ajax_nopriv_utm_filter_tours', array($this, 'filter_tours'));
        
        add_action('wp_ajax_utm_load_more_tours', array($this, 'load_more_tours'));
        add_action('wp_ajax_nopriv_utm_load_more_tours', array($this, 'load_more_tours'));
        
        add_action('wp_ajax_utm_check_availability', array($this, 'check_availability'));
        add_action('wp_ajax_nopriv_utm_check_availability', array($this, 'check_availability'));
        
        add_action('wp_ajax_utm_calculate_price', array($this, 'calculate_price'));
        add_action('wp_ajax_nopriv_utm_calculate_price', array($this, 'calculate_price'));
        
        add_action('wp_ajax_utm_submit_booking', array($this, 'submit_booking'));
        add_action('wp_ajax_nopriv_utm_submit_booking', array($this, 'submit_booking'));
        
        add_action('wp_ajax_utm_submit_enquiry', array($this, 'submit_enquiry'));
        add_action('wp_ajax_nopriv_utm_submit_enquiry', array($this, 'submit_enquiry'));
        
        add_action('wp_ajax_utm_submit_review', array($this, 'submit_review'));
        
        add_action('wp_ajax_utm_add_to_wishlist', array($this, 'add_to_wishlist'));
        add_action('wp_ajax_nopriv_utm_add_to_wishlist', array($this, 'add_to_wishlist'));
        
        add_action('wp_ajax_utm_search_tours', array($this, 'search_tours'));
        add_action('wp_ajax_nopriv_utm_search_tours', array($this, 'search_tours'));
    }
    
    public function filter_tours() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => 12,
            'paged' => isset($_POST['page']) ? absint($_POST['page']) : 1,
        );
        
        // Destination filter
        if (!empty($_POST['destination'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
                'field' => 'slug',
                'terms' => sanitize_text_field($_POST['destination']),
            );
        }
        
        // Tour type filter
        if (!empty($_POST['tour_type'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tour-type',
                'field' => 'slug',
                'terms' => sanitize_text_field($_POST['tour_type']),
            );
        }
        
        // Price range filter
        if (!empty($_POST['min_price'])) {
            $args['meta_query'][] = array(
                'key' => 'utm_tour_meta_price',
                'value' => floatval($_POST['min_price']),
                'compare' => '>=',
                'type' => 'DECIMAL',
            );
        }
        
        if (!empty($_POST['max_price'])) {
            $args['meta_query'][] = array(
                'key' => 'utm_tour_meta_price',
                'value' => floatval($_POST['max_price']),
                'compare' => '<=',
                'type' => 'DECIMAL',
            );
        }
        
        // Duration filter
        if (!empty($_POST['duration'])) {
            $args['meta_query'][] = array(
                'key' => 'utm_tour_meta_duration_value',
                'value' => absint($_POST['duration']),
                'compare' => '<=',
            );
        }
        
        // Sorting
        if (!empty($_POST['orderby'])) {
            switch ($_POST['orderby']) {
                case 'price_low':
                    $args['meta_key'] = 'utm_tour_meta_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'ASC';
                    break;
                case 'price_high':
                    $args['meta_key'] = 'utm_tour_meta_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'rating':
                    $args['meta_key'] = '_tour_average_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'popularity':
                    $args['meta_key'] = '_tour_booking_count';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                default:
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
            }
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include UTM_PLUGIN_DIR . 'templates/tour-card.php';
            }
        } else {
            echo '<div class="utm-no-results">' . __('No tours found matching your criteria.', 'ultimate-tours-manager') . '</div>';
        }
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'found_posts' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        ));
    }
    
    public function load_more_tours() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        $page = isset($_POST['page']) ? absint($_POST['page']) : 2;
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => 12,
            'paged' => $page,
        );
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include UTM_PLUGIN_DIR . 'templates/tour-card.php';
            }
        }
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => $page < $query->max_num_pages,
        ));
    }
    
    public function check_availability() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        $tour_id = absint($_POST['tour_id']);
        $date = sanitize_text_field($_POST['date']);
        $guests = absint($_POST['guests']);
        
        $max_guests = get_post_meta($tour_id, 'utm_tour_meta_max_guests', true);
        
        // Get existing bookings for this date
        global $wpdb;
        $booked = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(m1.meta_value + m2.meta_value) 
            FROM {$wpdb->postmeta} m1
            JOIN {$wpdb->postmeta} m2 ON m1.post_id = m2.post_id
            JOIN {$wpdb->postmeta} m3 ON m1.post_id = m3.post_id
            JOIN {$wpdb->postmeta} m4 ON m1.post_id = m4.post_id
            WHERE m1.meta_key = '_adults'
            AND m2.meta_key = '_children'
            AND m3.meta_key = '_tour_id' AND m3.meta_value = %d
            AND m4.meta_key = '_booking_date' AND m4.meta_value = %s",
            $tour_id,
            $date
        ));
        
        $available_spots = $max_guests - ($booked ? $booked : 0);
        
        $is_available = $available_spots >= $guests;
        
        wp_send_json_success(array(
            'available' => $is_available,
            'available_spots' => $available_spots,
            'message' => $is_available 
                ? sprintf(__('%d spots available', 'ultimate-tours-manager'), $available_spots)
                : __('Not enough spots available', 'ultimate-tours-manager'),
        ));
    }
    
    public function calculate_price() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        $tour_id = absint($_POST['tour_id']);
        $adults = absint($_POST['adults']);
        $children = isset($_POST['children']) ? absint($_POST['children']) : 0;
        $infants = isset($_POST['infants']) ? absint($_POST['infants']) : 0;
        
        $price = floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true));
        $sale_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true));
        $child_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_child_price', true));
        $infant_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_infant_price', true));
        
        $adult_price = ($sale_price && $sale_price < $price) ? $sale_price : $price;
        
        $adults_total = $adult_price * $adults;
        $children_total = $child_price * $children;
        $infants_total = $infant_price * $infants;
        
        $subtotal = $adults_total + $children_total + $infants_total;
        $total = $subtotal;
        
        // Apply tax if enabled
        $options = get_option('utm_options');
        $tax = 0;
        
        if (!empty($options['enable_tax']) && !empty($options['tax_rate'])) {
            $tax = $subtotal * ($options['tax_rate'] / 100);
            $total = $subtotal + $tax;
        }
        
        wp_send_json_success(array(
            'adults_total' => utm_format_price($adults_total),
            'children_total' => utm_format_price($children_total),
            'infants_total' => utm_format_price($infants_total),
            'subtotal' => utm_format_price($subtotal),
            'tax' => utm_format_price($tax),
            'total' => utm_format_price($total),
            'total_raw' => $total,
        ));
    }
    
    public function submit_booking() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        // Validate required fields
        $required = array('tour_id', 'first_name', 'last_name', 'email', 'phone', 'booking_date', 'adults');
        
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                wp_send_json_error(array('message' => sprintf(__('Please fill in %s', 'ultimate-tours-manager'), $field)));
            }
        }
        
        // Validate email
        if (!is_email($_POST['email'])) {
            wp_send_json_error(array('message' => __('Please enter a valid email address', 'ultimate-tours-manager')));
        }
        
        $tour_id = absint($_POST['tour_id']);
        $booking_number = 'UTM-' . strtoupper(wp_generate_password(8, false));
        
        // Create booking
        $booking_id = wp_insert_post(array(
            'post_type' => 'booking',
            'post_title' => $booking_number,
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($booking_id)) {
            wp_send_json_error(array('message' => __('Failed to create booking', 'ultimate-tours-manager')));
        }
        
        // Save booking data
        $adults = absint($_POST['adults']);
        $children = isset($_POST['children']) ? absint($_POST['children']) : 0;
        $infants = isset($_POST['infants']) ? absint($_POST['infants']) : 0;
        
        update_post_meta($booking_id, '_booking_number', $booking_number);
        update_post_meta($booking_id, '_tour_id', $tour_id);
        update_post_meta($booking_id, '_first_name', sanitize_text_field($_POST['first_name']));
        update_post_meta($booking_id, '_last_name', sanitize_text_field($_POST['last_name']));
        update_post_meta($booking_id, '_email', sanitize_email($_POST['email']));
        update_post_meta($booking_id, '_phone', sanitize_text_field($_POST['phone']));
        update_post_meta($booking_id, '_booking_date', sanitize_text_field($_POST['booking_date']));
        update_post_meta($booking_id, '_adults', $adults);
        update_post_meta($booking_id, '_children', $children);
        update_post_meta($booking_id, '_infants', $infants);
        update_post_meta($booking_id, '_booking_status', 'pending');
        update_post_meta($booking_id, '_payment_status', 'unpaid');
        
        if (!empty($_POST['special_requirements'])) {
            update_post_meta($booking_id, '_special_requirements', sanitize_textarea_field($_POST['special_requirements']));
        }
        
        // Calculate and save total
        $price = floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true));
        $sale_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true));
        $child_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_child_price', true));
        $infant_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_infant_price', true));
        
        $adult_price = ($sale_price && $sale_price < $price) ? $sale_price : $price;
        $total = ($adult_price * $adults) + ($child_price * $children) + ($infant_price * $infants);
        
        update_post_meta($booking_id, '_total_price', $total);
        
        // Update tour booking count
        $booking_count = get_post_meta($tour_id, '_tour_booking_count', true);
        update_post_meta($tour_id, '_tour_booking_count', intval($booking_count) + 1);
        
        // Trigger booking created action
        do_action('utm_booking_created', $booking_id);
        
        wp_send_json_success(array(
            'message' => __('Booking submitted successfully!', 'ultimate-tours-manager'),
            'booking_id' => $booking_id,
            'booking_number' => $booking_number,
            'redirect' => '', // Payment page URL if needed
        ));
    }
    
    public function submit_enquiry() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        // Validate
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
            wp_send_json_error(array('message' => __('Please fill in all required fields', 'ultimate-tours-manager')));
        }
        
        if (!is_email($_POST['email'])) {
            wp_send_json_error(array('message' => __('Please enter a valid email address', 'ultimate-tours-manager')));
        }
        
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'utm_enquiries',
            array(
                'tour_id' => absint($_POST['tour_id']),
                'name' => sanitize_text_field($_POST['name']),
                'email' => sanitize_email($_POST['email']),
                'phone' => sanitize_text_field($_POST['phone']),
                'subject' => sanitize_text_field($_POST['subject']),
                'message' => sanitize_textarea_field($_POST['message']),
                'status' => 'unread',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            )
        );
        
        if ($result) {
            // Send email notification
            do_action('utm_enquiry_submitted', $wpdb->insert_id);
            
            wp_send_json_success(array('message' => __('Enquiry sent successfully!', 'ultimate-tours-manager')));
        } else {
            wp_send_json_error(array('message' => __('Failed to send enquiry', 'ultimate-tours-manager')));
        }
    }
    
    public function submit_review() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please login to submit a review', 'ultimate-tours-manager')));
        }
        
        $tour_id = absint($_POST['tour_id']);
        $rating = absint($_POST['rating']);
        $content = sanitize_textarea_field($_POST['content']);
        
        if ($rating < 1 || $rating > 5) {
            wp_send_json_error(array('message' => __('Invalid rating', 'ultimate-tours-manager')));
        }
        
        if (empty($content)) {
            wp_send_json_error(array('message' => __('Please write a review', 'ultimate-tours-manager')));
        }
        
        global $wpdb;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'utm_reviews',
            array(
                'tour_id' => $tour_id,
                'user_id' => get_current_user_id(),
                'rating' => $rating,
                'title' => sanitize_text_field($_POST['title']),
                'content' => $content,
                'status' => 'pending',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            )
        );
        
        if ($result) {
            do_action('utm_review_submitted', $wpdb->insert_id);
            
            wp_send_json_success(array('message' => __('Review submitted successfully! It will be visible after approval.', 'ultimate-tours-manager')));
        } else {
            wp_send_json_error(array('message' => __('Failed to submit review', 'ultimate-tours-manager')));
        }
    }
    
    public function add_to_wishlist() {
        $tour_id = absint($_POST['tour_id']);
        
        if (!is_user_logged_in()) {
            // Store in cookie for guests
            $wishlist = isset($_COOKIE['utm_wishlist']) ? json_decode(stripslashes($_COOKIE['utm_wishlist']), true) : array();
            
            if (in_array($tour_id, $wishlist)) {
                $wishlist = array_diff($wishlist, array($tour_id));
                $added = false;
            } else {
                $wishlist[] = $tour_id;
                $added = true;
            }
            
            setcookie('utm_wishlist', json_encode($wishlist), time() + (86400 * 30), '/');
        } else {
            $user_id = get_current_user_id();
            $wishlist = get_user_meta($user_id, 'utm_wishlist', true);
            $wishlist = $wishlist ? $wishlist : array();
            
            if (in_array($tour_id, $wishlist)) {
                $wishlist = array_diff($wishlist, array($tour_id));
                $added = false;
            } else {
                $wishlist[] = $tour_id;
                $added = true;
            }
            
            update_user_meta($user_id, 'utm_wishlist', $wishlist);
        }
        
        wp_send_json_success(array(
            'added' => $added,
            'message' => $added ? __('Added to wishlist', 'ultimate-tours-manager') : __('Removed from wishlist', 'ultimate-tours-manager'),
        ));
    }
    
    public function search_tours() {
        $query = sanitize_text_field($_POST['query']);
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => 10,
            's' => $query,
        );
        
        $tours = new WP_Query($args);
        $results = array();
        
        while ($tours->have_posts()) {
            $tours->the_post();
            
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'url' => get_permalink(),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'price' => utm_format_price(get_post_meta(get_the_ID(), 'utm_tour_meta_price', true)),
            );
        }
        
        wp_reset_postdata();
        
        wp_send_json_success(array('results' => $results));
    }
}

// Initialize
UTM_Ajax::get_instance();
