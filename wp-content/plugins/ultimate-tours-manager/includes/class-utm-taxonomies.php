<?php
/**
 * UTM Taxonomies Class
 *
 * Register custom taxonomies
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Taxonomies {
    
    /**
     * Single instance
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
        add_action('init', array($this, 'register_destination_taxonomy'));
        add_action('init', array($this, 'register_tour_type_taxonomy'));
        add_action('init', array($this, 'register_tour_feature_taxonomy'));
        add_action('init', array($this, 'register_tour_duration_taxonomy'));
        add_action('init', array($this, 'register_tour_season_taxonomy'));
        
        // Add custom columns
        add_filter('manage_edit-destination_columns', array($this, 'taxonomy_columns'));
        add_filter('manage_edit-tour-type_columns', array($this, 'taxonomy_columns'));
        add_filter('manage_edit-tour-feature_columns', array($this, 'taxonomy_columns'));
        add_filter('manage_edit-tour-duration_columns', array($this, 'taxonomy_columns'));
        add_filter('manage_edit-tour-season_columns', array($this, 'taxonomy_columns'));
        
        // Add column data
        add_filter('manage_destination_custom_column', array($this, 'taxonomy_column_data'), 10, 3);
        add_filter('manage_tour-type_custom_column', array($this, 'taxonomy_column_data'), 10, 3);
        add_filter('manage_tour-feature_custom_column', array($this, 'taxonomy_column_data'), 10, 3);
        add_filter('manage_tour-duration_custom_column', array($this, 'taxonomy_column_data'), 10, 3);
        add_filter('manage_tour-season_custom_column', array($this, 'taxonomy_column_data'), 10, 3);
        
        // Add form fields
        add_action('destination_add_form_fields', array($this, 'add_taxonomy_fields'));
        add_action('destination_edit_form_fields', array($this, 'edit_taxonomy_fields'), 10, 2);
        add_action('tour-type_add_form_fields', array($this, 'add_taxonomy_fields'));
        add_action('tour-type_edit_form_fields', array($this, 'edit_taxonomy_fields'), 10, 2);
        add_action('tour-feature_add_form_fields', array($this, 'add_taxonomy_fields'));
        add_action('tour-feature_edit_form_fields', array($this, 'edit_taxonomy_fields'), 10, 2);
        
        // Save taxonomy fields
        add_action('created_destination', array($this, 'save_taxonomy_fields'));
        add_action('edited_destination', array($this, 'save_taxonomy_fields'));
        add_action('created_tour-type', array($this, 'save_taxonomy_fields'));
        add_action('edited_tour-type', array($this, 'save_taxonomy_fields'));
        add_action('created_tour-feature', array($this, 'save_taxonomy_fields'));
        add_action('edited_tour-feature', array($this, 'save_taxonomy_fields'));
        
        // Add taxonomy meta boxes for image
        add_action('destination_add_form_fields', array($this, 'add_taxonomy_image_field'));
        add_action('destination_edit_form_fields', array($this, 'edit_taxonomy_image_field'), 10, 2);
        add_action('tour-type_add_form_fields', array($this, 'add_taxonomy_image_field'));
        add_action('tour-type_edit_form_fields', array($this, 'edit_taxonomy_image_field'), 10, 2);
        add_action('tour-feature_add_form_fields', array($this, 'add_taxonomy_image_field'));
        add_action('tour-feature_edit_form_fields', array($this, 'edit_taxonomy_image_field'), 10, 2);
        
        // Save taxonomy image
        add_action('created_destination', array($this, 'save_taxonomy_image'));
        add_action('edited_destination', array($this, 'save_taxonomy_image'));
        add_action('created_tour-type', array($this, 'save_taxonomy_image'));
        add_action('edited_tour-type', array($this, 'save_taxonomy_image'));
        add_action('created_tour-feature', array($this, 'save_taxonomy_image'));
        add_action('edited_tour-feature', array($this, 'save_taxonomy_image'));
    }
    
    /**
     * Register Destination Taxonomy
     */
    public function register_destination_taxonomy() {
        $labels = array(
            'name' => __('Destinations', 'ultimate-tours-manager'),
            'singular_name' => __('Destination', 'ultimate-tours-manager'),
            'menu_name' => __('Destinations', 'ultimate-tours-manager'),
            'all_items' => __('All Destinations', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Destination', 'ultimate-tours-manager'),
            'view_item' => __('View Destination', 'ultimate-tours-manager'),
            'update_item' => __('Update Destination', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Destination', 'ultimate-tours-manager'),
            'new_item_name' => __('New Destination Name', 'ultimate-tours-manager'),
            'search_items' => __('Search Destinations', 'ultimate-tours-manager'),
            'popular_items' => __('Popular Destinations', 'ultimate-tours-manager'),
            'separate_items_with_commas' => __('Separate destinations with commas', 'ultimate-tours-manager'),
            'add_or_remove_items' => __('Add or remove destinations', 'ultimate-tours-manager'),
            'choose_from_most_used' => __('Choose from the most used destinations', 'ultimate-tours-manager'),
            'not_found' => __('No destinations found', 'ultimate-tours-manager'),
            'no_terms' => __('No destinations', 'ultimate-tours-manager'),
            'items_list_navigation' => __('Destinations list navigation', 'ultimate-tours-manager'),
            'items_list' => __('Destinations list', 'ultimate-tours-manager'),
            'most_used' => __('Most Used', 'ultimate-tours-manager'),
            'back_to_items' => __('← Back to Destinations', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour destinations', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'show_tagcloud' => true,
            'show_in_quick_edit' => true,
            'show_admin_column' => true,
            'meta_box_cb' => 'post_categories_meta_box',
            'query_var' => true,
            'rewrite' => array(
                'slug' => get_option('utm_destination_slug', 'destination'),
                'with_front' => true,
                'hierarchical' => true,
            ),
        );
        
        register_taxonomy('destination', array('tour'), $args);
    }
    
    /**
     * Register Tour Type Taxonomy
     */
    public function register_tour_type_taxonomy() {
        $labels = array(
            'name' => __('Tour Types', 'ultimate-tours-manager'),
            'singular_name' => __('Tour Type', 'ultimate-tours-manager'),
            'menu_name' => __('Tour Types', 'ultimate-tours-manager'),
            'all_items' => __('All Tour Types', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Tour Type', 'ultimate-tours-manager'),
            'view_item' => __('View Tour Type', 'ultimate-tours-manager'),
            'update_item' => __('Update Tour Type', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Tour Type', 'ultimate-tours-manager'),
            'new_item_name' => __('New Tour Type Name', 'ultimate-tours-manager'),
            'search_items' => __('Search Tour Types', 'ultimate-tours-manager'),
            'popular_items' => __('Popular Tour Types', 'ultimate-tours-manager'),
            'separate_items_with_commas' => __('Separate tour types with commas', 'ultimate-tours-manager'),
            'add_or_remove_items' => __('Add or remove tour types', 'ultimate-tours-manager'),
            'choose_from_most_used' => __('Choose from the most used tour types', 'ultimate-tours-manager'),
            'not_found' => __('No tour types found', 'ultimate-tours-manager'),
            'no_terms' => __('No tour types', 'ultimate-tours-manager'),
            'items_list_navigation' => __('Tour Types list navigation', 'ultimate-tours-manager'),
            'items_list' => __('Tour Types list', 'ultimate-tours-manager'),
            'most_used' => __('Most Used', 'ultimate-tours-manager'),
            'back_to_items' => __('← Back to Tour Types', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour categories and types', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'show_tagcloud' => true,
            'show_in_quick_edit' => true,
            'show_admin_column' => true,
            'meta_box_cb' => 'post_categories_meta_box',
            'query_var' => true,
            'rewrite' => array(
                'slug' => get_option('utm_tour_type_slug', 'tour-type'),
                'with_front' => true,
                'hierarchical' => true,
            ),
        );
        
        register_taxonomy('tour-type', array('tour'), $args);
    }
    
    /**
     * Register Tour Feature Taxonomy
     */
    public function register_tour_feature_taxonomy() {
        $labels = array(
            'name' => __('Tour Features', 'ultimate-tours-manager'),
            'singular_name' => __('Tour Feature', 'ultimate-tours-manager'),
            'menu_name' => __('Features', 'ultimate-tours-manager'),
            'all_items' => __('All Features', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Feature', 'ultimate-tours-manager'),
            'view_item' => __('View Feature', 'ultimate-tours-manager'),
            'update_item' => __('Update Feature', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Feature', 'ultimate-tours-manager'),
            'new_item_name' => __('New Feature Name', 'ultimate-tours-manager'),
            'search_items' => __('Search Features', 'ultimate-tours-manager'),
            'popular_items' => __('Popular Features', 'ultimate-tours-manager'),
            'separate_items_with_commas' => __('Separate features with commas', 'ultimate-tours-manager'),
            'add_or_remove_items' => __('Add or remove features', 'ultimate-tours-manager'),
            'choose_from_most_used' => __('Choose from the most used features', 'ultimate-tours-manager'),
            'not_found' => __('No features found', 'ultimate-tours-manager'),
            'no_terms' => __('No features', 'ultimate-tours-manager'),
            'items_list_navigation' => __('Features list navigation', 'ultimate-tours-manager'),
            'items_list' => __('Features list', 'ultimate-tours-manager'),
            'most_used' => __('Most Used', 'ultimate-tours-manager'),
            'back_to_items' => __('← Back to Features', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour features and amenities', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'show_tagcloud' => true,
            'show_in_quick_edit' => true,
            'show_admin_column' => true,
            'meta_box_cb' => 'post_tags_meta_box',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'tour-feature',
                'with_front' => true,
                'hierarchical' => false,
            ),
        );
        
        register_taxonomy('tour-feature', array('tour'), $args);
    }
    
    /**
     * Register Tour Duration Taxonomy
     */
    public function register_tour_duration_taxonomy() {
        $labels = array(
            'name' => __('Tour Durations', 'ultimate-tours-manager'),
            'singular_name' => __('Tour Duration', 'ultimate-tours-manager'),
            'menu_name' => __('Durations', 'ultimate-tours-manager'),
            'all_items' => __('All Durations', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Duration', 'ultimate-tours-manager'),
            'view_item' => __('View Duration', 'ultimate-tours-manager'),
            'update_item' => __('Update Duration', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Duration', 'ultimate-tours-manager'),
            'new_item_name' => __('New Duration Name', 'ultimate-tours-manager'),
            'search_items' => __('Search Durations', 'ultimate-tours-manager'),
            'popular_items' => __('Popular Durations', 'ultimate-tours-manager'),
            'separate_items_with_commas' => __('Separate durations with commas', 'ultimate-tours-manager'),
            'add_or_remove_items' => __('Add or remove durations', 'ultimate-tours-manager'),
            'choose_from_most_used' => __('Choose from the most used durations', 'ultimate-tours-manager'),
            'not_found' => __('No durations found', 'ultimate-tours-manager'),
            'no_terms' => __('No durations', 'ultimate-tours-manager'),
            'items_list_navigation' => __('Durations list navigation', 'ultimate-tours-manager'),
            'items_list' => __('Durations list', 'ultimate-tours-manager'),
            'most_used' => __('Most Used', 'ultimate-tours-manager'),
            'back_to_items' => __('← Back to Durations', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour duration categories', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'show_tagcloud' => false,
            'show_in_quick_edit' => false,
            'show_admin_column' => true,
            'meta_box_cb' => false,
            'query_var' => true,
            'rewrite' => false,
        );
        
        register_taxonomy('tour-duration', array('tour'), $args);
    }
    
    /**
     * Register Tour Season Taxonomy
     */
    public function register_tour_season_taxonomy() {
        $labels = array(
            'name' => __('Tour Seasons', 'ultimate-tours-manager'),
            'singular_name' => __('Tour Season', 'ultimate-tours-manager'),
            'menu_name' => __('Seasons', 'ultimate-tours-manager'),
            'all_items' => __('All Seasons', 'ultimate-tours-manager'),
            'edit_item' => __('Edit Season', 'ultimate-tours-manager'),
            'view_item' => __('View Season', 'ultimate-tours-manager'),
            'update_item' => __('Update Season', 'ultimate-tours-manager'),
            'add_new_item' => __('Add New Season', 'ultimate-tours-manager'),
            'new_item_name' => __('New Season Name', 'ultimate-tours-manager'),
            'search_items' => __('Search Seasons', 'ultimate-tours-manager'),
            'popular_items' => __('Popular Seasons', 'ultimate-tours-manager'),
            'separate_items_with_commas' => __('Separate seasons with commas', 'ultimate-tours-manager'),
            'add_or_remove_items' => __('Add or remove seasons', 'ultimate-tours-manager'),
            'choose_from_most_used' => __('Choose from the most used seasons', 'ultimate-tours-manager'),
            'not_found' => __('No seasons found', 'ultimate-tours-manager'),
            'no_terms' => __('No seasons', 'ultimate-tours-manager'),
            'items_list_navigation' => __('Seasons list navigation', 'ultimate-tours-manager'),
            'items_list' => __('Seasons list', 'ultimate-tours-manager'),
            'most_used' => __('Most Used', 'ultimate-tours-manager'),
            'back_to_items' => __('← Back to Seasons', 'ultimate-tours-manager'),
        );
        
        $args = array(
            'labels' => $labels,
            'description' => __('Tour seasons', 'ultimate-tours-manager'),
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'show_tagcloud' => false,
            'show_in_quick_edit' => false,
            'show_admin_column' => true,
            'meta_box_cb' => false,
            'query_var' => true,
            'rewrite' => false,
        );
        
        register_taxonomy('tour-season', array('tour'), $args);
    }
    
    /**
     * Add custom columns to taxonomy
     */
    public function taxonomy_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumb'] = __('Image', 'ultimate-tours-manager');
        $new_columns['name'] = $columns['name'];
        $new_columns['description'] = $columns['description'];
        $new_columns['slug'] = $columns['slug'];
        $new_columns['posts'] = $columns['posts'];
        
        return $new_columns;
    }
    
    /**
     * Add column data to taxonomy
     */
    public function taxonomy_column_data($string, $column_name, $term_id) {
        if ($column_name === 'thumb') {
            $image_id = get_term_meta($term_id, 'taxonomy_image_id', true);
            if ($image_id) {
                $image = wp_get_attachment_image($image_id, 'thumbnail');
                echo $image;
            }
        }
        return $string;
    }
    
    /**
     * Add taxonomy fields
     */
    public function add_taxonomy_fields($taxonomy) {
        ?>
        <div class="form-field term-icon-wrap">
            <label for="term-icon"><?php _e('Icon Class', 'ultimate-tours-manager'); ?></label>
            <input type="text" name="term_icon" id="term-icon" class="regular-text" placeholder="dashicons-admin-site">
            <p class="description"><?php _e('Enter Dashicon class name (e.g., dashicons-admin-site)', 'ultimate-tours-manager'); ?></p>
        </div>
        
        <div class="form-field term-color-wrap">
            <label for="term-color"><?php _e('Color', 'ultimate-tours-manager'); ?></label>
            <input type="text" name="term_color" id="term-color" class="color-picker" value="">
            <p class="description"><?php _e('Select a color for this term', 'ultimate-tours-manager'); ?></p>
        </div>
        
        <div class="form-field term-order-wrap">
            <label for="term_order"><?php _e('Order', 'ultimate-tours-manager'); ?></label>
            <input type="number" name="term_order" id="term_order" class="small-text" value="0" min="0">
            <p class="description"><?php _e('Order number for sorting', 'ultimate-tours-manager'); ?></p>
        </div>
        
        <?php
    }
    
    /**
     * Edit taxonomy fields
     */
    public function edit_taxonomy_fields($term, $taxonomy) {
        $icon = get_term_meta($term->term_id, 'term_icon', true);
        $color = get_term_meta($term->term_id, 'term_color', true);
        $order = get_term_meta($term->term_id, 'term_order', true);
        ?>
        <tr class="form-field term-icon-wrap">
            <th scope="row"><label for="term-icon"><?php _e('Icon Class', 'ultimate-tours-manager'); ?></label></th>
            <td>
                <input type="text" name="term_icon" id="term-icon" class="regular-text" value="<?php echo esc_attr($icon); ?>" placeholder="dashicons-admin-site">
                <p class="description"><?php _e('Enter Dashicon class name (e.g., dashicons-admin-site)', 'ultimate-tours-manager'); ?></p>
            </td>
        </tr>
        
        <tr class="form-field term-color-wrap">
            <th scope="row"><label for="term-color"><?php _e('Color', 'ultimate-tours-manager'); ?></label></th>
            <td>
                <input type="text" name="term_color" id="term-color" class="color-picker" value="<?php echo esc_attr($color); ?>">
                <p class="description"><?php _e('Select a color for this term', 'ultimate-tours-manager'); ?></p>
            </td>
        </tr>
        
        <tr class="form-field term-order-wrap">
            <th scope="row"><label for="term_order"><?php _e('Order', 'ultimate-tours-manager'); ?></label></th>
            <td>
                <input type="number" name="term_order" id="term_order" class="small-text" value="<?php echo esc_attr($order); ?>" min="0">
                <p class="description"><?php _e('Order number for sorting', 'ultimate-tours-manager'); ?></p>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Save taxonomy fields
     */
    public function save_taxonomy_fields($term_id) {
        if (isset($_POST['term_icon'])) {
            update_term_meta($term_id, 'term_icon', sanitize_text_field($_POST['term_icon']));
        }
        
        if (isset($_POST['term_color'])) {
            update_term_meta($term_id, 'term_color', sanitize_hex_color($_POST['term_color']));
        }
        
        if (isset($_POST['term_order'])) {
            update_term_meta($term_id, 'term_order', intval($_POST['term_order']));
        }
    }
    
    /**
     * Add taxonomy image field
     */
    public function add_taxonomy_image_field($taxonomy) {
        ?>
        <div class="form-field term-image-wrap">
            <label for="taxonomy-image"><?php _e('Image', 'ultimate-tours-manager'); ?></label>
            <div class="taxonomy-image-wrapper">
                <input type="hidden" name="taxonomy_image_id" id="taxonomy-image-id" value="">
                <div class="taxonomy-image-preview" id="taxonomy-image-preview"></div>
                <button type="button" class="button upload-image-button" id="upload-taxonomy-image">
                    <?php _e('Upload Image', 'ultimate-tours-manager'); ?>
                </button>
                <button type="button" class="button remove-image-button hidden" id="remove-taxonomy-image">
                    <?php _e('Remove Image', 'ultimate-tours-manager'); ?>
                </button>
            </div>
            <p class="description"><?php _e('Upload an image for this term', 'ultimate-tours-manager'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Edit taxonomy image field
     */
    public function edit_taxonomy_image_field($term, $taxonomy) {
        $image_id = get_term_meta($term->term_id, 'taxonomy_image_id', true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        ?>
        <tr class="form-field term-image-wrap">
            <th scope="row"><label for="taxonomy-image"><?php _e('Image', 'ultimate-tours-manager'); ?></label></th>
            <td>
                <div class="taxonomy-image-wrapper">
                    <input type="hidden" name="taxonomy_image_id" id="taxonomy-image-id" value="<?php echo esc_attr($image_id); ?>">
                    <div class="taxonomy-image-preview" id="taxonomy-image-preview">
                        <?php if ($image_url): ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button upload-image-button" id="upload-taxonomy-image">
                        <?php _e('Upload Image', 'ultimate-tours-manager'); ?>
                    </button>
                    <button type="button" class="button remove-image-button <?php echo $image_url ? '' : 'hidden'; ?>" id="remove-taxonomy-image">
                        <?php _e('Remove Image', 'ultimate-tours-manager'); ?>
                    </button>
                </div>
                <p class="description"><?php _e('Upload an image for this term', 'ultimate-tours-manager'); ?></p>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Save taxonomy image
     */
    public function save_taxonomy_image($term_id) {
        if (isset($_POST['taxonomy_image_id'])) {
            update_term_meta($term_id, 'taxonomy_image_id', intval($_POST['taxonomy_image_id']));
        }
    }
}
