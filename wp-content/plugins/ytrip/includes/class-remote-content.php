<?php
/**
 * YTrip REST API
 * Provides remote access to YTrip functionality
 * @package YTrip
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', 'ytrip_register_rest_routes');

function ytrip_register_rest_routes() {
    register_rest_route('ytrip/v1', '/create-content', array(
        'methods' => 'POST',
        'callback' => 'ytrip_rest_create_content',
        'permission_callback' => 'ytrip_rest_permission_check',
    ));

    register_rest_route('ytrip/v1', '/test-access', array(
        'methods' => 'GET',
        'callback' => 'ytrip_rest_test_access',
        'permission_callback' => '__return_true',
    ));
}

function ytrip_rest_permission_check() {
    // Check for application password authentication
    $current_user = wp_get_current_user();

    // If no user is authenticated, return false
    if (!$current_user) {
        return new WP_Error('rest_forbidden', 'Authentication required', array('status' => 401));
    }

    // Check if user can edit posts
    if (!current_user_can('edit_posts')) {
        return new WP_Error('rest_forbidden', 'You do not have permission to create content', array('status' => 403));
    }

    return true;
}

function ytrip_rest_test_access() {
    return array(
        'status' => 'success',
        'message' => 'YTrip REST API is working',
        'user' => wp_get_current_user() ? wp_get_current_user()->user_login : 'Not authenticated',
        'time' => current_time('mysql'),
    );
}

function ytrip_rest_create_content($request) {
    $params = $request->get_json_params();
    $num_categories = isset($params['num_categories']) ? intval($params['num_categories']) : 4;
    $num_tours = isset($params['num_tours']) ? intval($params['num_tours']) : 4;

    // Categories Data
    $categories = array(
        array('name' => 'Adventure Tours', 'description' => 'Exciting adventures for thrill-seekers'),
        array('name' => 'Cultural Experiences', 'description' => 'Immerse yourself in local culture'),
        array('name' => 'Beach & Relaxation', 'description' => 'Sun, sand, and pure relaxation'),
        array('name' => 'City Breaks', 'description' => 'Explore vibrant cities and urban life'),
        array('name' => 'Nature & Wildlife', 'description' => 'Discover amazing wildlife and natural wonders'),
        array('name' => 'Food & Culinary', 'description' => 'Taste local cuisine and delicacies'),
        array('name' => 'Historical Tours', 'description' => 'Journey through history and heritage'),
        array('name' => 'Luxury Travel', 'description' => 'Premium experiences and exclusive access'),
    );

    // Destinations Data
    $destinations = array(
        array('name' => 'Germany', 'description' => 'Beautiful landscapes and rich history'),
        array('name' => 'Egypt', 'description' => 'Ancient wonders and beautiful beaches'),
        array('name' => 'France', 'description' => 'Romantic cities and exquisite cuisine'),
        array('name' => 'Japan', 'description' => 'Unique culture and stunning scenery'),
        array('name' => 'Italy', 'description' => 'Art, history, and incredible food'),
        array('name' => 'Spain', 'description' => 'Vibrant culture and sunny beaches'),
        array('name' => 'Greece', 'description' => 'Ancient ruins and crystal waters'),
        array('name' => 'Thailand', 'description' => 'Tropical paradise and Buddhist temples'),
    );

    // Tour Data Templates
    $tour_templates = array(
        array(
            'title' => 'Amazing {destination} Adventure',
            'duration' => 7,
            'price' => 1299,
            'description' => 'Experience the best of {destination} with this incredible adventure tour. Visit iconic landmarks, taste local cuisine, and create unforgettable memories.',
        ),
        array(
            'title' => '{destination} Cultural Journey',
            'duration' => 10,
            'price' => 1899,
            'description' => 'Immerse yourself in the rich culture of {destination}. This comprehensive tour takes you to museums, historical sites, and hidden gems.',
        ),
        array(
            'title' => 'Ultimate {destination} Experience',
            'duration' => 14,
            'price' => 2499,
            'description' => 'The complete {destination} experience. From bustling cities to peaceful countryside, discover everything this amazing destination has to offer.',
        ),
        array(
            'title' => '{destination} Explorer Package',
            'duration' => 5,
            'price' => 899,
            'description' => 'A quick but comprehensive tour of {destination}. Perfect for those with limited time who still want to see the highlights.',
        ),
    );

    $results = array();
    $results['categories'] = array();
    $results['destinations'] = array();
    $results['tours'] = array();
    $category_ids = array();

    // Create Categories
    for ($i = 0; $i < min($num_categories, count($categories)); $i++) {
        $cat = $categories[$i];
        $term = wp_insert_term($cat['name'], 'ytrip_category', array(
            'description' => $cat['description'],
            'slug' => sanitize_title($cat['name']),
        ));

        if (!is_wp_error($term)) {
            $category_ids[] = $term['term_id'];
            $results['categories'][] = array(
                'name' => $cat['name'],
                'status' => 'success',
                'term_id' => $term['term_id'],
            );
        } else {
            $results['categories'][] = array(
                'name' => $cat['name'],
                'status' => 'error',
                'message' => $term->get_error_message(),
            );
        }
    }

    // Create/Check Destinations
    foreach ($destinations as $dest) {
        $existing = get_term_by('name', $dest['name'], 'ytrip_destination');
        if (!$existing) {
            $term = wp_insert_term($dest['name'], 'ytrip_destination', array(
                'description' => $dest['description'],
                'slug' => sanitize_title($dest['name']),
            ));
            if (!is_wp_error($term)) {
                $dest_id = $term['term_id'];
                $results['destinations'][] = array(
                    'name' => $dest['name'],
                    'status' => 'success',
                    'term_id' => $term['term_id'],
                );
            } else {
                $results['destinations'][] = array(
                    'name' => $dest['name'],
                    'status' => 'error',
                    'message' => $term->get_error_message(),
                );
                continue;
            }
        } else {
            $dest_id = $existing->term_id;
            $results['destinations'][] = array(
                'name' => $dest['name'],
                'status' => 'exists',
                'term_id' => $existing->term_id,
            );
        }

        $destination_ids[] = $dest_id;
    }

    // Create Tours
    $tour_count = 0;
    foreach ($destination_ids as $dest_id) {
        $destination = get_term($dest_id, 'ytrip_destination');
        $dest_name = $destination->name;

        for ($i = 0; $i < min($num_tours, count($tour_templates)); $i++) {
            $template = $tour_templates[$i];

            // Replace placeholders
            $title = str_replace('{destination}', $dest_name, $template['title']);
            $description = str_replace('{destination}', $dest_name, $template['description']);

            // Randomize price slightly
            $price = $template['price'] + rand(-100, 100);

            $post_data = array(
                'post_title' => $title,
                'post_content' => $description,
                'post_status' => 'publish',
                'post_type' => 'ytrip_tour',
                'post_excerpt' => wp_trim_words($description, 20),
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                // Assign destination
                wp_set_object_terms($post_id, array($dest_id), 'ytrip_destination');

                // Assign random category
                if (!empty($category_ids)) {
                    $random_cat = $category_ids[array_rand($category_ids)];
                    wp_set_object_terms($post_id, array($random_cat), 'ytrip_category');
                }

                // Add tour details meta
                update_post_meta($post_id, 'ytrip_tour_details', array(
                    'duration' => $template['duration'],
                    'price' => $price,
                    'max_group_size' => rand(10, 30),
                    'min_age' => rand(6, 18),
                    'difficulty' => array('Easy', 'Moderate', 'Hard')[array_rand(array('Easy', 'Moderate', 'Hard'))],
                    'inquiry_email' => get_option('admin_email'),
                ));

                $tour_count++;
                $results['tours'][] = array(
                    'title' => $title,
                    'destination' => $dest_name,
                    'status' => 'success',
                    'post_id' => $post_id,
                    'price' => $price,
                );
            } else {
                $results['tours'][] = array(
                    'title' => $title,
                    'status' => 'error',
                    'message' => $post_id->get_error_message(),
                );
            }
        }
    }

    return new WP_REST_Response(array(
        'status' => 'success',
        'created' => array(
            'categories' => count(array_filter($results['categories'], function($item) {
                return $item['status'] === 'success';
            })),
            'destinations' => count($destination_ids),
            'tours' => $tour_count,
        ),
        'results' => $results,
    ), 200);
}
