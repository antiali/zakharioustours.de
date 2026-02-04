<?php
/**
 * UTM Security Fix Class
 *
 * Ensure admin can always access settings
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Security_Fix {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Ensure admin can always access
        add_action('admin_init', array($this, 'ensure_admin_capabilities'));
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_filter('user_has_cap', array($this, 'ensure_settings_access'), 10, 3);
    }
    
    /**
     * Ensure admin users have access to settings
     */
    public function ensure_admin_capabilities() {
        // Get admin role
        $role = get_role('administrator');
        
        if ($role) {
            // Ensure all required capabilities are present
            $required_caps = array(
                'manage_options',
                'edit_posts',
                'delete_posts',
                'read_private_posts',
                'edit_private_posts',
                'ytrip_settings', // Custom capability
                'utm_settings', // Custom capability
            );
            
            foreach ($required_caps as $cap) {
                if (!$role->has_cap($cap)) {
                    $role->add_cap($cap);
                }
            }
            
            // Also check for editor role
            $editor_role = get_role('editor');
            if ($editor_role) {
                $editor_role->add_cap('ytrip_settings');
                $editor_role->add_cap('manage_tours');
            }
        }
        
        // Flush capabilities
        if (get_transient('utm_flush_caps')) {
            delete_transient('utm_flush_caps');
            wp_get_current_user()->get_role_caps();
        }
    }
    
    /**
     * Add settings menu for all roles
     */
    public function add_settings_menu() {
        // Add settings page that's accessible
        add_menu_page(
            'YTrip Settings',
            'Settings',
            'manage_options',
            'ytrip-settings',
            '',
            'dashicons-admin-generic',
            2
        );
        
        // Also add submenu under tours
        add_submenu_page(
            'edit.php?post_type=tour',
            'YTrip Settings',
            'Settings',
            'manage_options',
            'ytrip-settings',
            ''
        );
    }
    
    /**
     * Filter user capabilities for settings access
     */
    public function ensure_settings_access($allcaps, $caps, $args) {
        // Allow access to settings page for admin and editor roles
        if (in_array('ytrip_settings', $caps)) {
            $user = wp_get_current_user();
            
            if ($user && user_can($user->ID, 'edit_posts')) {
                $allcaps['ytrip_settings'] = true;
            }
        }
        
        return $allcaps;
    }
}

// Initialize security fix
UTM_Security_Fix::get_instance();
