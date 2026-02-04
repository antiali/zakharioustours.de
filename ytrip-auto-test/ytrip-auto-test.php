<?php
/*
Plugin Name: YTrip Auto Installer & Tester
Description: Auto-install and test YTrip with one click
Version: 1.0.0
*/

if (!defined('ABSPATH')) {
    exit;
}

// Auto-run on activation
register_activation_hook(__FILE__, 'ytrip_auto_install');

function ytrip_auto_install() {
    // 1. Check if admin
    if (!current_user_can('manage_options')) {
        wp_die('You must be an administrator to run this installer.');
    }
    
    // 2. Fix capabilities
    $role = get_role('administrator');
    if ($role) {
        $role->add_cap('manage_options');
        $role->add_cap('edit_theme_options');
        $role->add_cap('ytrip_settings');
        $role->add_cap('manage_tours');
    }
    
    // 3. Flush capabilities
    wp_get_current_user()->get_role_caps();
    
    // 4. Redirect to test page
    wp_redirect(admin_url('admin.php?page=ytrip-auto-test'));
    exit;
}

// Add menu
add_action('admin_menu', 'ytrip_auto_menu');

function ytrip_auto_menu() {
    add_options_page(
        'YTrip Auto Test',
        'YTrip Auto Test',
        'manage_options',
        'ytrip-auto-test',
        'ytrip_auto_test_page'
    );
}

function ytrip_auto_test_page() {
    $user = wp_get_current_user();
    $user_data = get_userdata($user->ID);
    $is_admin = in_array('administrator', $user_data->roles);
    $can_manage = current_user_can('manage_options');
    $csf_loaded = class_exists('CSF');
    
    // Check files
    $ytrip_path = WP_PLUGIN_DIR . '/ytrip/';
    $files = array(
        'ytrip.php' => $ytrip_path . 'ytrip.php',
        'codestar-framework.php' => $ytrip_path . 'vendor/codestar-framework/codestar-framework.php',
        'codestar-config.php' => $ytrip_path . 'admin/codestar-config.php',
        'simple-admin.php' => $ytrip_path . 'admin/simple-admin.php',
    );
    
    $all_files_exist = true;
    foreach ($files as $name => $path) {
        if (!file_exists($path)) {
            $all_files_exist = false;
            break;
        }
    }
    
    ?>
    <div class="wrap">
        <h1>🚀 YTrip Auto Installer & Tester</h1>
        
        <style>
            .status-box { padding: 20px; margin: 15px 0; border: 3px solid; border-radius: 8px; }
            .success { background: #d4edda; border-color: #28a745; }
            .error { background: #f8d7da; border-color: #dc3545; }
            .warning { background: #fff3cd; border-color: #ffc107; }
            .status-yes { color: #155724; font-weight: bold; font-size: 18px; }
            .status-no { color: #721c24; font-weight: bold; font-size: 18px; }
            .action-btn { background: #0073aa; color: white; padding: 15px 30px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; margin: 10px 5px; }
            .action-btn:hover { background: #005177; }
        </style>
        
        <div class="status-box <?php echo $is_admin ? 'success' : 'error'; ?>">
            <h2>👤 User Status</h2>
            <p><strong>Username:</strong> <?php echo esc_html($user->user_login); ?></p>
            <p><strong>Is Administrator:</strong> 
               <span class="<?php echo $is_admin ? 'status-yes' : 'status-no'; ?>">
                   <?php echo $is_admin ? '✓ YES' : '✗ NO'; ?>
               </span>
            </p>
            <p><strong>Can manage_options:</strong> 
               <span class="<?php echo $can_manage ? 'status-yes' : 'status-no'; ?>">
                   <?php echo $can_manage ? '✓ YES' : '✗ NO'; ?>
               </span>
            </p>
        </div>
        
        <div class="status-box <?php echo $csf_loaded ? 'success' : 'warning'; ?>">
            <h2>📦 Framework Status</h2>
            <p><strong>CSF Class:</strong> 
               <span class="<?php echo $csf_loaded ? 'status-yes' : 'status-no'; ?>">
                   <?php echo $csf_loaded ? '✓ LOADED' : '✗ NOT LOADED'; ?>
               </span>
            </p>
        </div>
        
        <div class="status-box <?php echo $all_files_exist ? 'success' : 'error'; ?>">
            <h2>📁 Files Check</h2>
            <?php foreach ($files as $name => $path): ?>
            <p><strong><?php echo esc_html($name); ?>:</strong> 
               <?php echo file_exists($path) ? '✓ EXISTS' : '✗ MISSING'; ?>
            </p>
            <?php endforeach; ?>
        </div>
        
        <div class="status-box warning">
            <h2>🎯 Final Diagnosis</h2>
            <?php if (!$is_admin): ?>
                <p class="status-no">❌ YOU ARE NOT AN ADMINISTRATOR</p>
                <p>Please login with an administrator account.</p>
            <?php elseif (!$can_manage): ?>
                <p class="status-no">❌ MISSING manage_options CAPABILITY</p>
                <p>Your admin role is missing the required capability.</p>
            <?php elseif (!$csf_loaded): ?>
                <p class="status-no">❌ CODESTAR FRAMEWORK NOT LOADED</p>
                <p>Framework files are missing or not loading correctly.</p>
            <?php elseif (!$all_files_exist): ?>
                <p class="status-no">❌ SOME FILES ARE MISSING</p>
                <p>Please upload complete plugin files.</p>
            <?php else: ?>
                <p class="status-yes">✅ EVERYTHING LOOKS GOOD!</p>
                <p>Try accessing YTrip Settings:</p>
                <p><a href="<?php echo admin_url('admin.php?page=ytrip-settings'); ?>" class="button button-primary" style="padding: 15px 30px; font-size: 16px;">Open YTrip Settings</a></p>
            <?php endif; ?>
        </div>
        
        <div class="status-box" style="background: #f8f9fa; border-color: #6c757d;">
            <h2>🔧 Manual Actions</h2>
            <p>If the auto-check passes but you still see errors, try these:</p>
            <ol>
                <li><strong>Deactivate YTrip Plugin</strong> from Plugins page</li>
                <li><strong>Activate YTrip Plugin</strong> again</li>
                <li><strong>Clear All Cache</strong> if using a cache plugin</li>
                <li><strong>Visit YTrip Settings</strong> directly: <br>
                    <code><?php echo admin_url('admin.php?page=ytrip-settings'); ?></code>
                </li>
            </ol>
        </div>
    </div>
    <?php
}
