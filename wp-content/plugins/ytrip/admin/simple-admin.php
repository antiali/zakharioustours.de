<?php
/**
 * YTrip - Simple Admin Fix
 * Single file, no complexity, proper timing
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load on admin_menu - proper timing
add_action('admin_menu', 'ytrip_simple_admin_menu', 20);

function ytrip_simple_admin_menu() {
    // FIXED: Use edit_posts instead of manage_options for broader access
    // This allows Administrators AND Editors to access settings
    $capability = 'manage_options';
    
    // Check if user has ANY admin capability
    if (!current_user_can($capability)) {
        // Try alternative capabilities for broader access
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return; // User has no admin access at all
        }
        // If they can edit, let them in with lower capability
        $capability = 'edit_posts';
    }
    
    // Add settings page with proper capability
    add_menu_page(
        'YTrip Settings',
        'YTrip',
        $capability,
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
            <div class="notice notice-warning">
                <p>⚠ Codestar Framework not loaded. Settings below may not work.</p>
            </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Quick Status Check</h2>
            <table class="widefat">
                <tr>
                    <td><strong>Current User:</strong></td>
                    <td><?php 
                        $user = wp_get_current_user();
                        echo esc_html($user->display_name); 
                    ?></td>
                </tr>
                <tr>
                    <td><strong>User Role:</strong></td>
                    <td><?php 
                        $roles = wp_get_current_user()->roles;
                        echo esc_html(implode(', ', $roles)); 
                    ?></td>
                </tr>
                <tr>
                    <td><strong>Can manage_options:</strong></td>
                    <td><?php echo current_user_can('manage_options') ? '<span style="color:green;font-weight:bold;">✓ Yes</span>' : '<span style="color:red;font-weight:bold;">✗ No</span>'; ?></td>
                </tr>
                <tr>
                    <td><strong>CSF Class:</strong></td>
                    <td><?php echo class_exists('CSF') ? '<span style="color:green;font-weight:bold;">✓ Loaded</span>' : '<span style="color:red;font-weight:bold;">✗ Not Loaded</span>'; ?></td>
                </tr>
                <tr>
                    <td><strong>CSF Options Class:</strong></td>
                    <td><?php echo class_exists('CSF_Options') ? '<span style="color:green;font-weight:bold;">✓ Loaded</span>' : '<span style="color:red;font-weight:bold;">✗ Not Loaded</span>'; ?></td>
                </tr>
            </table>
        </div>
        
        <?php if (!class_exists('CSF')): ?>
        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Troubleshooting</h2>
            <p>If CSF is not loaded, check the following:</p>
            <ol>
                <li>File exists: <code>vendor/codestar-framework/codestar-framework.php</code> - <?php echo file_exists(YTRIP_PATH . 'vendor/codestar-framework/codestar-framework.php') ? '✓' : '✗'; ?></li>
                <li>File exists: <code>vendor/codestar-framework/classes/setup.class.php</code> - <?php echo file_exists(YTRIP_PATH . 'vendor/codestar-framework/classes/setup.class.php') ? '✓' : '✗'; ?></li>
                <li>File exists: <code>vendor/codestar-framework/classes/admin-options.class.php</code> - <?php echo file_exists(YTRIP_PATH . 'vendor/codestar-framework/classes/admin-options.class.php') ? '✓' : '✗'; ?></li>
            </ol>
        </div>
        <?php endif; ?>
        
        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Debug Page</h2>
            <p>For detailed information, visit the <a href="<?php echo admin_url('admin.php?page=ytrip-debug'); ?>">Full Debug Page</a></p>
        </div>
        
        <div class="card" style="max-width: 800px; margin: 20px 0; padding: 20px; border: 1px solid #ccc;">
            <h2>Help</h2>
            <p>If you see "Sorry, you are not allowed to access this page", please:</p>
            <ol>
                <li>Make sure you are logged in as <strong>Administrator</strong></li>
                <li>Deactivate and reactivate the plugin</li>
                <li>Clear WordPress cache</li>
                <li>Check the <a href="<?php echo admin_url('admin.php?page=ytrip-debug'); ?>">Debug Page</a> for details</li>
            </ol>
        </div>
    </div>
    <?php
}
