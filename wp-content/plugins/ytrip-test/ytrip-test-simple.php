<?php
/*
Plugin Name: YTrip Test Plugin
Description: Simple test to debug access issues
Version: 1.0.0
Author: YTrip
*/

if (!defined('ABSPATH')) {
    exit;
}

// Add test menu
add_action('admin_menu', 'ytrip_test_menu');

function ytrip_test_menu() {
    add_options_page(
        'YTrip Test',
        'YTrip Test',
        'read',
        'ytrip-test-page',
        'ytrip_test_page'
    );
}

function ytrip_test_page() {
    $user = wp_get_current_user();
    $user_data = get_userdata($user->ID);
    $is_admin = in_array('administrator', $user_data->roles);
    $can_manage = current_user_can('manage_options');
    
    ?>
    <div class="wrap">
        <h1>🧪 YTrip Access Test</h1>
        
        <style>
            .result-box { padding: 20px; margin: 10px 0; border: 2px solid; }
            .success { background: #d4edda; border-color: #28a745; color: #155724; }
            .error { background: #f8d7da; border-color: #dc3545; color: #721c24; }
            .warning { background: #fff3cd; border-color: #ffc107; color: #856404; }
            .status-yes { color: green; font-weight: bold; }
            .status-no { color: red; font-weight: bold; }
        </style>
        
        <div class="result-box <?php echo $is_admin ? 'success' : 'error'; ?>">
            <h2>User Role</h2>
            <p><strong>Is Administrator:</strong> 
               <?php echo $is_admin ? '<span class="status-yes">✓ YES</span>' : '<span class="status-no">✗ NO</span>'; ?></p>
            <p><strong>Roles:</strong> <?php echo esc_html(implode(', ', $user_data->roles)); ?></p>
        </div>
        
        <div class="result-box <?php echo $can_manage ? 'success' : 'error'; ?>">
            <h2>Capability</h2>
            <p><strong>Can manage_options:</strong> 
               <?php echo $can_manage ? '<span class="status-yes">✓ YES</span>' : '<span class="status-no">✗ NO</span>'; ?></p>
        </div>
        
        <div class="result-box <?php echo class_exists('CSF') ? 'success' : 'warning'; ?>">
            <h2>Framework</h2>
            <p><strong>CSF Class:</strong> 
               <?php echo class_exists('CSF') ? '<span class="status-yes">✓ LOADED</span>' : '<span class="status-no">✗ NOT LOADED</span>'; ?></p>
            <p><strong>CSF_Options Class:</strong> 
               <?php echo class_exists('CSF_Options') ? '<span class="status-yes">✓ LOADED</span>' : '<span class="status-no">✗ NOT LOADED</span>'; ?></p>
        </div>
        
        <div class="result-box warning">
            <h2>Diagnosis</h2>
            <?php if (!$is_admin): ?>
                <p><strong>Issue:</strong> You are not logged in as Administrator.</p>
                <p><strong>Solution:</strong> Login with admin account.</p>
            <?php elseif (!$can_manage): ?>
                <p><strong>Issue:</strong> Your admin role is missing 'manage_options' capability.</p>
                <p><strong>Solution:</strong> Check your user role settings or contact your hosting provider.</p>
            <?php elseif (!class_exists('CSF')): ?>
                <p><strong>Issue:</strong> Codestar Framework is not loaded.</p>
                <p><strong>Solution:</strong> Check if framework files exist in vendor folder.</p>
            <?php else: ?>
                <p><strong>Status:</strong> Everything looks correct!</p>
                <p><strong>If still seeing errors:</strong> Clear cache, deactivate/reactivate plugin, check for plugin conflicts.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
