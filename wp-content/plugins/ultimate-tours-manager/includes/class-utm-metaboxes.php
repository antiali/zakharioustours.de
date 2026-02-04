<?php
/**
 * UTM Metaboxes Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Metaboxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Metaboxes are handled by Codestar Framework in class-utm-settings.php
    }
}
