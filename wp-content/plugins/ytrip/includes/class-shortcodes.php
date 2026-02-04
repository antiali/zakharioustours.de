<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Shortcodes {
    
    public function __construct() {
        // Tour Booking Form
        add_shortcode( 'ytrip_booking_form', array( $this, 'booking_form_shortcode' ) );
        
        // Tour List
        add_shortcode( 'ytrip_tour_list', array( $this, 'tour_list_shortcode' ) );
        
        // Search
        add_shortcode( 'ytrip_search', array( $this, 'search_shortcode' ) );
    }

    /**
     * Tour Booking Form Shortcode
     */
    public function booking_form_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'tour_id' => 0,
        ), $atts );

        if ( ! $atts['tour_id'] ) {
            return '<p>' . __( 'Please provide a tour ID.', 'ytrip' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="ytrip-booking-form" id="ytrip-booking-form">
            <form method="post" action="">
                <?php wp_nonce_field( 'ytrip_inquiry_action', 'ytrip_inquiry_nonce' ); ?>
                
                <div class="form-row">
                    <label for="ytrip_name"><?php _e( 'Name', 'ytrip' ); ?></label>
                    <input type="text" id="ytrip_name" name="name" required>
                </div>
                
                <div class="form-row">
                    <label for="ytrip_email"><?php _e( 'Email', 'ytrip' ); ?></label>
                    <input type="email" id="ytrip_email" name="email" required>
                </div>
                
                <div class="form-row">
                    <label for="ytrip_message"><?php _e( 'Message', 'ytrip' ); ?></label>
                    <textarea id="ytrip_message" name="message" rows="5" required></textarea>
                </div>
                
                <input type="hidden" name="tour_id" value="<?php echo esc_attr( $atts['tour_id'] ); ?>">
                
                <button type="submit" class="button button-primary"><?php _e( 'Send Inquiry', 'ytrip' ); ?></button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Tour List Shortcode
     */
    public function tour_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'limit' => 10,
            'category' => '',
        ), $atts );

        $args = array(
            'post_type' => 'ytrip_tour',
            'posts_per_page' => absint( $atts['limit'] ),
            'post_status' => 'publish',
        );

        if ( ! empty( $atts['category'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'ytrip_category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            );
        }

        $query = new WP_Query( $args );
        
        if ( ! $query->have_posts() ) {
            return '<p>' . __( 'No tours found.', 'ytrip' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="ytrip-tour-list">
            <h3><?php _e( 'Available Tours', 'ytrip' ); ?></h3>
            <div class="tour-grid">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="tour-card">
                        <h4><?php the_title(); ?></h4>
                        <p><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="button">
                            <?php _e( 'View Details', 'ytrip' ); ?>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Search Shortcode
     */
    public function search_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'placeholder' => __( 'Search tours...', 'ytrip' ),
        ), $atts );

        ob_start();
        ?>
        <div class="ytrip-search">
            <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input 
                    type="text" 
                    name="ytrip_search" 
                    placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
                    class="search-input"
                >
                <button type="submit" class="button">
                    <?php _e( 'Search', 'ytrip' ); ?>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
new YTrip_Shortcodes();
