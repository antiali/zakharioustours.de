<?php
/**
 * YTrip Performance Optimization
 * Handles caching, database, and asset optimization
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class YTrip_Performance {

    private $options;
    private $cache_group = 'ytrip_cache';
    private $cache_time = 3600; // 1 Hour

    public function __construct() {
        $this->options = get_option( 'ytrip_settings' );
        
        // Database
        add_action( 'init', array( $this, 'optimize_database' ) );
        add_action( 'save_post_ytrip_tour', array( $this, 'clear_cache' ) );
        
        // Assets
        add_filter( 'script_loader_tag', array( $this, 'defer_scripts' ), 10, 2 );
        
        // Images
        if ( ! empty( $this->options['enable_lazy_load'] ) ) {
            add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_loading' ), 10, 2 );
        }

        // Cache Clearing AJAX
        add_action( 'wp_ajax_ytrip_clear_cache', array( $this, 'ajax_clear_cache' ) );
    }

    /**
     * Database Optimization: Add Indexes
     */
    public function optimize_database() {
        if ( empty( $this->options['enable_db_indexes'] ) ) return;
        
        $installed_ver = get_option( 'ytrip_db_ver' );
        if ( $installed_ver === YTRIP_VERSION ) return;

        global $wpdb;
        
        // Check if index exists on postmeta
        $index_exists = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->postmeta} WHERE Key_name = 'ytrip_meta_key_idx'" );
        
        if ( empty( $index_exists ) ) {
            $wpdb->query( "CREATE INDEX ytrip_meta_key_idx ON {$wpdb->postmeta} (meta_key(32), post_id)" );
        }

        update_option( 'ytrip_db_ver', YTRIP_VERSION );
    }

    /**
     * Defer Non-Critical JS
     */
    public function defer_scripts( $tag, $handle ) {
        if ( empty( $this->options['defer_js'] ) ) return $tag;
        
        // List of scripts to defer
        $defer_handles = array( 'ytrip-main', 'ytrip-animations', 'ytrip-microinteractions' );
        
        if ( in_array( $handle, $defer_handles ) ) {
            return str_replace( ' src', ' defer src', $tag );
        }
        
        return $tag;
    }

    /**
     * Force Native Lazy Loading
     */
    public function add_lazy_loading( $attr, $attachment ) {
        $attr['loading'] = 'lazy';
        return $attr;
    }

    /**
     * Caching Helper: Set
     */
    public function set_cache( $key, $data ) {
        if ( empty( $this->options['enable_object_cache'] ) ) return false;
        set_transient( $this->cache_group . '_' . $key, $data, $this->cache_time );
    }

    /**
     * Caching Helper: Get
     */
    public function get_cache( $key ) {
        if ( empty( $this->options['enable_object_cache'] ) ) return false;
        return get_transient( $this->cache_group . '_' . $key );
    }

    /**
     * Clear All Transients
     */
    public function clear_cache() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_ytrip_cache_%' 
            OR option_name LIKE '_transient_timeout_ytrip_cache_%'"
        );
    }

    public function ajax_clear_cache() {
        if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error();
        
        $this->clear_cache();
        
        // Also flush object cache if persistent
        wp_cache_flush();

        wp_send_json_success( 'Cache Cleared Successfully' );
    }
}

// Initialize
new YTrip_Performance();

// Helper for Admin Button
function ytrip_clear_cache_button() {
    echo '<button type="button" class="button button-primary" id="ytrip-clear-cache-btn">Clear All Caches</button>';
    echo '<span id="ytrip-cache-msg" style="margin-left:10px; font-weight:600; color: green; display:none;"></span>';
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#ytrip-clear-cache-btn').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            btn.addClass('disabled').text('Clearing...');
            
            $.post(ajaxurl, { action: 'ytrip_clear_cache' }, function(res) {
                btn.removeClass('disabled').text('Clear All Caches');
                if(res.success) {
                    $('#ytrip-cache-msg').text(res.data).show().delay(3000).fadeOut();
                }
            });
        });
    });
    </script>
    <?php
}
