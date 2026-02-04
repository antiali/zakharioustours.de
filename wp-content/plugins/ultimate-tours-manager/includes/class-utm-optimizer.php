<?php
/**
 * UTM Optimizer Class - Performance & Caching
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Optimizer {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('save_post_tour', array($this, 'clear_tour_cache'));
        add_action('utm_daily_cleanup', array($this, 'daily_cleanup'));
        add_action('wp_ajax_utm_clear_cache', array($this, 'ajax_clear_cache'));
    }
    
    public function clear_tour_cache($post_id = null) {
        global $wpdb;
        
        if ($post_id) {
            delete_transient('utm_tour_' . $post_id);
        }
        
        // Clear all UTM transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_utm_%' 
            OR option_name LIKE '_transient_timeout_utm_%'"
        );
        
        // Clear object cache
        wp_cache_flush_group('utm_tours');
        wp_cache_flush_group('utm_destinations');
    }
    
    public function daily_cleanup() {
        global $wpdb;
        
        // Clean old transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_timeout_utm_%' 
            AND option_value < UNIX_TIMESTAMP()"
        );
        
        // Clean expired sessions
        $this->clean_expired_sessions();
        
        // Clean old logs
        $this->clean_old_logs();
        
        // Optimize database tables
        $this->optimize_tables();
    }
    
    private function clean_expired_sessions() {
        // Clean any expired booking sessions
    }
    
    private function clean_old_logs() {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/ultimate-tours-manager/logs';
        
        if (!is_dir($log_dir)) {
            return;
        }
        
        $files = glob($log_dir . '/*.log');
        $max_age = 30 * DAY_IN_SECONDS;
        
        foreach ($files as $file) {
            if (filemtime($file) < (time() - $max_age)) {
                @unlink($file);
            }
        }
    }
    
    private function optimize_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'utm_bookings',
            $wpdb->prefix . 'utm_reviews',
            $wpdb->prefix . 'utm_enquiries',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("OPTIMIZE TABLE $table");
        }
    }
    
    public function ajax_clear_cache() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized', 'ultimate-tours-manager')));
        }
        
        $this->clear_tour_cache();
        
        wp_send_json_success(array('message' => __('Cache cleared successfully', 'ultimate-tours-manager')));
    }
    
    public function get_cached_tours($args, $cache_key) {
        $options = get_option('utm_options');
        
        if (empty($options['enable_cache'])) {
            return false;
        }
        
        return get_transient('utm_tours_' . md5($cache_key));
    }
    
    public function set_cached_tours($data, $cache_key) {
        $options = get_option('utm_options');
        
        if (empty($options['enable_cache'])) {
            return;
        }
        
        $duration = isset($options['cache_duration']) ? absint($options['cache_duration']) * HOUR_IN_SECONDS : DAY_IN_SECONDS;
        
        set_transient('utm_tours_' . md5($cache_key), $data, $duration);
    }
}
