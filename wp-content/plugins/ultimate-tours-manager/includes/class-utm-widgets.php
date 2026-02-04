<?php
/**
 * UTM Widgets Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Widgets {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('widgets_init', array($this, 'register_widgets'));
    }
    
    public function register_widgets() {
        register_widget('UTM_Popular_Tours_Widget');
        register_widget('UTM_Tour_Search_Widget');
        register_widget('UTM_Destinations_Widget');
    }
}

class UTM_Popular_Tours_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'utm_popular_tours',
            __('UTM: Popular Tours', 'ultimate-tours-manager'),
            array('description' => __('Display popular tours', 'ultimate-tours-manager'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $tours = new WP_Query(array(
            'post_type' => 'tour',
            'posts_per_page' => $limit,
            'meta_key' => '_tour_booking_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        ));
        
        if ($tours->have_posts()) {
            echo '<ul class="utm-widget-tours">';
            while ($tours->have_posts()) {
                $tours->the_post();
                $price = get_post_meta(get_the_ID(), 'utm_tour_meta_price', true);
                ?>
                <li class="utm-widget-tour-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="utm-widget-tour-thumb">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="utm-widget-tour-info">
                            <h4><?php the_title(); ?></h4>
                            <?php if ($price) : ?>
                                <span class="utm-widget-price"><?php echo utm_format_price($price); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                <?php
            }
            echo '</ul>';
            wp_reset_postdata();
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Popular Tours', 'ultimate-tours-manager');
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'ultimate-tours-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of tours:', 'ultimate-tours-manager'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1" max="10">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['limit'] = absint($new_instance['limit']);
        return $instance;
    }
}

class UTM_Tour_Search_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'utm_tour_search',
            __('UTM: Tour Search', 'ultimate-tours-manager'),
            array('description' => __('Tour search form', 'ultimate-tours-manager'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        echo do_shortcode('[utm_search style="vertical"]');
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Search Tours', 'ultimate-tours-manager');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'ultimate-tours-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }
}

class UTM_Destinations_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'utm_destinations',
            __('UTM: Destinations', 'ultimate-tours-manager'),
            array('description' => __('Display tour destinations', 'ultimate-tours-manager'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $destinations = get_terms(array(
            'taxonomy' => 'destination',
            'hide_empty' => true,
            'number' => $limit,
        ));
        
        if (!empty($destinations) && !is_wp_error($destinations)) {
            echo '<ul class="utm-widget-destinations">';
            foreach ($destinations as $destination) {
                ?>
                <li>
                    <a href="<?php echo get_term_link($destination); ?>">
                        <?php echo esc_html($destination->name); ?>
                        <span class="count">(<?php echo $destination->count; ?>)</span>
                    </a>
                </li>
                <?php
            }
            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Destinations', 'ultimate-tours-manager');
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'ultimate-tours-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of destinations:', 'ultimate-tours-manager'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1" max="20">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['limit'] = absint($new_instance['limit']);
        return $instance;
    }
}
