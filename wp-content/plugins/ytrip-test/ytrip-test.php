<?php
/*
Plugin Name: YTrip Test Plugin
Description: Simple test to debug access issues
Version: 1.0.0
*/

if (!defined('ABSPATH')) {
    exit;
}

// Add test page - DIRECT, no checks
add_action('admin_menu', 'ytrip_test_menu');

function ytrip_test_menu() {
    // Get current user
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Check if user exists
    if (!$user_id) {
        return;
    }
    
    // Get user data
    $user_data = get_userdata($user_id);
    
    // Get user roles
    $roles = $user_data->roles;
    
    // Check if admin
    $is_admin = in_array('administrator', $roles);
    
    // Add page WITHOUT capability check first
    add_menu_page(
        'YTrip Test',
        'YTrip Test',
        'read', // MINIMAL capability - everyone can read
        'ytrip-test',
        'ytrip_test_page',
        'dashicons-admin-generic',
        90
    );
    
    // Add page WITH manage_options capability
    add_options_page(
        'YTrip Admin Test',
        'YTrip Admin Test',
        'manage_options', // ADMIN ONLY
        'ytrip-admin-test',
        'ytrip_admin_test_page'
    );
}

function ytrip_test_page() {
    $current_user = wp_get_current_user();
    $user_data = get_userdata($current_user->ID);
    
    ?>
    <div class="wrap">
        <h1>🧪 YTrip Test Page</h1>
        <div style="background: #e7f3ff; padding: 20px; margin: 20px 0; border: 2px solid #8a2be2;">
            <h2>User Information</h2>
            <table class="widefat">
                <tr>
                    <td><strong>User ID:</strong></td>
                    <td><?php echo esc_html($current_user->ID); ?></td>
                </tr>
                <tr>
                    <td><strong>Username:</strong></td>
                    <td><?php echo esc_html($current_user->user_login); ?></td>
                </tr>
                <tr>
                    <td><strong>Display Name:</strong></td>
                    <td><?php echo esc_html($current_user->display_name); ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?php echo esc_html($current_user->user_email); ?></td>
                </tr>
                <tr>
                    <td><strong>Roles:</strong></td>
                    <td><?php echo esc_html(implode(', ', $user_data->roles)); ?></td>
                </tr>
                <tr>
                    <td><strong>Is Administrator:</strong></td>
                    <td style="font-weight: bold; color: <?php echo in_array('administrator', $user_data->roles) ? 'green' : 'red'; ?>;">
                        <?php echo in_array('administrator', $user_data->roles) ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div style="background: #d4edda; padding: 20px; margin: 20px 0; border: 2px solid #28a745;">
            <h2>Capability Tests</h2>
            <table class="widefat">
                <tr>
                    <td><strong>read:</strong></td>
                    <td><?php echo current_user_can('read') ? '✓' : '✗'; ?></td>
                </tr>
                <tr>
                    <td><strong>edit_posts:</strong></td>
                    <td><?php echo current_user_can('edit_posts') ? '✓' : '✗'; ?></td>
                </tr>
                <tr>
                    <td><strong>manage_options:</strong></td>
                    <td style="font-weight: bold; color: <?php echo current_user_can('manage_options') ? 'green' : 'red'; ?>;">
                        <?php echo current_user_can('manage_options') ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>edit_theme_options:</strong></td>
                    <td><?php echo current_user_can('edit_theme_options') ? '✓' : '✗'; ?></td>
                </tr>
            </table>
        </div>
        
        <div style="background: #fff3cd; padding: 20px; margin: 20px 0; border: 2px solid #ffc107;">
            <h2>Framework Status</h2>
            <table class="widefat">
                <tr>
                    <td><strong>CSF Class:</strong></td>
                    <td><?php echo class_exists('CSF') ? '✓ LOADED' : '✗ NOT LOADED'; ?></td>
                </tr>
                <tr>
                    <td><strong>CSF_Setup Class:</strong></td>
                    <td><?php echo class_exists('CSF_Setup') ? '✓ LOADED' : '✗ NOT LOADED'; ?></td>
                </tr>
                <tr>
                    <td><strong>CSF_Options Class:</strong></td>
                    <td><?php echo class_exists('CSF_Options') ? '✓ LOADED' : '✗ NOT LOADED'; ?></td>
                </tr>
            </table>
        </div>
        
        <div style="background: #f8d7da; padding: 20px; margin: 20px 0; border: 2px solid #dc3545;">
            <h2>Diagnosis</h2>
            <?php if (in_array('administrator', $user_data->roles)): ?>
                <p style="color: green; font-weight: bold; font-size: 18px;">✓ You ARE an Administrator</p>
                
                <?php if (current_user_can('manage_options')): ?>
                    <p style="color: green; font-weight: bold;">✓ You HAVE manage_options capability</p>
                    <p><strong>Conclusion:</strong> You should be able to access admin pages. If you still see "not allowed", there might be a plugin conflict or caching issue.</p>
                <?php else: ?>
                    <p style="color: red; font-weight: bold;">✗ You DON'T have manage_options capability</p>
                    <p><strong>Conclusion:</strong> Your user role is missing the 'manage_options' capability. This is unusual for an administrator.</p>
                <?php endif; ?>
                
            <?php else: ?>
                <p style="color: red; font-weight: bold; font-size: 18px;">✗ You are NOT an Administrator</p>
                <p><strong>Conclusion:</strong> You don't have the required permissions. Please login with an administrator account.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function ytrip_admin_test_page() {
    ?>
    <div class="wrap">
        <h1>🔒 YTrip Admin Test (manage_options)</h1>
        <?php if (current_user_can('manage_options')): ?>
            <div class="notice notice-success">
                <p>✓ SUCCESS! You can access this page because you have 'manage_options' capability.</p>
            </div>
        <?php else: ?>
            <div class="notice notice-error">
                <p>✗ ERROR! You cannot access this page because you don't have 'manage_options' capability.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}
