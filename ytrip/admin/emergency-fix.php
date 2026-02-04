<?php
/**
 * YTrip Emergency Admin Fix
 * 
 * This script ensures admin can access settings even if there are permission issues
 */

if (!defined('ABSPATH')) {
    exit;
}

// Run immediately on admin init
add_action('admin_menu', 'ytrip_emergency_menu_fix', 999);
add_action('admin_init', 'ytrip_emergency_caps_fix', 1);

/**
 * Emergency menu fix - Ensure menu is accessible
 */
function ytrip_emergency_menu_fix() {
    // Check if user can manage options (admin)
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Add a backup menu item for ytrip settings
    add_menu_page(
        'YTrip Settings (Backup)',
        'YTrip',
        'manage_options',
        'ytrip-settings-backup',
        'ytrip_render_backup_settings',
        'dashicons-airplane',
        26
    );
}

/**
 * Emergency capabilities fix
 */
function ytrip_emergency_caps_fix() {
    // Get current user
    $current_user = wp_get_current_user();
    if (!$current_user || !$current_user->ID) {
        return;
    }
    
    // Only fix for admins
    if (!user_can($current_user->ID, 'manage_options')) {
        return;
    }
    
    // Add all required capabilities to the admin role
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('manage_options');
        $admin_role->add_cap('edit_theme_options');
        $admin_role->add_cap('ytrip_settings');
        $admin_role->add_cap('edit_tours');
        $admin_role->add_cap('edit_others_tours');
    }
    
    // Add capabilities to current user
    $user = get_userdata($current_user->ID);
    if ($user) {
        $all_caps = array_merge($user->allcaps, array(
            'manage_options' => true,
            'edit_theme_options' => true,
            'ytrip_settings' => true,
            'edit_tours' => true,
            'edit_others_tours' => true,
        ));
        $user->allcaps = $all_caps;
    }
}

/**
 * Backup settings page renderer
 */
function ytrip_render_backup_settings() {
    ?>
    <div class="wrap">
        <h1>YTrip Settings - Emergency Access</h1>
        
        <div class="notice notice-info">
            <p><strong>Emergency Access Mode</strong> - If you see this page, you have admin access.</p>
            <p>Please try the main YTrip Settings page: <a href="<?php echo admin_url('admin.php?page=ytrip-settings'); ?>">YTrip Settings</a></p>
        </div>
        
        <h2>Current Capabilities</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>Capability</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>manage_options</td>
                    <td><?php echo current_user_can('manage_options') ? '✓ YES' : '✗ NO'; ?></td>
                </tr>
                <tr>
                    <td>edit_theme_options</td>
                    <td><?php echo current_user_can('edit_theme_options') ? '✓ YES' : '✗ NO'; ?></td>
                </tr>
                <tr>
                    <td>ytrip_settings</td>
                    <td><?php echo current_user_can('ytrip_settings') ? '✓ YES' : '✗ NO'; ?></td>
                </tr>
            </tbody>
        </table>
        
        <h2>Framework Status</h2>
        <table class="widefat fixed">
            <tbody>
                <tr>
                    <td>CSF Class</td>
                    <td><?php echo class_exists('CSF') ? '✓ Loaded' : '✗ Not Loaded'; ?></td>
                </tr>
                <tr>
                    <td>CSF_Setup Class</td>
                    <td><?php echo class_exists('CSF_Setup') ? '✓ Loaded' : '✗ Not Loaded'; ?></td>
                </tr>
                <tr>
                    <td>CSF_Options Class</td>
                    <td><?php echo class_exists('CSF_Options') ? '✓ Loaded' : '✗ Not Loaded'; ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php if (class_exists('CSF')): ?>
        <h2>Framework Configuration</h2>
        <pre><?php print_r(CSF_Setup::$args); ?></pre>
        <?php endif; ?>
    </div>
    <?php
}

// Filter to always allow access to ytrip settings
add_filter('option_page_capability_ytrip_settings', '__return_true');

// Filter to override capability check
add_filter('user_has_cap', 'ytrip_force_admin_access', 99, 3);

/**
 * Force admin access to all ytrip capabilities
 */
function ytrip_force_admin_access($allcaps, $caps, $args) {
    $current_user = wp_get_current_user();
    
    // If checking for any ytrip capability and user is admin
    if ($current_user && user_can($current_user->ID, 'manage_options')) {
        // Grant all ytrip capabilities
        $ytrip_caps = array(
            'ytrip_settings',
            'edit_tours',
            'edit_others_tours',
            'manage_tours',
            'edit_tour',
            'read_tour',
            'delete_tour',
            'edit_tours',
            'edit_others_tours',
            'publish_tours',
            'read_private_tours',
        );
        
        foreach ($ytrip_caps as $cap) {
            if (in_array($cap, $caps)) {
                $allcaps[$cap] = true;
            }
        }
    }
    
    return $allcaps;
}
