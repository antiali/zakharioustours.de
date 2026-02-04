<?php
/**
 * YTrip Performance Optimization (Enterprise Grade)
 * Handles caching, database optimization, asset management, and critical CSS.
 * 
 * @package YTrip
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Performance {

    private $options;
    private $cache_group = 'ytrip_cache';
    private $cache_time  = 3600; // 1 Hour Default

    public function __construct() {
        $this->options = get_option( 'ytrip_settings' );
        
        // 1. Database Optimization
        add_action( 'init', array( $this, 'schedule_db_optimization' ) );
        add_action( 'ytrip_daily_maintenance', array( $this, 'run_db_maintenance' ) );
        
        // 2. Caching Hooks
        add_action( 'save_post_ytrip_tour', array( $this, 'flush_cache_on_save' ) );
        
        // 3. Asset Optimization
        if ( ! is_admin() ) {
            add_filter( 'script_loader_tag', array( $this, 'optimize_script_loading' ), 10, 2 );
            add_filter( 'style_loader_tag', array( $this, 'optimize_style_loading' ), 10, 4 );
        }
        
        // 4. Image Optimization
        if ( ! empty( $this->options['enable_lazy_load'] ) ) {
            add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_loading' ), 10, 2 );
        }

        // 5. AJAX Cache Clearing
        add_action( 'wp_ajax_ytrip_clear_cache', array( $this, 'ajax_clear_cache' ) );
    }

    /**
     * --- DATABASE OPTIMIZATION ---
     */
    public function schedule_db_optimization() {
        if ( ! wp_next_scheduled( 'ytrip_daily_maintenance' ) ) {
            wp_schedule_event( time(), 'daily', 'ytrip_daily_maintenance' );
        }
    }

    public function run_db_maintenance() {
        if ( empty( $this->options['enable_db_maintenance'] ) ) return;

        global $wpdb;
        
        // 1. Add Index to PostMeta if missing (Critical for filtering)
        // Check if index exists first to avoid errors
        $indices = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->postmeta} WHERE Key_name = 'ytrip_meta_idx'" );
        if ( empty( $indices ) ) {
            $wpdb->query( "CREATE INDEX ytrip_meta_idx ON {$wpdb->postmeta} (meta_key(32), post_id)" );
        }

        // 2. Clean up expired transients (WP doesn't always do this automatically)
        $wpdb->query( 
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_timeout_ytrip_%' 
             AND option_value < " . time() 
        );
    }

    /**
     * --- ASSET OPTIMIZATION ---
     */
    public function optimize_script_loading( $tag, $handle ) {
        // Only target our scripts or non-critical ones
        if ( is_admin() ) return $tag;
        
        $defer_handles = array( 'ytrip-main', 'ytrip-search', 'google-maps' );
        $async_handles = array( 'google-analytics', 'facebook-pixel' );

        if ( in_array( $handle, $defer_handles ) ) {
            return str_replace( ' src', ' defer="defer" src', $tag );
        }
        
        if ( in_array( $handle, $async_handles ) ) {
            return str_replace( ' src', ' async="async" src', $tag );
        }
        
        return $tag;
    }
    
    public function optimize_style_loading( $html, $handle, $href, $media ) {
        // Preload critical fonts if enqueued via this handle
        if ( 'ytrip-fonts' === $handle ) {
            return "<link rel='preload' as='style' href='$href' />$html";
        }
        return $html;
    }

    public function add_lazy_loading( $attr, $attachment ) {
        // Force native lazy loading
        $attr['loading'] = 'lazy';
        // Add decoding async for better paint performance
        $attr['decoding'] = 'async';
        return $attr;
    }

    /**
     * --- CACHING SYSTEM ---
     * Supports WP Object Cache (Redis/Memcached) + Transients fallback
     */
    public function set_cache( $key, $data, $expiration = 0 ) {
        if ( $expiration === 0 ) $expiration = $this->cache_time;
        
        // Use Object Cache if available (persistent)
        if ( wp_using_ext_object_cache() ) {
            wp_cache_set( $key, $data, $this->cache_group, $expiration );
        } else {
            // Fallback to DB Transients
            set_transient( $this->cache_group . '_' . $key, $data, $expiration );
        }
    }

    public function get_cache( $key ) {
        // Use Object Cache
        if ( wp_using_ext_object_cache() ) {
            return wp_cache_get( $key, $this->cache_group );
        }
        // Fallback
        return get_transient( $this->cache_group . '_' . $key );
    }

    public function delete_cache( $key ) {
        if ( wp_using_ext_object_cache() ) {
            wp_cache_delete( $key, $this->cache_group );
        } else {
            delete_transient( $this->cache_group . '_' . $key );
        }
    }

    public function flush_cache_on_save( $post_id ) {
        // Clear specific caches related to tours
        $this->delete_cache( 'featured_tours' );
        $this->delete_cache( 'tour_archive_query' );
        // Optionally: Clear global object cache group
        if ( wp_using_ext_object_cache() ) {
            wp_cache_flush_group( $this->cache_group );
        }
    }

    public function ajax_clear_cache() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        
        // 1. Clear Object Cache
        wp_cache_flush();
        
        // 2. Clear Transients (SQL Fallback for non-persistent environments)
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_ytrip_%' OR option_name LIKE '_transient_timeout_ytrip_%'" );

        wp_send_json_success( 'Performance Cache Cleared!' );
    }
}

// Initialize
new YTrip_Performance();
