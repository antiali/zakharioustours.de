<?php
/**
 * UTM API Class - REST API Endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_API {
    
    private static $instance = null;
    private $namespace = 'utm/v1';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    public function register_routes() {
        // Tours
        register_rest_route($this->namespace, '/tours', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tours'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route($this->namespace, '/tours/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tour'),
            'permission_callback' => '__return_true',
        ));
        
        // Bookings
        register_rest_route($this->namespace, '/bookings', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_bookings'),
                'permission_callback' => array($this, 'check_admin_permission'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'create_booking'),
                'permission_callback' => '__return_true',
            ),
        ));
        
        register_rest_route($this->namespace, '/bookings/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_booking'),
            'permission_callback' => array($this, 'check_booking_permission'),
        ));
        
        // Destinations
        register_rest_route($this->namespace, '/destinations', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_destinations'),
            'permission_callback' => '__return_true',
        ));
        
        // Search
        register_rest_route($this->namespace, '/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_tours'),
            'permission_callback' => '__return_true',
        ));
        
        // Reviews
        register_rest_route($this->namespace, '/reviews', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_reviews'),
                'permission_callback' => '__return_true',
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'create_review'),
                'permission_callback' => 'is_user_logged_in',
            ),
        ));
        
        // Availability
        register_rest_route($this->namespace, '/availability/(?P<tour_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_availability'),
            'permission_callback' => '__return_true',
        ));
        
        // Calculate Price
        register_rest_route($this->namespace, '/calculate-price', array(
            'methods' => 'POST',
            'callback' => array($this, 'calculate_price'),
            'permission_callback' => '__return_true',
        ));
    }
    
    public function get_tours($request) {
        $params = $request->get_params();
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => isset($params['per_page']) ? absint($params['per_page']) : 10,
            'paged' => isset($params['page']) ? absint($params['page']) : 1,
            'orderby' => isset($params['orderby']) ? $params['orderby'] : 'date',
            'order' => isset($params['order']) ? $params['order'] : 'DESC',
        );
        
        if (isset($params['destination'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
                'field' => 'slug',
                'terms' => $params['destination'],
            );
        }
        
        if (isset($params['type'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tour-type',
                'field' => 'slug',
                'terms' => $params['type'],
            );
        }
        
        $query = new WP_Query($args);
        $tours = array();
        
        foreach ($query->posts as $post) {
            $tours[] = $this->prepare_tour_response($post);
        }
        
        return new WP_REST_Response(array(
            'tours' => $tours,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
        ), 200);
    }
    
    public function get_tour($request) {
        $tour_id = $request['id'];
        $post = get_post($tour_id);
        
        if (!$post || $post->post_type !== 'tour') {
            return new WP_Error('not_found', __('Tour not found', 'ultimate-tours-manager'), array('status' => 404));
        }
        
        return new WP_REST_Response($this->prepare_tour_response($post, true), 200);
    }
    
    public function get_bookings($request) {
        $args = array(
            'post_type' => 'booking',
            'posts_per_page' => -1,
        );
        
        $query = new WP_Query($args);
        $bookings = array();
        
        foreach ($query->posts as $post) {
            $bookings[] = $this->prepare_booking_response($post);
        }
        
        return new WP_REST_Response($bookings, 200);
    }
    
    public function create_booking($request) {
        $params = $request->get_json_params();
        
        // Validate required fields
        $required = array('tour_id', 'first_name', 'last_name', 'email', 'phone', 'booking_date', 'adults');
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return new WP_Error('missing_field', sprintf(__('Missing required field: %s', 'ultimate-tours-manager'), $field), array('status' => 400));
            }
        }
        
        // Create booking
        $booking_number = 'UTM-' . strtoupper(wp_generate_password(8, false));
        
        $booking_id = wp_insert_post(array(
            'post_type' => 'booking',
            'post_title' => $booking_number,
            'post_status' => 'publish',
        ));
        
        if (is_wp_error($booking_id)) {
            return $booking_id;
        }
        
        // Save booking meta
        update_post_meta($booking_id, '_booking_number', $booking_number);
        update_post_meta($booking_id, '_tour_id', absint($params['tour_id']));
        update_post_meta($booking_id, '_first_name', sanitize_text_field($params['first_name']));
        update_post_meta($booking_id, '_last_name', sanitize_text_field($params['last_name']));
        update_post_meta($booking_id, '_email', sanitize_email($params['email']));
        update_post_meta($booking_id, '_phone', sanitize_text_field($params['phone']));
        update_post_meta($booking_id, '_booking_date', sanitize_text_field($params['booking_date']));
        update_post_meta($booking_id, '_adults', absint($params['adults']));
        update_post_meta($booking_id, '_children', isset($params['children']) ? absint($params['children']) : 0);
        update_post_meta($booking_id, '_infants', isset($params['infants']) ? absint($params['infants']) : 0);
        update_post_meta($booking_id, '_booking_status', 'pending');
        update_post_meta($booking_id, '_payment_status', 'unpaid');
        
        // Calculate total
        $total = $this->calculate_booking_total($params);
        update_post_meta($booking_id, '_total_price', $total);
        
        // Send notification
        do_action('utm_booking_created', $booking_id);
        
        return new WP_REST_Response(array(
            'success' => true,
            'booking_id' => $booking_id,
            'booking_number' => $booking_number,
            'total' => $total,
        ), 201);
    }
    
    public function get_booking($request) {
        $booking_id = $request['id'];
        $post = get_post($booking_id);
        
        if (!$post || $post->post_type !== 'booking') {
            return new WP_Error('not_found', __('Booking not found', 'ultimate-tours-manager'), array('status' => 404));
        }
        
        return new WP_REST_Response($this->prepare_booking_response($post), 200);
    }
    
    public function get_destinations($request) {
        $destinations = get_terms(array(
            'taxonomy' => 'destination',
            'hide_empty' => false,
        ));
        
        $data = array();
        foreach ($destinations as $destination) {
            $data[] = array(
                'id' => $destination->term_id,
                'name' => $destination->name,
                'slug' => $destination->slug,
                'description' => $destination->description,
                'count' => $destination->count,
                'image' => wp_get_attachment_image_url(get_term_meta($destination->term_id, 'taxonomy_image_id', true), 'medium'),
            );
        }
        
        return new WP_REST_Response($data, 200);
    }
    
    public function search_tours($request) {
        $params = $request->get_params();
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => 20,
            's' => isset($params['q']) ? $params['q'] : '',
        );
        
        if (isset($params['destination'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
                'field' => 'slug',
                'terms' => $params['destination'],
            );
        }
        
        if (isset($params['min_price'])) {
            $args['meta_query'][] = array(
                'key' => 'utm_tour_meta_price',
                'value' => floatval($params['min_price']),
                'compare' => '>=',
                'type' => 'DECIMAL',
            );
        }
        
        if (isset($params['max_price'])) {
            $args['meta_query'][] = array(
                'key' => 'utm_tour_meta_price',
                'value' => floatval($params['max_price']),
                'compare' => '<=',
                'type' => 'DECIMAL',
            );
        }
        
        $query = new WP_Query($args);
        $tours = array();
        
        foreach ($query->posts as $post) {
            $tours[] = $this->prepare_tour_response($post);
        }
        
        return new WP_REST_Response($tours, 200);
    }
    
    public function get_availability($request) {
        $tour_id = $request['tour_id'];
        $month = isset($request['month']) ? $request['month'] : date('Y-m');
        
        $available_dates = get_post_meta($tour_id, 'utm_tour_meta_available_dates', true);
        $max_guests = get_post_meta($tour_id, 'utm_tour_meta_max_guests', true);
        
        // Get existing bookings for this tour
        $bookings = get_posts(array(
            'post_type' => 'booking',
            'posts_per_page' => -1,
            'meta_query' => array(
                array('key' => '_tour_id', 'value' => $tour_id),
                array('key' => '_booking_status', 'value' => array('pending', 'confirmed'), 'compare' => 'IN'),
            ),
        ));
        
        $booked_spots = array();
        foreach ($bookings as $booking) {
            $date = get_post_meta($booking->ID, '_booking_date', true);
            $adults = get_post_meta($booking->ID, '_adults', true);
            $children = get_post_meta($booking->ID, '_children', true);
            
            if (!isset($booked_spots[$date])) {
                $booked_spots[$date] = 0;
            }
            $booked_spots[$date] += $adults + $children;
        }
        
        return new WP_REST_Response(array(
            'available_dates' => $available_dates,
            'max_guests' => $max_guests,
            'booked_spots' => $booked_spots,
        ), 200);
    }
    
    public function calculate_price($request) {
        $params = $request->get_json_params();
        
        $tour_id = absint($params['tour_id']);
        $adults = absint($params['adults']);
        $children = isset($params['children']) ? absint($params['children']) : 0;
        $infants = isset($params['infants']) ? absint($params['infants']) : 0;
        
        $price = floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true));
        $sale_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true));
        $child_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_child_price', true));
        $infant_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_infant_price', true));
        
        $adult_price = ($sale_price && $sale_price < $price) ? $sale_price : $price;
        
        $total = ($adult_price * $adults) + ($child_price * $children) + ($infant_price * $infants);
        
        return new WP_REST_Response(array(
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'adult_price' => $adult_price,
            'child_price' => $child_price,
            'infant_price' => $infant_price,
            'subtotal' => $total,
            'total' => $total,
            'formatted_total' => utm_format_price($total),
        ), 200);
    }
    
    private function prepare_tour_response($post, $detailed = false) {
        $tour_id = $post->ID;
        
        $data = array(
            'id' => $tour_id,
            'title' => $post->post_title,
            'slug' => $post->post_name,
            'excerpt' => get_the_excerpt($post),
            'url' => get_permalink($tour_id),
            'image' => get_the_post_thumbnail_url($tour_id, 'large'),
            'price' => floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true)),
            'sale_price' => floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true)),
            'duration' => get_post_meta($tour_id, 'utm_tour_meta_duration_value', true),
            'duration_unit' => get_post_meta($tour_id, 'utm_tour_meta_duration_unit', true),
            'rating' => floatval(get_post_meta($tour_id, '_tour_average_rating', true)),
            'review_count' => intval(get_post_meta($tour_id, '_tour_review_count', true)),
        );
        
        // Add destinations
        $destinations = get_the_terms($tour_id, 'destination');
        $data['destinations'] = array();
        if ($destinations && !is_wp_error($destinations)) {
            foreach ($destinations as $dest) {
                $data['destinations'][] = array('id' => $dest->term_id, 'name' => $dest->name, 'slug' => $dest->slug);
            }
        }
        
        if ($detailed) {
            $data['content'] = apply_filters('the_content', $post->post_content);
            $data['gallery'] = get_post_meta($tour_id, 'utm_tour_meta_gallery', true);
            $data['itinerary'] = get_post_meta($tour_id, 'utm_tour_meta_itinerary', true);
            $data['inclusions'] = get_post_meta($tour_id, 'utm_tour_meta_inclusions', true);
            $data['exclusions'] = get_post_meta($tour_id, 'utm_tour_meta_exclusions', true);
            $data['faqs'] = get_post_meta($tour_id, 'utm_tour_meta_faqs', true);
            $data['location'] = get_post_meta($tour_id, 'utm_tour_meta_location', true);
            $data['max_guests'] = get_post_meta($tour_id, 'utm_tour_meta_max_guests', true);
        }
        
        return $data;
    }
    
    private function prepare_booking_response($post) {
        $booking_id = $post->ID;
        
        return array(
            'id' => $booking_id,
            'booking_number' => get_post_meta($booking_id, '_booking_number', true),
            'tour_id' => get_post_meta($booking_id, '_tour_id', true),
            'customer' => array(
                'first_name' => get_post_meta($booking_id, '_first_name', true),
                'last_name' => get_post_meta($booking_id, '_last_name', true),
                'email' => get_post_meta($booking_id, '_email', true),
                'phone' => get_post_meta($booking_id, '_phone', true),
            ),
            'booking_date' => get_post_meta($booking_id, '_booking_date', true),
            'guests' => array(
                'adults' => get_post_meta($booking_id, '_adults', true),
                'children' => get_post_meta($booking_id, '_children', true),
                'infants' => get_post_meta($booking_id, '_infants', true),
            ),
            'total' => get_post_meta($booking_id, '_total_price', true),
            'status' => get_post_meta($booking_id, '_booking_status', true),
            'payment_status' => get_post_meta($booking_id, '_payment_status', true),
            'created_at' => $post->post_date,
        );
    }
    
    private function calculate_booking_total($params) {
        $tour_id = absint($params['tour_id']);
        $adults = absint($params['adults']);
        $children = isset($params['children']) ? absint($params['children']) : 0;
        $infants = isset($params['infants']) ? absint($params['infants']) : 0;
        
        $price = floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true));
        $sale_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true));
        $child_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_child_price', true));
        $infant_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_infant_price', true));
        
        $adult_price = ($sale_price && $sale_price < $price) ? $sale_price : $price;
        
        return ($adult_price * $adults) + ($child_price * $children) + ($infant_price * $infants);
    }
    
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }
    
    public function check_booking_permission($request) {
        if (current_user_can('manage_options')) {
            return true;
        }
        
        $booking_id = $request['id'];
        $email = get_post_meta($booking_id, '_email', true);
        $current_user = wp_get_current_user();
        
        return $current_user->user_email === $email;
    }
}
