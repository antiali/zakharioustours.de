<?php
/**
 * YTrip Asset Debug Tool
 * Helps identify 404 errors and missing assets
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_init', 'ytrip_asset_debug_page' );

function ytrip_asset_debug_page() {
    // Only show if debug mode is on or user has specific capability
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Check if debug action is triggered
    if ( isset( $_GET['ytrip_debug_assets'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'ytrip_debug_assets' ) ) {
        ytrip_display_asset_debug();
        exit;
    }
}

function ytrip_display_asset_debug() {
    echo '<!DOCTYPE html><html><head><title>YTrip Asset Debug</title>';
    echo '<style>body{font-family:monospace;padding:20px;background:#f0f0f0;}';
    echo '.error{color:#d63638;}.success{color:#00a32a;}.warning{color:#dba617;}';
    echo 'table{border-collapse:collapse;width:100%;background:#fff;}';
    echo 'th,td{border:1px solid #ddd;padding:8px;text:left;}';
    echo 'th{background:#0073aa;color:#fff;}</style></head><body>';
    echo '<h1>🔍 YTrip Asset Debug Report</h1>';
    echo '<p>Generated: ' . date( 'Y-m-d H:i:s' ) . '</p>';

    echo '<h2>📁 CSS Files</h2>';
    ytrip_check_css_files();

    echo '<h2>📄 JS Files</h2>';
    ytrip_check_js_files();

    echo '<h2>🎨 Google Fonts</h2>';
    ytrip_check_google_fonts();

    echo '<p><a href="' . admin_url() . '">← Back to Admin</a></p>';
    echo '</body></html>';
}

function ytrip_check_css_files() {
    $css_files = array(
        'optimized-main.css' => YTRIP_PATH . 'assets/css/optimized-main.css',
        'main.css' => YTRIP_PATH . 'assets/css/main.css',
        'card-styles.css' => YTRIP_PATH . 'assets/css/cards/card-styles.css',
        'archive-filters.css' => YTRIP_PATH . 'assets/css/archive-filters.css',
        'single-layout-1.css' => YTRIP_PATH . 'assets/css/layouts/single-layout-1.css',
        'single-layout-2.css' => YTRIP_PATH . 'assets/css/layouts/single-layout-2.css',
        'single-layout-3.css' => YTRIP_PATH . 'assets/css/layouts/single-layout-3.css',
        'single-layout-4.css' => YTRIP_PATH . 'assets/css/layouts/single-layout-4.css',
        'single-layout-5.css' => YTRIP_PATH . 'assets/css/layouts/single-layout-5.css',
    );

    echo '<table><tr><th>File</th><th>Status</th><th>URL</th></tr>';

    foreach ( $css_files as $name => $path ) {
        $exists = file_exists( $path );
        $url = YTRIP_URL . str_replace( YTRIP_PATH, '', $path );
        $status = $exists ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Missing</span>';

        echo '<tr>';
        echo '<td>' . $name . '</td>';
        echo '<td>' . $status . '</td>';
        echo '<td><code>' . $url . '</code></td>';
        echo '</tr>';
    }

    echo '</table>';
}

function ytrip_check_js_files() {
    $js_files = array(
        'main.js' => YTRIP_PATH . 'assets/js/main.js',
        'animations.js' => YTRIP_PATH . 'assets/js/animations.js',
        'parallax.js' => YTRIP_PATH . 'assets/js/parallax.js',
        'microinteractions.js' => YTRIP_PATH . 'assets/js/microinteractions.js',
        'archive-filters.js' => YTRIP_PATH . 'assets/js/archive-filters.js',
    );

    echo '<table><tr><th>File</th><th>Status</th><th>URL</th></tr>';

    foreach ( $js_files as $name => $path ) {
        $exists = file_exists( $path );
        $url = YTRIP_URL . str_replace( YTRIP_PATH, '', $path );
        $status = $exists ? '<span class="success">✓ Exists</span>' : '<span class="error">✗ Missing</span>';

        echo '<tr>';
        echo '<td>' . $name . '</td>';
        echo '<td>' . $status . '</td>';
        echo '<td><code>' . $url . '</code></td>';
        echo '</tr>';
    }

    echo '</table>';
}

function ytrip_check_google_fonts() {
    echo '<table><tr><th>Font</th><th>Status</th></tr>';

    $fonts = array(
        'Inter (Google Fonts)' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        'Outfit (Google Fonts)' => 'https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&display=swap',
    );

    foreach ( $fonts as $name => $url ) {
        $response = wp_remote_get( $url, array( 'timeout' => 5 ) );
        $status = is_wp_error( $response ) ? '<span class="error">✗ Failed</span>' : '<span class="success">✓ OK</span>';

        echo '<tr>';
        echo '<td>' . $name . '</td>';
        echo '<td>' . $status . '</td>';
        echo '</tr>';
    }

    echo '</table>';
}
