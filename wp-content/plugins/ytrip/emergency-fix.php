<?php
/**
 * YTrip EMERGENCY FIX SCRIPT
 * Upload this to: /wp-content/plugins/ytrip/emergency-fix.php
 * Then access: https://zakharioustours.de/wp-content/plugins/ytrip/emergency-fix.php
 *
 * This script will:
 * 1. Flush user roles and capabilities
 * 2. Check current user's role
 * 3. Add 'edit_posts' capability to user if missing
 * 4. Create demo content
 * 5. Flush permalinks
 */

// Load WordPress
require_once(ABSPATH . 'wp-load.php');

// Check authentication
if (!is_user_logged_in()) {
    wp_die('<h1>❌ Authentication Required</h1>
    <p>Please <a href="' . wp_login_url() . '">login to WordPress</a> first.</p>');
}

// Get current user
$current_user = wp_get_current_user();

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YTrip Emergency Fix</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #dc2626; margin-bottom: 20px; }
        h2 { color: #2563eb; margin: 20px 0 10px 0; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }
        .status-box { background: #f8fafc; border-left: 4px solid #2563eb; padding: 15px; margin: 15px 0; }
        .success { color: #16a34a; font-weight: 600; }
        .warning { color: #d97706; font-weight: 600; }
        .error { color: #dc2626; font-weight: 600; }
        button { display: inline-block; padding: 12px 24px; background: #2563eb; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 5px; text-decoration: none; }
        button:hover { background: #1d4ed8; }
        button:disabled { opacity: 0.5; cursor: not-allowed; }
        .section { margin: 30px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f8fafc; font-weight: 600; }
        .info { background: #dbeafe; border-left: 4px solid #3b82f6; padding: 15px; margin: 15px 0; }
        pre { background: #1e293b; padding: 15px; border-radius: 6px; overflow-x: auto; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #e11d48; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 YTrip Emergency Fix Tool</h1>
        <p><strong>Current User:</strong> ' . esc_html($current_user->display_name) . '</p>';

        // Process actions
        if (isset($_POST['action']) && wp_verify_nonce($_POST['_wpnonce'], 'ytrip_emergency_fix')) {
            $action = sanitize_text_field($_POST['action']);

            echo '<div class="status-box">';
            echo '<h2>Processing...</h2>';

            switch ($action) {
                case 'fix_roles':
                    ytrip_fix_user_roles();
                    break;
                case 'check_capabilities':
                    ytrip_check_capabilities();
                    break;
                case 'create_content':
                    ytrip_create_content();
                    break;
                case 'flush_permalinks':
                    ytrip_flush_permalinks();
                    break;
            }

            echo '</div>';
        }

        // Show current status
        ytrip_show_status();

        // Show actions
        echo '<div class="section">';
        echo '<h2>🚀 Emergency Fix Actions</h2>';

        echo '<form method="post">';
        wp_nonce_field('ytrip_emergency_fix');
        echo '<input type="hidden" name="action" value="fix_roles">';

        echo '<div class="status-box info">';
        echo '<h3>Step 1: Fix User Roles & Capabilities</h3>';
        echo '<p>This will:</p>';
        echo '<ul>';
        echo '<li>Flush all user roles and capabilities</li>';
        echo '<li>Reassign user to Administrator role if needed</li>';
        echo '<li>Add <strong>edit_posts</strong> capability to user</li>';
        echo '<li>Clear any capability caches</li>';
        echo '</ul>';
        echo '<button type="submit">🔧 Fix User Roles</button>';
        echo '</div>';

        echo '<div class="status-box info">';
        echo '<h3>Step 2: Create Demo Content</h3>';
        echo '<p>This will create:</p>';
        echo '<ul>';
        echo '<li>4 tour categories</li>';
        echo '<li>6 destinations</li>';
        echo '<li>24+ tours (4 per destination)</li>';
        echo '</ul>';
        echo '<button type="submit" name="action" value="create_content">📦 Create Content</button>';
        echo '</div>';

        echo '<div class="status-box info">';
        echo '<h3>Step 3: Flush Permalinks</h3>';
        echo '<p>This will:</p>';
        echo '<ul>';
        echo '<li>Flush rewrite rules</li>';
        echo '<li>Enable REST API endpoints</li>';
        echo '<li>Fix 404 errors for routes</li>';
        echo '</ul>';
        echo '<button type="submit" name="action" value="flush_permalinks">🔄 Flush Permalinks</button>';
        echo '</div>';

        echo '</form>';

        // Footer
        echo '<p style="margin-top: 30px; text-align: center; color: #6b7280;">';
        echo '<strong>⚠️ SECURITY:</strong> Delete this file after use for security.';
        echo '</p>';

    echo '</div>';
</body>
</html>';

/**
 * Functions
 */

function ytrip_show_status() {
    global $current_user;

    echo '<div class="section">';
    echo '<h2>📊 Current Status</h2>';

    echo '<table>';
    echo '<tr><th>Item</th><th>Status</th><th>Details</th></tr>';

    // User Role
    $roles = $current_user->roles;
    $is_admin = in_array('administrator', $roles);
    echo '<tr>';
    echo '<td><strong>User Role:</strong></td>';
    echo '<td>' . ($is_admin ? '<span class="success">Administrator</span>' : '<span class="warning">' . implode(', ', $roles) . '</span>') . '</td>';
    echo '<td>' . esc_html(implode(', ', $roles)) . '</td>';
    echo '</tr>';

    // Capabilities
    $can_edit = current_user_can('edit_posts');
    $can_manage = current_user_can('manage_options');
    $can_delete_plugins = current_user_can('delete_plugins');

    echo '<tr>';
    echo '<td><strong>edit_posts:</strong></td>';
    echo '<td>' . ($can_edit ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . '</td>';
    echo '<td>Required to access YTrip settings</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>manage_options:</strong></td>';
    echo '<td>' . ($can_manage ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . '</td>';
    echo '<td>Not required for YTrip</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>delete_plugins:</strong></td>';
    echo '<td>' . ($can_delete_plugins ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . '</td>';
    echo '<td>Required to delete/upload plugins</td>';
    echo '</tr>';

    // Current Content
    echo '<tr>';
    echo '<td><strong>Tours:</strong></td>';
    echo '<td>' . wp_count_posts('ytrip_tour')->publish . '</td>';
    echo '<td><a href="' . admin_url('edit.php?post_type=ytrip_tour') . '">Manage Tours</a></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>Categories:</strong></td>';
    echo '<td>' . wp_count_terms('ytrip_category') . '</td>';
    echo '<td><a href="' . admin_url('edit-tags.php?taxonomy=ytrip_category&post_type=ytrip_tour') . '">Manage Categories</a></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>Destinations:</strong></td>';
    echo '<td>' . wp_count_terms('ytrip_destination') . '</td>';
    echo '<td><a href="' . admin_url('edit-tags.php?taxonomy=ytrip_destination&post_type=ytrip_tour') . '">Manage Destinations</a></td>';
    echo '</tr>';

    echo '</table>';
    echo '</div>';
}

function ytrip_fix_user_roles() {
    global $current_user;

    echo '<h3>🔧 Fixing User Roles & Capabilities...</h3>';

    // Flush user roles
    wp_cache_delete('user_role', 'users');
    echo '<p>✓ Flushed user role cache</p>';

    // Check if user is Administrator
    if (!in_array('administrator', $current_user->roles)) {
        echo '<p class="warning">⚠️ User is NOT Administrator</p>';
        echo '<p>Current roles: ' . implode(', ', $current_user->roles) . '</p>';

        // Try to add Administrator role
        $current_user->set_role('administrator');
        echo '<p>✓ Assigned Administrator role</p>';

        // Clear caches
        wp_cache_flush();
        echo '<p>✓ Cleared all caches</p>';
    } else {
        echo '<p class="success">✓ User is already Administrator</p>';
    }

    // Ensure user has edit_posts capability
    if (!current_user_can('edit_posts')) {
        echo '<p class="warning">⚠️ User lacks edit_posts capability</p>';
        echo '<p>Adding capabilities manually...</p>';

        // Get the role object
        $user_role = get_role('administrator');

        if ($user_role) {
            // Add capabilities
            $user_role->add_cap('edit_posts');
            $user_role->add_cap('edit_pages');
            $user_role->add_cap('edit_others_posts');
            $user_role->add_cap('manage_categories');
            $user_role->add_cap('manage_options');

            echo '<p>✓ Added edit_posts, edit_pages, edit_others_posts, manage_categories, manage_options to Administrator role</p>';
        }

        // Remove from role and re-add
        $current_user->remove_role('administrator');
        $current_user->add_role('administrator');

        echo '<p>✓ Re-assigned user to Administrator role with full capabilities</p>';
    } else {
        echo '<p class="success">✓ User already has edit_posts capability</p>';
    }

    // Final flush
    wp_flush_roles();
    echo '<p class="success">✅ User roles and capabilities fixed!</p>';

    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ol>';
    echo '<li><a href="' . admin_url('admin.php?page=ytrip-settings') . '">Access YTrip Settings</a></li>';
    echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?action=create_content">Create Demo Content</a></li>';
    echo '<li><a href="' . $_SERVER['PHP_SELF'] . '?action=flush_permalinks">Flush Permalinks</a></li>';
    echo '</ol>';
}

function ytrip_check_capabilities() {
    echo '<h3>🔍 Checking Capabilities...</h3>';

    $caps = array(
        'edit_posts' => 'Edit posts/tours',
        'edit_pages' => 'Edit pages',
        'edit_others_posts' => 'Edit other users posts',
        'manage_options' => 'Manage WordPress options',
        'delete_plugins' => 'Delete/upload plugins',
    );

    echo '<table>';
    echo '<tr><th>Capability</th><th>Have It?</th><th>Purpose</th></tr>';

    foreach ($caps as $cap => $desc) {
        $has = current_user_can($cap);
        echo '<tr>';
        echo '<td><strong>' . $cap . ':</strong></td>';
        echo '<td>' . ($has ? '<span class="success">✓ Yes</span>' : '<span class="error">✗ No</span>') . '</td>';
        echo '<td>' . $desc . '</td>';
        echo '</tr>';
    }

    echo '</table>';

    // Check YTrip menu access
    echo '<h3>YTrip Menu Access</h3>';

    $has_simple_admin = current_user_can('edit_posts');
    $has_csf_access = current_user_can('manage_options');

    echo '<p>';
    if ($has_simple_admin && $has_csf_access) {
        echo '<span class="success">✅ You should be able to access YTrip settings</span>';
    } else {
        echo '<span class="error">❌ You cannot access YTrip settings yet</span>';
    }
    echo '</p>';

    if (!$has_simple_admin) {
        echo '<p class="warning">⚠️ Missing <strong>edit_posts</strong> capability</p>';
    }

    if (!$has_csf_access) {
        echo '<p class="warning">⚠️ Missing <strong>manage_options</strong> capability (optional for CSF settings)</p>';
    }
}

function ytrip_create_content() {
    echo '<h3>📦 Creating Demo Content...</h3>';

    // Categories
    $categories = array(
        array('name' => 'Adventure Tours', 'description' => 'Exciting adventures for thrill-seekers'),
        array('name' => 'Cultural Experiences', 'description' => 'Immerse yourself in local culture'),
        array('name' => 'Beach & Relaxation', 'description' => 'Sun, sand, and pure relaxation'),
        array('name' => 'City Breaks', 'description' => 'Explore vibrant cities and urban life'),
    );

    echo '<h4>Creating Categories...</h4><ul>';
    $category_ids = array();
    $created_cats = 0;

    foreach ($categories as $cat) {
        $term = get_term_by('name', $cat['name'], 'ytrip_category');
        if (!$term) {
            $result = wp_insert_term($cat['name'], 'ytrip_category', array(
                'description' => $cat['description'],
                'slug' => sanitize_title($cat['name']),
            ));
            if (!is_wp_error($result)) {
                $category_ids[] = $result['term_id'];
                echo '<li class="success">✓ Created: ' . esc_html($cat['name']) . '</li>';
                $created_cats++;
            } else {
                echo '<li class="error">✗ Failed: ' . esc_html($cat['name']) . '</li>';
            }
        } else {
            $category_ids[] = $term->term_id;
            echo '<li>ℹ Already exists: ' . esc_html($cat['name']) . '</li>';
        }
    }
    echo '</ul>';

    // Destinations
    $destinations = array(
        array('name' => 'Germany', 'description' => 'Beautiful landscapes and rich history'),
        array('name' => 'Egypt', 'description' => 'Ancient wonders and beautiful beaches'),
        array('name' => 'France', 'description' => 'Romantic cities and exquisite cuisine'),
        array('name' => 'Italy', 'description' => 'Art, history, and incredible food'),
        array('name' => 'Spain', 'description' => 'Vibrant culture and sunny beaches'),
        array('name' => 'Greece', 'description' => 'Ancient ruins and crystal waters'),
        array('name' => 'Thailand', 'description' => 'Tropical paradise and Buddhist temples'),
    );

    echo '<h4>Creating/Checking Destinations...</h4><ul>';
    $destination_ids = array();
    $created_dests = 0;

    foreach ($destinations as $dest) {
        $term = get_term_by('name', $dest['name'], 'ytrip_destination');
        if (!$term) {
            $result = wp_insert_term($dest['name'], 'ytrip_destination', array(
                'description' => $dest['description'],
                'slug' => sanitize_title($dest['name']),
            ));
            if (!is_wp_error($result)) {
                $destination_ids[] = $result['term_id'];
                echo '<li class="success">✓ Created: ' . esc_html($dest['name']) . '</li>';
                $created_dests++;
            } else {
                echo '<li class="error">✗ Failed: ' . esc_html($dest['name']) . '</li>';
            }
        } else {
            $destination_ids[] = $term->term_id;
            echo '<li>ℹ Already exists: ' . esc_html($dest['name']) . '</li>';
        }
    }
    echo '</ul>';

    // Tours
    $tour_templates = array(
        array('title' => 'Amazing {destination} Adventure', 'duration' => 7, 'price' => 1299),
        array('title' => '{destination} Cultural Journey', 'duration' => 10, 'price' => 1899),
        array('title' => 'Ultimate {destination} Experience', 'duration' => 14, 'price' => 2499),
        array('title' => '{destination} Explorer Package', 'duration' => 5, 'price' => 899),
    );

    echo '<h4>Creating Tours...</h4><ul>';
    $tour_count = 0;

    foreach ($destination_ids as $dest_id) {
        $destination = get_term($dest_id, 'ytrip_destination');
        $dest_name = $destination->name;

        foreach ($tour_templates as $template) {
            $title = str_replace('{destination}', $dest_name, $template['title']);
            $price = $template['price'] + rand(-100, 100);

            $post_id = wp_insert_post(array(
                'post_title' => $title,
                'post_content' => 'Experience the best of ' . $dest_name . ' with this incredible tour package. Visit iconic landmarks, taste local cuisine, and create unforgettable memories that will last a lifetime.',
                'post_status' => 'publish',
                'post_type' => 'ytrip_tour',
                'post_excerpt' => 'Experience the best of ' . $dest_name . ' with this incredible tour package. Visit iconic landmarks, taste local cuisine, and create unforgettable memories.',
            ));

            if (!is_wp_error($post_id)) {
                wp_set_object_terms($post_id, array($dest_id), 'ytrip_destination');

                if (!empty($category_ids)) {
                    $random_cat = $category_ids[array_rand($category_ids)];
                    wp_set_object_terms($post_id, array($random_cat), 'ytrip_category');
                }

                update_post_meta($post_id, 'ytrip_tour_details', array(
                    'duration' => $template['duration'],
                    'price' => $price,
                    'max_group_size' => rand(10, 30),
                    'min_age' => rand(6, 18),
                    'difficulty' => array('Easy', 'Moderate', 'Hard')[array_rand(array('Easy', 'Moderate', 'Hard'))],
                    'inquiry_email' => get_option('admin_email'),
                ));

                $tour_count++;
                echo '<li class="success">✓ Created: ' . esc_html($title) . ' - €' . $price . '</li>';
            } else {
                echo '<li class="error">✗ Failed: ' . esc_html($title) . '</li>';
            }
        }
    }
    echo '</ul>';

    echo '<p class="success"><strong>✅ Content Creation Complete!</strong></p>';
    echo '<ul>';
    echo '<li>Created ' . $created_cats . ' categories</li>';
    echo '<li>Created ' . $created_dests . ' destinations</li>';
    echo '<li>Created ' . $tour_count . ' tours</li>';
    echo '</ul>';

    echo '<p><a href="' . admin_url('edit.php?post_type=ytrip_tour') . '" class="button">View All Tours</a></p>';
}

function ytrip_flush_permalinks() {
    echo '<h3>🔄 Flushing Permalinks...</h3>';

    // Flush rewrite rules
    flush_rewrite_rules();

    echo '<p>✓ Flushed rewrite rules</p>';

    // Clear caches
    wp_cache_flush();

    echo '<p>✓ Cleared all caches</p>';

    echo '<p class="success"><strong>✅ Permalinks Flushed!</strong></p>';

    echo '<p>REST API should now be available at:</p>';
    echo '<code>https://zakharioustours.de/wp-json/ytrip/v1/test-access</code>';

    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ol>';
    echo '<li><a href="' . admin_url('admin.php?page=ytrip-settings') . '">Access YTrip Settings</a></li>';
    echo '<li><a href="' . admin_url('edit.php?post_type=ytrip_tour') . '">Manage Tours</a></li>';
    echo '<li><a href="https://zakharioustours.de/tours/">View Frontend Tours</a></li>';
    echo '</ol>';
}
