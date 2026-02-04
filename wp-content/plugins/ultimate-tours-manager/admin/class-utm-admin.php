<?php
/**
 * UTM Admin Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'handle_admin_actions'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widgets'));
    }
    
    public function add_admin_menu() {
        // Dashboard
        add_submenu_page(
            'edit.php?post_type=tour',
            __('Dashboard', 'ultimate-tours-manager'),
            __('Dashboard', 'ultimate-tours-manager'),
            'manage_options',
            'utm-dashboard',
            array($this, 'render_dashboard')
        );
        
        // Reports
        add_submenu_page(
            'edit.php?post_type=tour',
            __('Reports', 'ultimate-tours-manager'),
            __('Reports', 'ultimate-tours-manager'),
            'manage_options',
            'utm-reports',
            array($this, 'render_reports')
        );
        
        // Calendar
        add_submenu_page(
            'edit.php?post_type=tour',
            __('Booking Calendar', 'ultimate-tours-manager'),
            __('Calendar', 'ultimate-tours-manager'),
            'manage_options',
            'utm-calendar',
            array($this, 'render_calendar')
        );
        
        // Enquiries
        add_submenu_page(
            'edit.php?post_type=tour',
            __('Enquiries', 'ultimate-tours-manager'),
            __('Enquiries', 'ultimate-tours-manager'),
            'manage_options',
            'utm-enquiries',
            array($this, 'render_enquiries')
        );
    }
    
    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        
        if ($screen->post_type !== 'tour' && strpos($hook, 'utm') === false) {
            return;
        }
        
        wp_enqueue_style(
            'utm-admin',
            UTM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            UTM_VERSION
        );
        
        wp_enqueue_script(
            'utm-admin',
            UTM_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            UTM_VERSION,
            true
        );
        
        wp_localize_script('utm-admin', 'utmAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('utm_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure?', 'ultimate-tours-manager'),
                'saving' => __('Saving...', 'ultimate-tours-manager'),
                'saved' => __('Saved!', 'ultimate-tours-manager'),
            ),
        ));
        
        // Chart.js for reports
        if ($hook === 'tour_page_utm-reports' || $hook === 'tour_page_utm-dashboard') {
            wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.1', true);
        }
        
        // FullCalendar for booking calendar
        if ($hook === 'tour_page_utm-calendar') {
            wp_enqueue_style('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css');
            wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js', array(), '6.1.10', true);
        }
    }
    
    public function handle_admin_actions() {
        // Handle duplicate tour
        if (isset($_GET['action']) && $_GET['action'] === 'utm_duplicate_tour') {
            $this->duplicate_tour(absint($_GET['post']));
        }
        
        // Handle generate invoice
        if (isset($_GET['action']) && $_GET['action'] === 'utm_generate_invoice') {
            $this->generate_invoice(absint($_GET['post']));
        }
    }
    
    private function duplicate_tour($post_id) {
        if (!wp_verify_nonce($_GET['_wpnonce'], 'utm_duplicate_tour_' . $post_id)) {
            wp_die(__('Security check failed', 'ultimate-tours-manager'));
        }
        
        $post = get_post($post_id);
        
        if (!$post) {
            wp_die(__('Tour not found', 'ultimate-tours-manager'));
        }
        
        $new_post = array(
            'post_title' => $post->post_title . ' (Copy)',
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status' => 'draft',
            'post_type' => 'tour',
            'post_author' => get_current_user_id(),
        );
        
        $new_post_id = wp_insert_post($new_post);
        
        // Copy meta
        $meta = get_post_meta($post_id);
        foreach ($meta as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_post_id, $key, maybe_unserialize($value));
            }
        }
        
        // Copy taxonomies
        $taxonomies = get_object_taxonomies('tour');
        foreach ($taxonomies as $taxonomy) {
            $terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'ids'));
            wp_set_object_terms($new_post_id, $terms, $taxonomy);
        }
        
        wp_redirect(admin_url('post.php?post=' . $new_post_id . '&action=edit'));
        exit;
    }
    
    private function generate_invoice($booking_id) {
        // Generate PDF invoice
    }
    
    public function admin_notices() {
        $screen = get_current_screen();
        
        // Show welcome notice
        if (get_option('utm_show_welcome') && $screen->post_type === 'tour') {
            ?>
            <div class="notice notice-success is-dismissible utm-welcome-notice">
                <h3><?php _e('Welcome to Ultimate Tours Manager!', 'ultimate-tours-manager'); ?></h3>
                <p><?php _e('Thank you for installing. Get started by creating your first tour.', 'ultimate-tours-manager'); ?></p>
                <p>
                    <a href="<?php echo admin_url('post-new.php?post_type=tour'); ?>" class="button button-primary">
                        <?php _e('Create Tour', 'ultimate-tours-manager'); ?>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=tour&page=utm-settings'); ?>" class="button">
                        <?php _e('Settings', 'ultimate-tours-manager'); ?>
                    </a>
                    <a href="#" class="utm-dismiss-welcome"><?php _e('Dismiss', 'ultimate-tours-manager'); ?></a>
                </p>
            </div>
            <?php
        }
    }
    
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget(
            'utm_bookings_widget',
            __('Recent Bookings', 'ultimate-tours-manager'),
            array($this, 'render_bookings_widget')
        );
        
        wp_add_dashboard_widget(
            'utm_stats_widget',
            __('Tour Statistics', 'ultimate-tours-manager'),
            array($this, 'render_stats_widget')
        );
    }
    
    public function render_bookings_widget() {
        $bookings = get_posts(array(
            'post_type' => 'booking',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        
        if (empty($bookings)) {
            echo '<p>' . __('No bookings yet.', 'ultimate-tours-manager') . '</p>';
            return;
        }
        
        echo '<table class="widefat"><tbody>';
        
        foreach ($bookings as $booking) {
            $booking_number = get_post_meta($booking->ID, '_booking_number', true);
            $status = get_post_meta($booking->ID, '_booking_status', true);
            $total = get_post_meta($booking->ID, '_total_price', true);
            
            echo '<tr>';
            echo '<td><a href="' . admin_url('post.php?post=' . $booking->ID . '&action=edit') . '">' . esc_html($booking_number) . '</a></td>';
            echo '<td><span class="status-' . $status . '">' . ucfirst($status) . '</span></td>';
            echo '<td>' . utm_format_price($total) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
        echo '<p><a href="' . admin_url('edit.php?post_type=booking') . '">' . __('View All Bookings', 'ultimate-tours-manager') . '</a></p>';
    }
    
    public function render_stats_widget() {
        $tours_count = wp_count_posts('tour')->publish;
        $bookings_count = wp_count_posts('booking')->publish;
        
        global $wpdb;
        $total_revenue = $wpdb->get_var(
            "SELECT SUM(meta_value) FROM {$wpdb->postmeta} 
            WHERE meta_key = '_total_price' 
            AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = 'booking' AND post_status = 'publish')"
        );
        
        ?>
        <div class="utm-stats-grid">
            <div class="utm-stat-box">
                <span class="utm-stat-value"><?php echo $tours_count; ?></span>
                <span class="utm-stat-label"><?php _e('Tours', 'ultimate-tours-manager'); ?></span>
            </div>
            <div class="utm-stat-box">
                <span class="utm-stat-value"><?php echo $bookings_count; ?></span>
                <span class="utm-stat-label"><?php _e('Bookings', 'ultimate-tours-manager'); ?></span>
            </div>
            <div class="utm-stat-box">
                <span class="utm-stat-value"><?php echo utm_format_price($total_revenue); ?></span>
                <span class="utm-stat-label"><?php _e('Revenue', 'ultimate-tours-manager'); ?></span>
            </div>
        </div>
        <?php
    }
    
    public function render_dashboard() {
        include UTM_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    public function render_reports() {
        include UTM_PLUGIN_DIR . 'admin/views/reports.php';
    }
    
    public function render_calendar() {
        include UTM_PLUGIN_DIR . 'admin/views/calendar.php';
    }
    
    public function render_enquiries() {
        include UTM_PLUGIN_DIR . 'admin/views/enquiries.php';
    }
}
