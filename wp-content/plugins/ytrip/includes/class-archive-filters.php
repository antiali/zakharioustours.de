<?php
/**
 * Archive Filters Class
 * Handles filtering, sorting, and view modes for tour archives
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class YTrip_Archive_Filters {

    private $options;

    public function __construct() {
        $this->options = get_option( 'ytrip_settings' );
        
        add_action( 'pre_get_posts', array( $this, 'modify_archive_query' ) );
        add_action( 'wp_ajax_ytrip_filter_tours', array( $this, 'ajax_filter_tours' ) );
        add_action( 'wp_ajax_nopriv_ytrip_filter_tours', array( $this, 'ajax_filter_tours' ) );
    }

    /**
     * Modify main archive query based on URL params
     */
    public function modify_archive_query( $query ) {
        if ( is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( ! is_post_type_archive( 'ytrip_tour' ) && ! is_tax( 'ytrip_destination' ) && ! is_tax( 'ytrip_category' ) ) {
            return;
        }

        // Posts per page from settings
        $per_page = $this->options['archive_per_page'] ?? 12;
        $query->set( 'posts_per_page', $per_page );

        // Sorting
        $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
        $order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';

        switch ( $orderby ) {
            case 'price_low':
                $query->set( 'meta_key', '_ytrip_price' );
                $query->set( 'orderby', 'meta_value_num' );
                $query->set( 'order', 'ASC' );
                break;
            case 'price_high':
                $query->set( 'meta_key', '_ytrip_price' );
                $query->set( 'orderby', 'meta_value_num' );
                $query->set( 'order', 'DESC' );
                break;
            case 'rating':
                $query->set( 'meta_key', '_ytrip_rating' );
                $query->set( 'orderby', 'meta_value_num' );
                $query->set( 'order', 'DESC' );
                break;
            case 'popularity':
                $query->set( 'meta_key', '_ytrip_views' );
                $query->set( 'orderby', 'meta_value_num' );
                $query->set( 'order', 'DESC' );
                break;
            case 'title':
                $query->set( 'orderby', 'title' );
                $query->set( 'order', $order );
                break;
            default:
                $query->set( 'orderby', 'date' );
                $query->set( 'order', 'DESC' );
        }

        // Meta query for filters
        $meta_query = array();

        // Price range filter
        if ( ! empty( $_GET['min_price'] ) || ! empty( $_GET['max_price'] ) ) {
            $price_query = array( 'key' => '_ytrip_price', 'type' => 'NUMERIC' );
            
            if ( ! empty( $_GET['min_price'] ) ) {
                $price_query['value'] = absint( $_GET['min_price'] );
                $price_query['compare'] = '>=';
            }
            
            if ( ! empty( $_GET['max_price'] ) ) {
                $meta_query[] = array(
                    'key'     => '_ytrip_price',
                    'value'   => absint( $_GET['max_price'] ),
                    'compare' => '<=',
                    'type'    => 'NUMERIC',
                );
            }
            
            if ( ! empty( $_GET['min_price'] ) ) {
                $meta_query[] = $price_query;
            }
        }

        // Duration filter
        if ( ! empty( $_GET['duration'] ) ) {
            $duration = sanitize_text_field( $_GET['duration'] );
            switch ( $duration ) {
                case '1-3':
                    $meta_query[] = array(
                        'key'     => '_ytrip_duration_days',
                        'value'   => array( 1, 3 ),
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    );
                    break;
                case '4-7':
                    $meta_query[] = array(
                        'key'     => '_ytrip_duration_days',
                        'value'   => array( 4, 7 ),
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    );
                    break;
                case '8-14':
                    $meta_query[] = array(
                        'key'     => '_ytrip_duration_days',
                        'value'   => array( 8, 14 ),
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC',
                    );
                    break;
                case '15+':
                    $meta_query[] = array(
                        'key'     => '_ytrip_duration_days',
                        'value'   => 15,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    );
                    break;
            }
        }

        // Rating filter
        if ( ! empty( $_GET['rating'] ) ) {
            $meta_query[] = array(
                'key'     => '_ytrip_rating',
                'value'   => absint( $_GET['rating'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }

        if ( ! empty( $meta_query ) ) {
            $meta_query['relation'] = 'AND';
            $query->set( 'meta_query', $meta_query );
        }

        // Taxonomy filters
        $tax_query = array();

        if ( ! empty( $_GET['destination'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'ytrip_destination',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['destination'] ),
            );
        }

        if ( ! empty( $_GET['category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'ytrip_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_GET['category'] ),
            );
        }

        if ( ! empty( $tax_query ) ) {
            $tax_query['relation'] = 'AND';
            $query->set( 'tax_query', $tax_query );
        }
    }

    /**
     * AJAX handler for filtering tours
     */
    public function ajax_filter_tours() {
        check_ajax_referer( 'ytrip_filter_nonce', 'nonce' );

        $args = array(
            'post_type'      => 'ytrip_tour',
            'posts_per_page' => $this->options['archive_per_page'] ?? 12,
            'paged'          => isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
        );

        // Apply filters from POST data
        $meta_query = array();
        $tax_query  = array();

        // Price range
        if ( ! empty( $_POST['min_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_ytrip_price',
                'value'   => absint( $_POST['min_price'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
        if ( ! empty( $_POST['max_price'] ) ) {
            $meta_query[] = array(
                'key'     => '_ytrip_price',
                'value'   => absint( $_POST['max_price'] ),
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }

        // Duration
        if ( ! empty( $_POST['duration'] ) ) {
            $duration_ranges = array(
                '1-3'  => array( 1, 3 ),
                '4-7'  => array( 4, 7 ),
                '8-14' => array( 8, 14 ),
            );
            
            $dur = sanitize_text_field( $_POST['duration'] );
            if ( isset( $duration_ranges[$dur] ) ) {
                $meta_query[] = array(
                    'key'     => '_ytrip_duration_days',
                    'value'   => $duration_ranges[$dur],
                    'compare' => 'BETWEEN',
                    'type'    => 'NUMERIC',
                );
            } elseif ( $dur === '15+' ) {
                $meta_query[] = array(
                    'key'     => '_ytrip_duration_days',
                    'value'   => 15,
                    'compare' => '>=',
                    'type'    => 'NUMERIC',
                );
            }
        }

        // Rating
        if ( ! empty( $_POST['rating'] ) ) {
            $meta_query[] = array(
                'key'     => '_ytrip_rating',
                'value'   => absint( $_POST['rating'] ),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }

        // Destination
        if ( ! empty( $_POST['destination'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'ytrip_destination',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['destination'] ),
            );
        }

        // Category
        if ( ! empty( $_POST['category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'ytrip_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['category'] ),
            );
        }

        if ( ! empty( $meta_query ) ) {
            $meta_query['relation'] = 'AND';
            $args['meta_query'] = $meta_query;
        }

        if ( ! empty( $tax_query ) ) {
            $tax_query['relation'] = 'AND';
            $args['tax_query'] = $tax_query;
        }

        // Sorting
        $orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'date';
        switch ( $orderby ) {
            case 'price_low':
                $args['meta_key'] = '_ytrip_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
                break;
            case 'price_high':
                $args['meta_key'] = '_ytrip_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'rating':
                $args['meta_key'] = '_ytrip_rating';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
        }

        $query = new WP_Query( $args );

        ob_start();

        if ( $query->have_posts() ) {
            $view_mode   = isset( $_POST['view'] ) ? sanitize_text_field( $_POST['view'] ) : 'grid';
            $card_style  = $this->options['tour_card_style'] ?? 'style_1';
            $card_file   = $view_mode === 'list' ? 'card-list-view.php' : $this->get_card_file( $card_style );

            while ( $query->have_posts() ) {
                $query->the_post();
                include YTRIP_PATH . 'templates/cards/' . $card_file;
            }
        } else {
            echo '<p class="ytrip-no-results">' . esc_html__( 'No tours found matching your criteria.', 'ytrip' ) . '</p>';
        }

        $html = ob_get_clean();

        wp_send_json_success( array(
            'html'        => $html,
            'found_posts' => $query->found_posts,
            'max_pages'   => $query->max_num_pages,
        ) );

        wp_reset_postdata();
        wp_die();
    }

    /**
     * Get card template filename from style
     */
    private function get_card_file( $style ) {
        $map = array(
            'style_1'  => 'card-overlay-gradient.php',
            'style_2'  => 'card-classic-white.php',
            'style_3'  => 'card-modern-shadow.php',
            'style_4'  => 'card-minimal-border.php',
            'style_5'  => 'card-glassmorphism.php',
            'style_6'  => 'card-hover-zoom.php',
            'style_7'  => 'card-split-content.php',
            'style_8'  => 'card-badge-corner.php',
            'style_9'  => 'card-horizontal.php',
            'style_10' => 'card-compact-grid.php',
        );
        
        return isset( $map[$style] ) ? $map[$style] : 'card-classic-white.php';
    }

    /**
     * Get filter data for templates
     */
    public static function get_filter_data() {
        return array(
            'destinations' => get_terms( array(
                'taxonomy'   => 'ytrip_destination',
                'hide_empty' => false, // Changed to false to show all terms for now
            ) ),
            'categories' => get_terms( array(
                'taxonomy'   => 'ytrip_category',
                'hide_empty' => false, // Changed to false to show all terms for now
            ) ),
            'durations' => array(
                '1-3'  => __( '1-3 Days', 'ytrip' ),
                '4-7'  => __( '4-7 Days', 'ytrip' ),
                '8-14' => __( '8-14 Days', 'ytrip' ),
                '15+'  => __( '15+ Days', 'ytrip' ),
            ),
            'sort_options' => array(
                'date'       => __( 'Latest', 'ytrip' ),
                'price_low'  => __( 'Price: Low to High', 'ytrip' ),
                'price_high' => __( 'Price: High to Low', 'ytrip' ),
                'rating'     => __( 'Top Rated', 'ytrip' ),
                'popularity' => __( 'Most Popular', 'ytrip' ),
            ),
        );
    }
}

new YTrip_Archive_Filters();
