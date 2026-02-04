<?php
/**
 * YTrip Emergency Fix - Admin Access
 * 
 * This file ensures admin can always access settings
 */

if (!defined('ABSPATH')) {
    exit;
}

// Ensure CSF class exists before proceeding
if (!class_exists('CSF')) {
    return;
}

// Hook to ensure capabilities on every admin load
add_action('admin_init', 'ytrip_ensure_admin_access');

function ytrip_ensure_admin_access() {
    // Get admin role
    $role = get_role('administrator');
    
    if ($role) {
        // Add all required capabilities
        $role->add_cap('manage_options');
        $role->add_cap('edit_theme_options');
        $role->add_cap('ytrip_settings');
    }
    
    // Also add to editor role
    $editor_role = get_role('editor');
    if ($editor_role) {
        $editor_role->add_cap('ytrip_settings');
    }
}

// Filter to allow access to settings page
add_filter('user_has_cap', 'ytrip_allow_settings_access', 10, 3);

function ytrip_allow_settings_access($allcaps, $caps, $args) {
    // If checking for ytrip_settings capability
    if (in_array('ytrip_settings', $caps)) {
        $user = wp_get_current_user();
        
        // Allow access for admin users
        if ($user && user_can($user->ID, 'manage_options')) {
            $allcaps['ytrip_settings'] = true;
        }
    }
    
    return $allcaps;
}

// Force CSF to use correct capability
add_filter('csf/capability', 'ytrip_csf_capability_fix', 10, 2);

function ytrip_csf_capability_fix($capability, $prefix) {
    // Always use manage_options for ytrip
    if ($prefix === 'ytrip_settings') {
        return 'manage_options';
    }
    return $capability;
}
