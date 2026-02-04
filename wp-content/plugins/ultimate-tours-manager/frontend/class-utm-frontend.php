<?php
/**
 * UTM Frontend Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_filter('template_include', array($this, 'template_include'));
        add_filter('single_template', array($this, 'single_tour_template'));
        add_filter('archive_template', array($this, 'archive_tour_template'));
        add_filter('the_content', array($this, 'tour_content_filter'));
        add_action('wp_head', array($this, 'add_schema_markup'));
        add_action('wp_head', array($this, 'add_open_graph_tags'));
    }
    
    public function enqueue_assets() {
        if (!is_singular('tour') && !is_post_type_archive('tour') && !is_tax('destination') && !is_tax('tour-type')) {
            return;
        }
        
        // Font Awesome
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1');
        
        // Main CSS
        wp_enqueue_style('utm-frontend', UTM_PLUGIN_URL . 'assets/css/frontend.css', array(), UTM_VERSION);
        
        // Swiper for sliders
        wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
        wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
        
        // Lightbox
        wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), '5.0');
        wp_enqueue_script('fancybox', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array(), '5.0', true);
        
        // Date picker
        wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13');
        wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true);
        
        // Main JS
        wp_enqueue_script('utm-frontend', UTM_PLUGIN_URL . 'assets/js/frontend.js', array('jquery', 'swiper', 'fancybox', 'flatpickr'), UTM_VERSION, true);
        
        $options = get_option('utm_options');
        
        wp_localize_script('utm-frontend', 'utmFrontend', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'resturl' => rest_url('utm/v1/'),
            'nonce' => wp_create_nonce('utm_nonce'),
            'currency' => isset($options['currency']) ? $options['currency'] : 'USD',
            'dateFormat' => isset($options['date_format']) ? $options['date_format'] : 'Y-m-d',
            'strings' => array(
                'loading' => __('Loading...', 'ultimate-tours-manager'),
                'book_now' => __('Book Now', 'ultimate-tours-manager'),
                'add_to_cart' => __('Add to Cart', 'ultimate-tours-manager'),
                'added' => __('Added!', 'ultimate-tours-manager'),
                'error' => __('An error occurred', 'ultimate-tours-manager'),
                'select_date' => __('Select Date', 'ultimate-tours-manager'),
                'adults' => __('Adults', 'ultimate-tours-manager'),
                'children' => __('Children', 'ultimate-tours-manager'),
                'infants' => __('Infants', 'ultimate-tours-manager'),
            ),
        ));
        
        // Add dynamic CSS
        $this->add_dynamic_css();
    }
    
    private function add_dynamic_css() {
        $options = get_option('utm_options');
        
        $primary = isset($options['primary_color']) ? $options['primary_color'] : '#0073aa';
        $secondary = isset($options['secondary_color']) ? $options['secondary_color'] : '#23282d';
        $accent = isset($options['accent_color']) ? $options['accent_color'] : '#ffc107';
        
        $css = "
            :root {
                --utm-primary: {$primary};
                --utm-secondary: {$secondary};
                --utm-accent: {$accent};
            }
        ";
        
        if (!empty($options['custom_css'])) {
            $css .= $options['custom_css'];
        }
        
        wp_add_inline_style('utm-frontend', $css);
    }
    
    public function template_include($template) {
        return $template;
    }
    
    public function single_tour_template($template) {
        global $post;
        
        if ($post->post_type === 'tour') {
            $custom_template = UTM_PLUGIN_DIR . 'templates/single-tour.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    public function archive_tour_template($template) {
        if (is_post_type_archive('tour')) {
            $custom_template = UTM_PLUGIN_DIR . 'templates/archive-tour.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
    
    public function tour_content_filter($content) {
        if (!is_singular('tour') || !in_the_loop() || !is_main_query()) {
            return $content;
        }
        
        ob_start();
        include UTM_PLUGIN_DIR . 'templates/content-tour.php';
        return ob_get_clean();
    }
    
    public function add_schema_markup() {
        if (!is_singular('tour')) {
            return;
        }
        
        $options = get_option('utm_options');
        
        if (empty($options['enable_schema'])) {
            return;
        }
        
        global $post;
        $tour_id = $post->ID;
        
        $price = get_post_meta($tour_id, 'utm_tour_meta_price', true);
        $sale_price = get_post_meta($tour_id, 'utm_tour_meta_sale_price', true);
        $rating = get_post_meta($tour_id, '_tour_average_rating', true);
        $review_count = get_post_meta($tour_id, '_tour_review_count', true);
        $duration = get_post_meta($tour_id, 'utm_tour_meta_duration_value', true);
        $duration_unit = get_post_meta($tour_id, 'utm_tour_meta_duration_unit', true);
        
        $destinations = get_the_terms($tour_id, 'destination');
        $destination_name = (!empty($destinations) && !is_wp_error($destinations)) ? $destinations[0]->name : '';
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'image' => get_the_post_thumbnail_url($tour_id, 'large'),
            'touristType' => 'Traveler',
        );
        
        if ($destination_name) {
            $schema['itinerary'] = array(
                '@type' => 'Place',
                'name' => $destination_name,
            );
        }
        
        if ($price) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $sale_price && $sale_price < $price ? $sale_price : $price,
                'priceCurrency' => isset($options['currency']) ? $options['currency'] : 'USD',
                'availability' => 'https://schema.org/InStock',
            );
        }
        
        if ($rating && $review_count) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating,
                'reviewCount' => $review_count,
            );
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
    
    public function add_open_graph_tags() {
        if (!is_singular('tour')) {
            return;
        }
        
        $options = get_option('utm_options');
        
        if (empty($options['enable_og_tags'])) {
            return;
        }
        
        global $post;
        $tour_id = $post->ID;
        
        $price = get_post_meta($tour_id, 'utm_tour_meta_price', true);
        
        echo '<meta property="og:type" content="product">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_the_excerpt()) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url(get_the_post_thumbnail_url($tour_id, 'large')) . '">' . "\n";
        
        if ($price) {
            $currency = isset($options['currency']) ? $options['currency'] : 'USD';
            echo '<meta property="product:price:amount" content="' . esc_attr($price) . '">' . "\n";
            echo '<meta property="product:price:currency" content="' . esc_attr($currency) . '">' . "\n";
        }
        
        // Twitter Cards
        if (!empty($options['enable_twitter_cards'])) {
            echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '">' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr(get_the_excerpt()) . '">' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url(get_the_post_thumbnail_url($tour_id, 'large')) . '">' . "\n";
        }
    }
}
