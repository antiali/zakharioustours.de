<?php
/**
 * YTrip Debug/Testing Script
 * 
 * Run this to verify the plugin is working correctly
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../../../../');
}

echo "=== YTrip Plugin Test ===\n\n";

// Check if plugin is activated
echo "1. Checking plugin activation...\n";
if (is_plugin_active('ytrip/ytrip.php')) {
    echo "   ✓ Plugin is ACTIVE\n";
} else {
    echo "   ✗ Plugin is NOT active\n";
}

// Check CSF class
echo "\n2. Checking CSF Framework...\n";
if (class_exists('CSF')) {
    echo "   ✓ CSF class exists\n";
} else {
    echo "   ✗ CSF class NOT found\n";
}

// Check admin capabilities
echo "\n3. Checking admin capabilities...\n";
$user = wp_get_current_user();
if ($user && $user->ID) {
    echo "   Current user: {$user->display_name}\n";
    echo "   - Can manage_options: " . (current_user_can('manage_options') ? '✓' : '✗') . "\n";
    echo "   - Can edit_theme_options: " . (current_user_can('edit_theme_options') ? '✓' : '✗') . "\n";
    echo "   - Can ytrip_settings: " . (current_user_can('ytrip_settings') ? '✓' : '✗') . "\n";
} else {
    echo "   No user logged in\n";
}

// Check post types
echo "\n4. Checking post types...\n";
if (post_type_exists('tour')) {
    echo "   ✓ Tour post type exists\n";
} else {
    echo "   ✗ Tour post type NOT found\n";
}

if (post_type_exists('destination')) {
    echo "   ✓ Destination post type exists\n";
} else {
    echo "   ✗ Destination post type NOT found\n";
}

// Check taxonomies
echo "\n5. Checking taxonomies...\n";
if (taxonomy_exists('tour_category')) {
    echo "   ✓ Tour category exists\n";
} else {
    echo "   ✗ Tour category NOT found\n";
}

if (taxonomy_exists('tour_type')) {
    echo "   ✓ Tour type exists\n";
} else {
    echo "   ✗ Tour type NOT found\n";
}

// Check settings page
echo "\n6. Checking settings page configuration...\n";
$ytrip_options = get_option('ytrip_settings');
if (is_array($ytrip_options)) {
    echo "   ✓ Settings option exists (count: " . count($ytrip_options) . ")\n";
} else {
    echo "   ℹ Settings not yet saved (normal for first run)\n";
}

echo "\n=== Test Complete ===\n";
