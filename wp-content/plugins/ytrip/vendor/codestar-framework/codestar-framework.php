<?php
/**
 * Codestar Framework - Main Entry Point
 *
 * @package   Codestar_Framework
 * @author    Codestar
 * @link      https://codestarframework.com
 * @version   2.2.9
 */

if (!defined('ABSPATH')) {
    die;
}

// Check if framework is already loaded
if (class_exists('CSF')) {
    return;
}

// Framework path
$csf_dir = dirname(__FILE__) . '/';

// Load setup class first
require_once $csf_dir . 'classes/setup.class.php';

/**
 * Main CSF Class - Facade for CSF_Setup
 */
if (!class_exists('CSF')) {
    class CSF extends CSF_Setup {
        
        /**
         * Create admin options
         */
        public static function createOptions($prefix, $args = array()) {
            self::$args['admin_options'][$prefix] = $args;
        }
        
        /**
         * Create admin section
         */
        public static function createSection($prefix, $args = array()) {
            if (!isset(self::$args['sections'][$prefix])) {
                self::$args['sections'][$prefix] = array();
            }
            self::$args['sections'][$prefix][] = $args;
        }
        
        /**
         * Create metabox
         */
        public static function createMetabox($prefix, $args = array()) {
            self::$args['metabox_options'][$prefix] = $args;
        }
        
        /**
         * Create taxonomy options
         */
        public static function createTaxonomyOptions($prefix, $args = array()) {
            self::$args['taxonomy_options'][$prefix] = $args;
        }
        
        /**
         * Create customize options
         */
        public static function createCustomizeOptions($prefix, $args = array()) {
            self::$args['customize_options'][$prefix] = $args;
        }
        
        /**
         * Create nav menu options
         */
        public static function createNavMenuOptions($prefix, $args = array()) {
            self::$args['nav_menu_options'][$prefix] = $args;
        }
        
        /**
         * Create profile options
         */
        public static function createProfileOptions($prefix, $args = array()) {
            self::$args['profile_options'][$prefix] = $args;
        }
        
        /**
         * Create widget
         */
        public static function createWidget($prefix, $args = array()) {
            self::$args['widget_options'][$prefix] = $args;
        }
        
        /**
         * Create comment metabox
         */
        public static function createCommentMetabox($prefix, $args = array()) {
            self::$args['comment_options'][$prefix] = $args;
        }
        
        /**
         * Create shortcoder
         */
        public static function createShortcoder($prefix, $args = array()) {
            self::$args['shortcode_options'][$prefix] = $args;
        }
        
        /**
         * Get option value
         */
        public static function getOption($prefix = '', $key = '', $default = null) {
            $options = get_option($prefix, array());
            
            if (empty($key)) {
                return $options;
            }
            
            return isset($options[$key]) ? $options[$key] : $default;
        }
        
        /**
         * Set option value
         */
        public static function setOption($prefix = '', $key = '', $value = '') {
            $options = get_option($prefix, array());
            $options[$key] = $value;
            return update_option($prefix, $options);
        }
    }
}

// Initialize the framework ONLY if not already initialized by setup.class.php
if (!class_exists('CSF_Setup') || empty(CSF_Setup::$instance)) {
    CSF::init(__FILE__, true);
}

// Load additional classes
$csf_classes = array(
    'abstract.class.php',
    'fields.class.php',
    'admin-options.class.php',
    'metabox-options.class.php',
    'taxonomy-options.class.php',
    'customize-options.class.php',
    'nav-menu-options.class.php',
    'profile-options.class.php',
    'widget-options.class.php',
    'comment-options.class.php',
    'shortcode-options.class.php',
);

foreach ($csf_classes as $class_file) {
    $class_path = $csf_dir . 'classes/' . $class_file;
    if (file_exists($class_path)) {
        require_once $class_path;
    }
}

// Load all field types
$fields_dir = $csf_dir . 'fields/';
if (is_dir($fields_dir)) {
    foreach (glob($fields_dir . '*.php') as $field_file) {
        require_once $field_file;
    }
}

// Load functions
$functions_dir = $csf_dir . 'functions/';
if (is_dir($functions_dir)) {
    foreach (glob($functions_dir . '*.php') as $func_file) {
        require_once $func_file;
    }
}
