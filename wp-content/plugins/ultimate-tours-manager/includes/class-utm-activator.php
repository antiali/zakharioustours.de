<?php
/**
 * UTM Activator Class
 *
 * Fired during plugin activation
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Activator {
    
    /**
     * Activate the plugin
     */
    public static function activate() {
        // Set default options
        self::set_default_options();
        
        // Create custom tables
        self::create_tables();
        
        // Create required directories
        self::create_directories();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set flag to flush rules on next init
        update_option('utm_flush_rewrite_rules', true);
        
        // Set activation timestamp
        update_option('utm_activation_time', current_time('timestamp'));
        
        // Schedule cron jobs
        self::schedule_cron_jobs();
        
        // Create sample data option
        update_option('utm_show_welcome', true);
    }
    
    /**
     * Set default options
     */
    private static function set_default_options() {
        $defaults = array(
            'utm_version' => UTM_VERSION,
            'utm_tour_slug' => 'tour',
            'utm_destination_slug' => 'destination',
            'utm_tour_type_slug' => 'tour-type',
            'utm_enable_booking' => true,
            'utm_enable_reviews' => true,
            'utm_enable_gallery' => true,
            'utm_enable_map' => true,
            'utm_enable_pricing' => true,
            'utm_enable_itinerary' => true,
            'utm_currency' => 'USD',
            'utm_date_format' => 'Y-m-d',
            'utm_time_format' => 'H:i',
            'utm_items_per_page' => 12,
            'utm_gallery_columns' => 4,
            'utm_related_tours_count' => 4,
            'utm_booking_status' => 'pending',
            'utm_auto_confirm_booking' => false,
            'utm_enable_notifications' => true,
            'utm_admin_email' => get_option('admin_email'),
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Create custom database tables
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_bookings = $wpdb->prefix . 'utm_bookings';
        $table_reviews = $wpdb->prefix . 'utm_reviews';
        $table_enquiries = $wpdb->prefix . 'utm_enquiries';
        
        $sql = array();
        
        // Bookings table
        $sql[] = "CREATE TABLE IF NOT EXISTS $table_bookings (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tour_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            booking_number varchar(50) NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50) NOT NULL,
            adults int(11) NOT NULL DEFAULT 0,
            children int(11) NOT NULL DEFAULT 0,
            infants int(11) NOT NULL DEFAULT 0,
            total_price decimal(10,2) NOT NULL,
            booking_date date NOT NULL,
            booking_time time NOT NULL,
            special_requirements text,
            status varchar(50) NOT NULL DEFAULT 'pending',
            payment_status varchar(50) NOT NULL DEFAULT 'unpaid',
            payment_method varchar(50),
            transaction_id varchar(255),
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY tour_id (tour_id),
            KEY user_id (user_id),
            KEY booking_number (booking_number),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Reviews table
        $sql[] = "CREATE TABLE IF NOT EXISTS $table_reviews (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tour_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            rating int(11) NOT NULL DEFAULT 5,
            title varchar(255),
            content text NOT NULL,
            pros text,
            cons text,
            verified_purchase tinyint(1) NOT NULL DEFAULT 0,
            helpful_count int(11) NOT NULL DEFAULT 0,
            status varchar(50) NOT NULL DEFAULT 'pending',
            ip_address varchar(45),
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY tour_id (tour_id),
            KEY user_id (user_id),
            KEY rating (rating),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Enquiries table
        $sql[] = "CREATE TABLE IF NOT EXISTS $table_enquiries (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tour_id bigint(20) UNSIGNED NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50),
            subject varchar(255),
            message text NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'unread',
            ip_address varchar(45),
            user_agent text,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY tour_id (tour_id),
            KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        foreach ($sql as $query) {
            dbDelta($query);
        }
    }
    
    /**
     * Create required directories
     */
    private static function create_directories() {
        $upload_dir = wp_upload_dir();
        
        $directories = array(
            'ultimate-tours-manager',
            'ultimate-tours-manager/bookings',
            'ultimate-tours-manager/temp',
            'ultimate-tours-manager/logs',
            'ultimate-tours-manager/exports',
            'ultimate-tours-manager/imports',
            'ultimate-tours-manager/cache',
        );
        
        foreach ($directories as $dir) {
            $path = $upload_dir['basedir'] . '/' . $dir;
            if (!file_exists($path)) {
                wp_mkdir_p($path);
                
                // Create index.php to prevent directory browsing
                file_put_contents($path . '/index.php', '<?php // Silence is golden.');
            }
        }
    }
    
    /**
     * Schedule cron jobs
     */
    private static function schedule_cron_jobs() {
        if (!wp_next_scheduled('utm_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'utm_daily_cleanup');
        }
        
        if (!wp_next_scheduled('utm_send_reminders')) {
            wp_schedule_event(time(), 'hourly', 'utm_send_reminders');
        }
        
        if (!wp_next_scheduled('utm_sync_inventory')) {
            wp_schedule_event(time(), 'hourly', 'utm_sync_inventory');
        }
    }
}
