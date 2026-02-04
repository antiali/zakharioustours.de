<?php
/**
 * Plugin Name: Ultimate Tours Manager - Professional Edition
 * Plugin URI: https://example.com/ultimate-tours-manager
 * Description: إضافة احترافية وعالمية لإدارة الجولات السياحية مع لوحة تحكم متقدمة وتكامل كامل مع WooCommerce
 * Version: 2.0.0
 * Author: CallDigital
 * Author URI: https://calldigitalnow.com
 * Text Domain: ultimate-tours-manager
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('UTM_VERSION', '2.0.0');
define('UTM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UTM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UTM_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('UTM_PLUGIN_FILE', __FILE__);

/**
 * Main Ultimate Tours Manager Class
 */
class Ultimate_Tours_Manager {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
        $this->load_textdomain();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Codestar Framework
        require_once UTM_PLUGIN_DIR . 'includes/codestar-framework/codestar-framework.php';
        
        // Core classes
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-activator.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-deactivator.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-post-types.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-taxonomies.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-metaboxes.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-shortcodes.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-widgets.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-api.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-woocommerce.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-export-import.php';
        require_once UTM_PLUGIN_DIR . 'includes/class-utm-optimizer.php';
        
        // Admin classes
        if (is_admin()) {
            require_once UTM_PLUGIN_DIR . 'admin/class-utm-admin.php';
            require_once UTM_PLUGIN_DIR . 'admin/class-utm-settings.php';
        }
        
        // Frontend classes
        if (!is_admin()) {
            require_once UTM_PLUGIN_DIR . 'frontend/class-utm-frontend.php';
            require_once UTM_PLUGIN_DIR . 'frontend/class-utm-ajax.php';
        }
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array('UTM_Activator', 'activate'));
        register_deactivation_hook(__FILE__, array('UTM_Deactivator', 'deactivate'));
        
        add_action('plugins_loaded', array($this, 'init'));
        add_action('init', array($this, 'init_plugin'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize classes
        if (is_admin()) {
            UTM_Admin::get_instance();
            UTM_Settings::get_instance();
        }
        
        if (!is_admin()) {
            UTM_Frontend::get_instance();
        }
        
        UTM_Post_Types::get_instance();
        UTM_Taxonomies::get_instance();
        UTM_Metaboxes::get_instance();
        UTM_Shortcodes::get_instance();
        UTM_Widgets::get_instance();
        UTM_API::get_instance();
        UTM_WooCommerce::get_instance();
        UTM_Export_Import::get_instance();
        UTM_Optimizer::get_instance();
    }
    
    /**
     * Initialize plugin functionality
     */
    public function init_plugin() {
        // Flush rewrite rules if needed
        if (get_option('utm_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('utm_flush_rewrite_rules');
        }
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('ultimate-tours-manager', false, dirname(UTM_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Get plugin version
     */
    public static function get_version() {
        return UTM_VERSION;
    }
}

// Initialize the plugin
function utm() {
    return Ultimate_Tours_Manager::get_instance();
}

// Start the plugin
utm();
