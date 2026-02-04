<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function ytrip_generate_dynamic_css() {
    $options = get_option('ytrip_settings');
    $preset = ytrip_get_active_color_preset();
    
    // Safety check if options not yet saved
    if ( empty( $preset ) || empty( $options ) ) {
        return;
    }
    
    $spacing = isset($options['section_spacing']) ? $options['section_spacing'] : 80;
    $radius = isset($options['border_radius']) ? $options['border_radius'] : 12;
    $shadow = isset($options['card_shadow']) ? $options['card_shadow'] : '0 4px 6px rgba(0,0,0,0.1)';

    $css = ":root {
        --ytrip-primary: {$preset['primary']};
        --ytrip-secondary: {$preset['secondary']};
        --ytrip-accent: {$preset['accent']};
        --ytrip-spacing: {$spacing}px;
        --ytrip-radius: {$radius}px;
        --ytrip-shadow: {$shadow};
    }";
    
    return $css;
}

add_action('wp_enqueue_scripts', function() {
    // wp_enqueue_style('ytrip-main', YTRIP_URL . 'assets/css/main.css'); // Ensure this file exists
    // wp_add_inline_style('ytrip-main', ytrip_generate_dynamic_css());
});
