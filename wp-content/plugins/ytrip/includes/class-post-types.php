<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class YTrip_Post_Types {
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
    }
    public function register_post_types() {
        register_post_type('ytrip_tour', array(
            'labels' => array(
                'name' => 'Tours',
                'singular_name' => 'Tour'
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes'),
            'show_in_rest' => true,
        ));
    }
}
new YTrip_Post_Types();
