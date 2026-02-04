<?php
/**
 * UTM Shortcodes Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('utm_tours', array($this, 'tours_shortcode'));
        add_shortcode('utm_tour', array($this, 'single_tour_shortcode'));
        add_shortcode('utm_destinations', array($this, 'destinations_shortcode'));
        add_shortcode('utm_search', array($this, 'search_shortcode'));
        add_shortcode('utm_booking_form', array($this, 'booking_form_shortcode'));
        add_shortcode('utm_featured_tours', array($this, 'featured_tours_shortcode'));
        add_shortcode('utm_tour_slider', array($this, 'tour_slider_shortcode'));
    }
    
    public function tours_shortcode($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
            'limit' => 6,
            'destination' => '',
            'type' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'style' => 'grid',
            'show_filter' => 'no',
        ), $atts);
        
        ob_start();
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => $atts['limit'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );
        
        if (!empty($atts['destination'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'destination',
                'field' => 'slug',
                'terms' => $atts['destination'],
            );
        }
        
        if (!empty($atts['type'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tour-type',
                'field' => 'slug',
                'terms' => $atts['type'],
            );
        }
        
        $tours = new WP_Query($args);
        
        if ($tours->have_posts()) {
            echo '<div class="utm-tours-grid utm-columns-' . esc_attr($atts['columns']) . ' utm-style-' . esc_attr($atts['style']) . '">';
            
            while ($tours->have_posts()) {
                $tours->the_post();
                $this->render_tour_card(get_the_ID());
            }
            
            echo '</div>';
            
            wp_reset_postdata();
        } else {
            echo '<p class="utm-no-tours">' . __('No tours found.', 'ultimate-tours-manager') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    public function single_tour_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);
        
        if (!$atts['id']) {
            return '';
        }
        
        ob_start();
        $this->render_tour_card($atts['id']);
        return ob_get_clean();
    }
    
    public function destinations_shortcode($atts) {
        $atts = shortcode_atts(array(
            'columns' => 4,
            'limit' => 8,
            'style' => 'grid',
            'show_count' => 'yes',
        ), $atts);
        
        ob_start();
        
        $destinations = get_terms(array(
            'taxonomy' => 'destination',
            'hide_empty' => true,
            'number' => $atts['limit'],
        ));
        
        if (!empty($destinations) && !is_wp_error($destinations)) {
            echo '<div class="utm-destinations-grid utm-columns-' . esc_attr($atts['columns']) . '">';
            
            foreach ($destinations as $destination) {
                $image_id = get_term_meta($destination->term_id, 'taxonomy_image_id', true);
                $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium_large') : '';
                
                echo '<div class="utm-destination-card">';
                echo '<a href="' . get_term_link($destination) . '">';
                
                if ($image_url) {
                    echo '<div class="utm-destination-image" style="background-image: url(' . esc_url($image_url) . ')"></div>';
                }
                
                echo '<div class="utm-destination-content">';
                echo '<h3 class="utm-destination-title">' . esc_html($destination->name) . '</h3>';
                
                if ($atts['show_count'] === 'yes') {
                    echo '<span class="utm-destination-count">' . sprintf(_n('%d Tour', '%d Tours', $destination->count, 'ultimate-tours-manager'), $destination->count) . '</span>';
                }
                
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        
        return ob_get_clean();
    }
    
    public function search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'horizontal',
        ), $atts);
        
        ob_start();
        ?>
        <div class="utm-search-form utm-search-<?php echo esc_attr($atts['style']); ?>">
            <form action="<?php echo get_post_type_archive_link('tour'); ?>" method="get">
                <div class="utm-search-fields">
                    <div class="utm-search-field">
                        <label><?php _e('Destination', 'ultimate-tours-manager'); ?></label>
                        <?php wp_dropdown_categories(array(
                            'taxonomy' => 'destination',
                            'name' => 'destination',
                            'show_option_all' => __('All Destinations', 'ultimate-tours-manager'),
                            'hide_empty' => true,
                            'value_field' => 'slug',
                        )); ?>
                    </div>
                    
                    <div class="utm-search-field">
                        <label><?php _e('Tour Type', 'ultimate-tours-manager'); ?></label>
                        <?php wp_dropdown_categories(array(
                            'taxonomy' => 'tour-type',
                            'name' => 'tour-type',
                            'show_option_all' => __('All Types', 'ultimate-tours-manager'),
                            'hide_empty' => true,
                            'value_field' => 'slug',
                        )); ?>
                    </div>
                    
                    <div class="utm-search-field">
                        <label><?php _e('Date', 'ultimate-tours-manager'); ?></label>
                        <input type="date" name="tour_date" placeholder="<?php _e('Select Date', 'ultimate-tours-manager'); ?>">
                    </div>
                    
                    <div class="utm-search-field utm-search-submit">
                        <button type="submit" class="utm-btn utm-btn-primary">
                            <i class="fas fa-search"></i>
                            <?php _e('Search', 'ultimate-tours-manager'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function booking_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'tour_id' => 0,
        ), $atts);
        
        ob_start();
        include UTM_PLUGIN_DIR . 'templates/booking-form.php';
        return ob_get_clean();
    }
    
    public function featured_tours_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 4,
            'columns' => 4,
        ), $atts);
        
        $atts['meta_key'] = '_tour_featured';
        $atts['meta_value'] = '1';
        
        return $this->tours_shortcode($atts);
    }
    
    public function tour_slider_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 6,
            'autoplay' => 'yes',
            'arrows' => 'yes',
            'dots' => 'yes',
        ), $atts);
        
        ob_start();
        
        $args = array(
            'post_type' => 'tour',
            'posts_per_page' => $atts['limit'],
        );
        
        $tours = new WP_Query($args);
        
        if ($tours->have_posts()) {
            echo '<div class="utm-tour-slider" data-autoplay="' . esc_attr($atts['autoplay']) . '" data-arrows="' . esc_attr($atts['arrows']) . '" data-dots="' . esc_attr($atts['dots']) . '">';
            echo '<div class="utm-slider-wrapper">';
            
            while ($tours->have_posts()) {
                $tours->the_post();
                echo '<div class="utm-slider-item">';
                $this->render_tour_card(get_the_ID());
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
            
            wp_reset_postdata();
        }
        
        return ob_get_clean();
    }
    
    private function render_tour_card($tour_id) {
        $price = get_post_meta($tour_id, 'utm_tour_meta_price', true);
        $sale_price = get_post_meta($tour_id, 'utm_tour_meta_sale_price', true);
        $duration = get_post_meta($tour_id, 'utm_tour_meta_duration_value', true);
        $duration_unit = get_post_meta($tour_id, 'utm_tour_meta_duration_unit', true);
        $rating = get_post_meta($tour_id, '_tour_average_rating', true);
        
        $destinations = get_the_terms($tour_id, 'destination');
        $destination_name = (!empty($destinations) && !is_wp_error($destinations)) ? $destinations[0]->name : '';
        
        ?>
        <div class="utm-tour-card" id="tour-<?php echo $tour_id; ?>">
            <div class="utm-tour-image">
                <?php if (has_post_thumbnail($tour_id)) : ?>
                    <a href="<?php echo get_permalink($tour_id); ?>">
                        <?php echo get_the_post_thumbnail($tour_id, 'medium_large'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($sale_price && $sale_price < $price) : ?>
                    <span class="utm-tour-badge utm-badge-sale"><?php _e('Sale', 'ultimate-tours-manager'); ?></span>
                <?php endif; ?>
                
                <div class="utm-tour-wishlist">
                    <button class="utm-wishlist-btn" data-tour-id="<?php echo $tour_id; ?>">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
            </div>
            
            <div class="utm-tour-content">
                <?php if ($destination_name) : ?>
                    <div class="utm-tour-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo esc_html($destination_name); ?></span>
                    </div>
                <?php endif; ?>
                
                <h3 class="utm-tour-title">
                    <a href="<?php echo get_permalink($tour_id); ?>">
                        <?php echo get_the_title($tour_id); ?>
                    </a>
                </h3>
                
                <div class="utm-tour-meta">
                    <?php if ($duration) : ?>
                        <span class="utm-tour-duration">
                            <i class="far fa-clock"></i>
                            <?php echo esc_html($duration . ' ' . $duration_unit); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($rating) : ?>
                        <span class="utm-tour-rating">
                            <i class="fas fa-star"></i>
                            <?php echo number_format($rating, 1); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="utm-tour-footer">
                    <div class="utm-tour-price">
                        <?php if ($sale_price && $sale_price < $price) : ?>
                            <span class="utm-price-old"><?php echo utm_format_price($price); ?></span>
                            <span class="utm-price-new"><?php echo utm_format_price($sale_price); ?></span>
                        <?php else : ?>
                            <span class="utm-price"><?php echo utm_format_price($price); ?></span>
                        <?php endif; ?>
                        <span class="utm-price-unit"><?php _e('/ person', 'ultimate-tours-manager'); ?></span>
                    </div>
                    
                    <a href="<?php echo get_permalink($tour_id); ?>" class="utm-btn utm-btn-sm">
                        <?php _e('Book Now', 'ultimate-tours-manager'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
}

// Helper function for price formatting
function utm_format_price($price) {
    $options = get_option('utm_options');
    $currency = isset($options['currency']) ? $options['currency'] : 'USD';
    $position = isset($options['currency_position']) ? $options['currency_position'] : 'left';
    $decimals = isset($options['decimal_places']) ? $options['decimal_places'] : 2;
    $thousand_sep = isset($options['thousand_separator']) ? $options['thousand_separator'] : ',';
    $decimal_sep = isset($options['decimal_separator']) ? $options['decimal_separator'] : '.';
    
    $symbols = array(
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'AED' => 'د.إ',
        'SAR' => '﷼', 'EGP' => 'E£', 'JPY' => '¥', 'AUD' => 'A$',
        'CAD' => 'C$', 'CHF' => 'CHF', 'CNY' => '¥', 'INR' => '₹',
        'RUB' => '₽', 'TRY' => '₺',
    );
    
    $symbol = isset($symbols[$currency]) ? $symbols[$currency] : $currency;
    $formatted = number_format($price, $decimals, $decimal_sep, $thousand_sep);
    
    switch ($position) {
        case 'left':
            return $symbol . $formatted;
        case 'right':
            return $formatted . $symbol;
        case 'left_space':
            return $symbol . ' ' . $formatted;
        case 'right_space':
            return $formatted . ' ' . $symbol;
        default:
            return $symbol . $formatted;
    }
}
