<?php
/**
 * UTM Deactivator Class
 *
 * Fired during plugin deactivation
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Deactivator {
    
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear scheduled cron jobs
        self::clear_cron_jobs();
        
        // Clear transients
        self::clear_transients();
        
        // Clear caches
        self::clear_caches();
    }
    
    /**
     * Clear scheduled cron jobs
     */
    private static function clear_cron_jobs() {
        $crons = array(
            'utm_daily_cleanup',
            'utm_send_reminders',
            'utm_sync_inventory',
        );
        
        foreach ($crons as $cron) {
            wp_clear_scheduled_hook($cron);
        }
    }
    
    /**
     * Clear transients
     */
    private static function clear_transients() {
        global $wpdb;
        
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
            WHERE option_name LIKE '_transient_utm_%' 
            OR option_name LIKE '_transient_timeout_utm_%'"
        );
    }
    
    /**
     * Clear caches
     */
    private static function clear_caches() {
        // Clear object cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear page cache if caching plugin is active
        if (class_exists('W3_Plugin_TotalCacheAdmin')) {
            if (function_exists('w3tc_flush_all')) {
                w3tc_flush_all();
            }
        } elseif (class_exists('WP_Optimize')) {
            if (class_exists('WP_Optimize_Cache')) {
                $cache = new WP_Optimize_Cache();
                $cache->purge();
            }
        }
    }
}
