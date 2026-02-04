<?php
/**
 * YTrip Capabilities Fix - Simple & Fast
 * 
 * Ensures admin can access settings page
 */

if (!defined('ABSPATH')) {
    exit;
}

// Grant capabilities to administrator role on every admin load
add_action('admin_init', 'ytrip_fix_admin_caps', 1);

function ytrip_fix_admin_caps() {
    // Only run for admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Get admin role and add capabilities
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('manage_options');
        $role->add_cap('edit_theme_options');
        $role->add_cap('ytrip_settings');
    }
}

// Allow admins to access ytrip settings
add_filter('user_has_cap', 'ytrip_allow_settings_access', 10, 3);

function ytrip_allow_settings_access($allcaps, $caps, $args) {
    // Check if current user is admin
    if (!current_user_can('manage_options')) {
        return $allcaps;
    }
    
    // Grant ytrip_settings capability
    if (in_array('ytrip_settings', $caps)) {
        $allcaps['ytrip_settings'] = true;
    }
    
    return $allcaps;
}
