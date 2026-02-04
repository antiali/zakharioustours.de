<?php
/**
 * YTrip Full Debug - Troubleshoot All Issues
 * 
 * Run this to see everything
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add debug page - proper timing
add_action('admin_menu', 'ytrip_debug_menu', 25);

function ytrip_debug_menu() {
    // FIXED: Use same capability logic as main menu
    $capability = 'manage_options';
    if (!current_user_can($capability)) {
        $capability = 'edit_posts';
    }
    
    add_submenu_page(
        'ytrip-settings',
        'YTrip Debug',
        'Debug',
        $capability,
        'ytrip-debug',
        'ytrip_debug_page'
    );
}

function ytrip_debug_page() {
    global $wpdb, $wp_version, $wp_db_version;
    
    ?>
    <div class="wrap">
        <h1>🔍 YTrip Full Debug</h1>
        
        <style>
            .debug-section { margin: 20px 0; border: 1px solid #ccc; padding: 20px; }
            .debug-success { background: #d4edda; color: #155724; }
            .debug-error { background: #f8d7da; color: #721c24; }
            .debug-warning { background: #fff3cd; color: #856404; }
            .debug-info { background: #d1ecf1; color: #0c5460; }
            .debug-table { width: 100%; border-collapse: collapse; }
            .debug-table th { background: #f5f5f5; padding: 8px; text-align: left; }
            .debug-table td { padding: 8px; border-bottom: 1px solid #ddd; }
            .status-yes { color: green; font-weight: bold; }
            .status-no { color: red; font-weight: bold; }
        </style>
        
        <?php
        // Current User
        $current_user = wp_get_current_user();
        ?>
        <div class="debug-section debug-info">
            <h2>👤 Current User</h2>
            <table class="debug-table">
                <tr>
                    <th>Username</th>
                    <td><?php echo esc_html($current_user->user_login); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo esc_html($current_user->user_email); ?></td>
                </tr>
                <tr>
                    <th>Display Name</th>
                    <td><?php echo esc_html($current_user->display_name); ?></td>
                </tr>
                <tr>
                    <th>User ID</th>
                    <td><?php echo esc_html($current_user->ID); ?></td>
                </tr>
                <tr>
                    <th>Roles</th>
                    <td><?php echo implode(', ', $current_user->roles); ?></td>
                </tr>
            </table>
        </div>
        
        <?php
        // Capabilities
        ?>
        <div class="debug-section debug-info">
            <h2>🔑 User Capabilities</h2>
            <table class="debug-table">
                <tr>
                    <th>Capability</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>manage_options</td>
                    <td class="<?php echo current_user_can('manage_options') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo current_user_can('manage_options') ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
                <tr>
                    <td>edit_theme_options</td>
                    <td class="<?php echo current_user_can('edit_theme_options') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo current_user_can('edit_theme_options') ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
                <tr>
                    <td>ytrip_settings</td>
                    <td class="<?php echo current_user_can('ytrip_settings') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo current_user_can('ytrip_settings') ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
                <tr>
                    <td>edit_posts</td>
                    <td class="<?php echo current_user_can('edit_posts') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo current_user_can('edit_posts') ? '✓ YES' : '✗ NO'; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
        // Framework Check
        ?>
        <div class="debug-section">
            <h2>📦 Framework Check</h2>
            <table class="debug-table">
                <tr>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>CSF</td>
                    <td class="<?php echo class_exists('CSF') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo class_exists('CSF') ? '✓ LOADED' : '✗ NOT LOADED'; ?>
                    </td>
                </tr>
                <tr>
                    <td>CSF_Setup</td>
                    <td class="<?php echo class_exists('CSF_Setup') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo class_exists('CSF_Setup') ? '✓ LOADED' : '✗ NOT LOADED'; ?>
                    </td>
                </tr>
                <tr>
                    <td>CSF_Options</td>
                    <td class="<?php echo class_exists('CSF_Options') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo class_exists('CSF_Options') ? '✓ LOADED' : '✗ NOT LOADED'; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
        // File Check
        $files_to_check = array(
            'codestar-framework.php' => YTRIP_PATH . 'vendor/codestar-framework/codestar-framework.php',
            'setup.class.php' => YTRIP_PATH . 'vendor/codestar-framework/classes/setup.class.php',
            'admin-options.class.php' => YTRIP_PATH . 'vendor/codestar-framework/classes/admin-options.class.php',
            'codestar-config.php' => YTRIP_PATH . 'admin/codestar-config.php',
            'simple-admin.php' => YTRIP_PATH . 'admin/simple-admin.php',
        );
        ?>
        <div class="debug-section">
            <h2>📁 File Check</h2>
            <table class="debug-table">
                <tr>
                    <th>File</th>
                    <th>Status</th>
                    <th>Size</th>
                </tr>
                <?php foreach ($files_to_check as $name => $path): ?>
                <tr>
                    <td><?php echo esc_html($name); ?></td>
                    <td class="<?php echo file_exists($path) ? 'status-yes' : 'status-no'; ?>">
                        <?php echo file_exists($path) ? '✓ EXISTS' : '✗ MISSING'; ?>
                    </td>
                    <td><?php echo file_exists($path) ? size_format(filesize($path)) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <?php
        // Options Check
        $ytrip_options = get_option('ytrip_settings');
        ?>
        <div class="debug-section debug-info">
            <h2>⚙️ Plugin Options</h2>
            <table class="debug-table">
                <tr>
                    <th>Option</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>ytrip_settings</td>
                    <td class="<?php echo is_array($ytrip_options) ? 'status-yes' : 'status-warning'; ?>">
                        <?php echo is_array($ytrip_options) ? '✓ SAVED (' . count($ytrip_options) . ' items)' : '⚠ NOT SAVED'; ?>
                    </td>
                </tr>
            </table>
            <?php if (is_array($ytrip_options)): ?>
                <h3>Saved Settings:</h3>
                <pre><?php print_r($ytrip_options); ?></pre>
            <?php endif; ?>
        </div>
        
        <?php
        // Post Types
        ?>
        <div class="debug-section">
            <h2>📝 Post Types</h2>
            <table class="debug-table">
                <tr>
                    <th>Post Type</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>tour</td>
                    <td class="<?php echo post_type_exists('tour') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo post_type_exists('tour') ? '✓ REGISTERED' : '✗ NOT FOUND'; ?>
                    </td>
                </tr>
                <tr>
                    <td>destination</td>
                    <td class="<?php echo post_type_exists('destination') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo post_type_exists('destination') ? '✓ REGISTERED' : '✗ NOT FOUND'; ?>
                    </td>
                </tr>
                <tr>
                    <td>booking</td>
                    <td class="<?php echo post_type_exists('booking') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo post_type_exists('booking') ? '✓ REGISTERED' : '✗ NOT FOUND'; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
        // Taxonomies
        ?>
        <div class="debug-section">
            <h2>🏷️ Taxonomies</h2>
            <table class="debug-table">
                <tr>
                    <th>Taxonomy</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>tour_category</td>
                    <td class="<?php echo taxonomy_exists('tour_category') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo taxonomy_exists('tour_category') ? '✓ REGISTERED' : '✗ NOT FOUND'; ?>
                    </td>
                </tr>
                <tr>
                    <td>tour_type</td>
                    <td class="<?php echo taxonomy_exists('tour_type') ? 'status-yes' : 'status-no'; ?>">
                        <?php echo taxonomy_exists('tour_type') ? '✓ REGISTERED' : '✗ NOT FOUND'; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php
        // WordPress Environment
        ?>
        <div class="debug-section debug-info">
            <h2>🌍 WordPress Environment</h2>
            <table class="debug-table">
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>WordPress Version</td>
                    <td><?php echo esc_html($wp_version); ?></td>
                </tr>
                <tr>
                    <td>PHP Version</td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td>MySQL Version</td>
                    <td><?php echo esc_html($wpdb->db_version()); ?></td>
                </tr>
                <tr>
                    <td>Memory Limit</td>
                    <td><?php echo ini_get('memory_limit'); ?></td>
                </tr>
                <tr>
                    <td>Max Execution Time</td>
                    <td><?php echo ini_get('max_execution_time'); ?>s</td>
                </tr>
            </table>
        </div>
        
        <?php
        // Active Plugins
        $active_plugins = get_option('active_plugins');
        ?>
        <div class="debug-section debug-info">
            <h2>🔌 Active Plugins</h2>
            <table class="debug-table">
                <tr>
                    <th>Plugin</th>
                </tr>
                <?php foreach ($active_plugins as $plugin): ?>
                <tr>
                    <td><?php echo esc_html($plugin); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <?php
        // Error Log (last 10 lines)
        if (file_exists(WP_CONTENT_DIR . '/debug.log')):
            $log_lines = array_slice(file(WP_CONTENT_DIR . '/debug.log'), -10);
            ?>
            <div class="debug-section debug-warning">
                <h2>📋 Error Log (Last 10 Lines)</h2>
                <pre><?php echo esc_html(implode('', $log_lines)); ?></pre>
            </div>
        <?php endif; ?>
        
        <div class="debug-section debug-success">
            <h2>✅ Next Steps</h2>
            <ol>
                <li>Check all <span style="color: red; font-weight: bold;">RED</span> items above</li>
                <li>If CSF is NOT LOADED, check file paths</li>
                <li>If capabilities are NO, check user roles</li>
                <li>Copy this page and share for support</li>
            </ol>
        </div>
    </div>
    <?php
}
