<?php
/**
 * YTrip Frontend Assets Fix
 * 
 * Removes all external CDN dependencies and uses local assets
 * @package YTrip
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class YTrip_Frontend_Fix {
    
    public static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Dequeue all external CDN scripts
        add_action('wp_enqueue_scripts', array($this, 'dequeue_external_scripts'));
        add_action('wp_enqueue_styles', array($this, 'dequeue_external_styles'));
    }
    
    /**
     * Dequeue external CDN scripts that cause 404 errors
     */
    public function dequeue_external_scripts() {
        // Remove all known external CDN scripts causing issues
        $external_scripts = array(
            'style.min.js',
            'v4-shims.min.js',
            'main.min.js',
            'plugins.min.js',
            'refresh.js',
        );
        
        foreach ($external_scripts as $script) {
            wp_dequeue_script($script);
            wp_deregister_script($script);
        }
        
        // Also remove WebSocket related scripts (development only)
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            wp_dequeue_script('refresh');
        }
    }
    
    /**
     * Dequeue external CDN styles that cause 404 errors
     */
    public function dequeue_external_styles() {
        // Remove all known external CDN styles
        $external_styles = array(
            'style.min.css',
            'v4-shims.min.css',
            'all.min.css',
            'style.min.css',
            'classic.css',
        );
        
        foreach ($external_styles as $style) {
            wp_dequeue_style($style);
            wp_deregister_style($style);
        }
    }
    
    /**
     * Add inline styles for basic functionality
     */
    public function add_inline_styles() {
        ?>
        <style>
            /* Basic YTrip styles */
            .ytrip-booking-form {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: #f5f5f5;
                border-radius: 8px;
            }
            
            .ytrip-booking-form .form-row {
                margin-bottom: 15px;
            }
            
            .ytrip-booking-form label {
                display: block;
                margin-bottom: 5px;
                font-weight: 600;
                color: #333;
            }
            
            .ytrip-booking-form input[type="text"],
            .ytrip-booking-form input[type="email"],
            .ytrip-booking-form input[type="date"],
            .ytrip-booking-form select,
            .ytrip-booking-form textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            
            .ytrip-booking-form button {
                background: #0073aa;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
            }
            
            .ytrip-booking-form button:hover {
                background: #0056b3;
            }
        </style>
        <?php
    }
}

// Initialize
function ytrip_frontend_fix() {
    return YTrip_Frontend_Fix::instance();
}
