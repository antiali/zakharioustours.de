<?php
/*
Plugin Name: YTrip - Travel Booking Manager
Plugin URI: https://zakharioustours.de
Description: Professional travel/tourism booking system with minimal styling and CodeStar Framework integration.
Version: 1.0.0
Author: YTrip
Text Domain: ytrip
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Constants
define( 'YTRIP_VERSION', '1.0.0' );
define( 'YTRIP_FILE', __FILE__ );
define( 'YTRIP_PATH', plugin_dir_path( __FILE__ ) );
define( 'YTRIP_URL', plugin_dir_url( __FILE__ ) );
define( 'YTRIP_BASENAME', plugin_basename( __FILE__ ) );

// Include Framework
if ( ! class_exists( 'CSF' ) && file_exists( YTRIP_PATH . '/vendor/codestar-framework/codestar-framework.php' ) ) {
    require_once YTRIP_PATH . '/vendor/codestar-framework/codestar-framework.php';
}

// Main Class
if ( ! class_exists( 'YTrip' ) ) {
    class YTrip {
        
        private static $instance = null;

        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            $this->includes();
            $this->init_hooks();
        }

        private function includes() {
            // Core
            require_once YTRIP_PATH . 'includes/class-ytrip.php';
            require_once YTRIP_PATH . 'includes/class-post-types.php';
            require_once YTRIP_PATH . 'includes/class-taxonomies.php';
            require_once YTRIP_PATH . 'includes/class-woocommerce-integration.php';
            require_once YTRIP_PATH . 'includes/class-reviews.php';
            require_once YTRIP_PATH . 'includes/class-dynamic-css.php';
            require_once YTRIP_PATH . 'includes/class-shortcodes.php';
            require_once YTRIP_PATH . 'includes/class-ajax.php';
            require_once YTRIP_PATH . 'includes/class-template-loader.php';
            require_once YTRIP_PATH . 'includes/color-presets.php';
            require_once YTRIP_PATH . 'includes/helper-functions.php';
            require_once YTRIP_PATH . 'includes/class-schema-seo.php';
            require_once YTRIP_PATH . 'includes/class-archive-filters.php';
            require_once YTRIP_PATH . 'includes/class-performance.php';
            require_once YTRIP_PATH . 'includes/class-webp-generator.php';
            require_once YTRIP_PATH . 'includes/class-design-tokens.php';
            require_once YTRIP_PATH . 'includes/class-design-presets.php';
            require_once YTRIP_PATH . 'includes/class-helper.php';
            require_once YTRIP_PATH . 'includes/class-pricing-engine.php';
            require_once YTRIP_PATH . 'includes/class-rest-api.php';
            require_once YTRIP_PATH . 'includes/class-ical-sync.php';
            require_once YTRIP_PATH . 'includes/class-pdf-generator.php';
            require_once YTRIP_PATH . 'includes/class-tickets.php';
            require_once YTRIP_PATH . 'includes/class-agent-portal.php';

            // Admin
            if ( is_admin() ) {
                require_once YTRIP_PATH . 'admin/class-admin.php';
                require_once YTRIP_PATH . 'admin/codestar-config.php';
                require_once YTRIP_PATH . 'admin/homepage-builder.php';
                // Ensure metaboxes directory exists before requiring
                if ( file_exists( YTRIP_PATH . 'admin/metaboxes/tour-details.php' ) ) {
                    require_once YTRIP_PATH . 'admin/metaboxes/tour-details.php';
                }
            }

            // Public
            require_once YTRIP_PATH . 'public/class-frontend.php';
            require_once YTRIP_PATH . 'public/class-homepage.php';
            require_once YTRIP_PATH . 'public/class-search.php';
        }

        private function init_hooks() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        }

        public function load_textdomain() {
            load_plugin_textdomain( 'ytrip', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
    }

    function YTrip() {
        return YTrip::instance();
    }

    // Init Plugin
    add_action( 'plugins_loaded', 'YTrip' );
}
