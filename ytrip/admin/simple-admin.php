<?php
/**
 * YTrip - Simple Admin Fix
 * Single file, no complexity
 */

if (!defined('ABSPATH')) {
    exit;
}

// Just one simple hook
add_action('admin_menu', 'ytrip_simple_admin_menu');

function ytrip_simple_admin_menu() {
    // Only add menu if user is admin
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Add settings page
    add_menu_page(
        'YTrip Settings',
        'YTrip',
        'manage_options',
        'ytrip-settings',
        'ytrip_settings_page',
        'dashicons-airplane',
        25
    );
}

// Render settings page
function ytrip_settings_page() {
    ?>
    <div class="wrap">
        <h1>YTrip Settings</h1>
        
        <?php if (class_exists('CSF')): ?>
            <div class="notice notice-success">
                <p>✓ Codestar Framework is loaded correctly</p>
            </div>
        <?php else: ?>
            <div class="notice notice-error">
                <p>✗ Codestar Framework not loaded. Check if codestar-framework.php exists.</p>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Quick Test</h2>
            <table class="widefat">
                <tr>
                    <td><strong>User:</strong></td>
                    <td><?php echo wp_get_current_user()->display_name; ?></td>
                </tr>
                <tr>
                    <td><strong>Can manage_options:</strong></td>
                    <td><?php echo current_user_can('manage_options') ? '✓ Yes' : '✗ No'; ?></td>
                </tr>
                <tr>
                    <td><strong>CSF Class:</strong></td>
                    <td><?php echo class_exists('CSF') ? '✓ Loaded' : '✗ Not Loaded'; ?></td>
                </tr>
            </table>
        </div>
        
        <?php if (!class_exists('CSF')): ?>
        <div class="card">
            <h2>Troubleshooting</h2>
            <ol>
                <li>Check if file exists: <code>vendor/codestar-framework/codestar-framework.php</code></li>
                <li>Check if file exists: <code>vendor/codestar-framework/classes/setup.class.php</code></li>
                <li>Clear WordPress cache</li>
                <li>Deactivate and reactivate plugin</li>
            </ol>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
