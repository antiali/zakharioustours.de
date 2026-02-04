<?php
/**
 * Template Loader
 * Smart asset loading for YTrip plugin v2.1
 * Only loads CSS/JS on pages that need them for best performance
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class YTrip_Template_Loader {

    private $options;
    private $is_ytrip_page = false;

    public function __construct() {
        $this->options = get_option( 'ytrip_settings' );
        add_filter( 'template_include', array( $this, 'load_templates' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Check if current page is a YTrip page
     */
    private function is_ytrip_context() {
        // Single tour
        if ( is_singular( 'ytrip_tour' ) ) return true;
        
        // Tour archive
        if ( is_post_type_archive( 'ytrip_tour' ) ) return true;
        
        // Tour taxonomies
        if ( is_tax( 'ytrip_destination' ) || is_tax( 'ytrip_category' ) ) return true;
        
        // Homepage with YTrip sections (check if homepage_builder is used)
        if ( is_front_page() && ! empty( $this->options ) ) return true;
        
        // Pages with YTrip shortcodes (checked via content)
        global $post;
        if ( $post && has_shortcode( $post->post_content ?? '', 'ytrip_' ) ) return true;
        
        return false;
    }

    /**
     * Load custom templates based on settings
     */
    public function load_templates( $template ) {
        // Single Tour Layouts
        if ( is_singular( 'ytrip_tour' ) ) {
            $layout = $this->options['single_tour_layout'] ?? 'layout_1';
            
            $layout_files = array(
                'layout_1' => 'layout-1-classic.php',
                'layout_2' => 'layout-2-modern.php',
                'layout_3' => 'layout-3-split.php',
                'layout_4' => 'layout-4-booking.php',
                'layout_5' => 'layout-5-magazine.php',
            );

            $file = isset( $layout_files[$layout] ) ? $layout_files[$layout] : 'layout-1-classic.php';
            $custom = YTRIP_PATH . 'templates/single/' . $file;

            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }

        // Archive / Taxonomies
        if ( is_post_type_archive( 'ytrip_tour' ) || is_tax( 'ytrip_destination' ) || is_tax( 'ytrip_category' ) ) {
            $custom = YTRIP_PATH . 'templates/archive-ytrip_tour.php';
            if ( file_exists( $custom ) ) {
                return $custom;
            }
        }

        return $template;
    }

    /**
     * Smart asset enqueuing - only load what's needed
     */
    public function enqueue_assets() {
        // Early exit if not a YTrip page
        $this->is_ytrip_page = $this->is_ytrip_context();
        
        if ( ! $this->is_ytrip_page ) {
            return; // Don't load any YTrip assets on non-YTrip pages
        }

        // === CORE ASSETS (only on YTrip pages) ===
        $this->enqueue_core_assets();

        // === PAGE-SPECIFIC ASSETS ===
        
        // Single Tour Page
        if ( is_singular( 'ytrip_tour' ) ) {
            $this->enqueue_single_tour_assets();
        }
        
        // Archive/Taxonomy Pages
        if ( is_post_type_archive( 'ytrip_tour' ) || is_tax( 'ytrip_destination' ) || is_tax( 'ytrip_category' ) ) {
            $this->enqueue_archive_assets();
        }
        
        // Homepage
        if ( is_front_page() ) {
            $this->enqueue_homepage_assets();
        }

        // === OPTIONAL FEATURE ASSETS (based on settings) ===
        $this->enqueue_optional_features();

        // === LOCALIZE SCRIPT (only if main JS loaded) ===
        $this->localize_scripts();

        // === DYNAMIC CSS VARIABLES ===
        $this->add_dynamic_css();
    }

    /**
     * Core assets - minimal CSS/JS for YTrip functionality
     */
    private function enqueue_core_assets() {
        // Google Fonts - with display=swap for performance
        wp_enqueue_style(
            'ytrip-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap',
            array(),
            null
        );

        // Main CSS - core styles
        wp_enqueue_style( 
            'ytrip-main', 
            YTRIP_URL . 'assets/css/main.css', 
            array(), 
            YTRIP_VERSION 
        );

        // Main JS - core functionality
        wp_enqueue_script( 
            'ytrip-main', 
            YTRIP_URL . 'assets/js/main.js', 
            array( 'jquery' ), 
            YTRIP_VERSION, 
            true // Load in footer
        );
    }

    /**
     * Single tour page assets
     */
    private function enqueue_single_tour_assets() {
        $layout = $this->options['single_tour_layout'] ?? 'layout_1';
        
        $layout_css_map = array(
            'layout_1' => 'single-layout-1.css',
            'layout_2' => 'single-layout-2.css',
            'layout_3' => 'single-layout-3.css',
            'layout_4' => 'single-layout-4.css',
            'layout_5' => 'single-layout-5.css',
        );

        if ( isset( $layout_css_map[$layout] ) ) {
            wp_enqueue_style( 
                'ytrip-layout-' . $layout, 
                YTRIP_URL . 'assets/css/layouts/' . $layout_css_map[$layout], 
                array( 'ytrip-main' ), 
                YTRIP_VERSION 
            );
        }
    }

    /**
     * Archive page assets
     */
    private function enqueue_archive_assets() {
        // Archive filters CSS
        wp_enqueue_style( 
            'ytrip-archive-filters', 
            YTRIP_URL . 'assets/css/archive-filters.css', 
            array( 'ytrip-main' ), 
            YTRIP_VERSION 
        );
        
        // Card styles (needed for tour cards in archive)
        wp_enqueue_style( 
            'ytrip-cards', 
            YTRIP_URL . 'assets/css/cards/card-styles.css', 
            array( 'ytrip-main' ), 
            YTRIP_VERSION 
        );
        
        // Archive filters JS (only if AJAX enabled)
        $ajax_enabled = $this->options['archive_enable_ajax'] ?? true;
        if ( $ajax_enabled ) {
            wp_enqueue_script( 
                'ytrip-archive-filters', 
                YTRIP_URL . 'assets/js/archive-filters.js', 
                array( 'jquery' ), 
                YTRIP_VERSION, 
                true 
            );
        }
    }

    /**
     * Homepage assets
     */
    private function enqueue_homepage_assets() {
        // Card styles (needed for tour cards on homepage)
        wp_enqueue_style( 
            'ytrip-cards', 
            YTRIP_URL . 'assets/css/cards/card-styles.css', 
            array( 'ytrip-main' ), 
            YTRIP_VERSION 
        );
    }

    /**
     * Optional feature assets - only load if feature is enabled
     */
    private function enqueue_optional_features() {
        // Animations JS (scroll-triggered animations)
        $animations_enabled = $this->options['enable_animations'] ?? false;
        if ( $animations_enabled ) {
            wp_enqueue_script( 
                'ytrip-animations', 
                YTRIP_URL . 'assets/js/animations.js', 
                array(), // No jQuery dependency for better performance
                YTRIP_VERSION, 
                true 
            );
        }

        // Parallax JS (hero parallax effects)
        $parallax_enabled = $this->options['enable_parallax'] ?? false;
        if ( $parallax_enabled ) {
            wp_enqueue_script( 
                'ytrip-parallax', 
                YTRIP_URL . 'assets/js/parallax.js', 
                array(), 
                YTRIP_VERSION, 
                true 
            );
        }

        // Microinteractions JS - only on pages with interactive elements
        $microinteractions_enabled = $this->options['enable_microinteractions'] ?? true;
        if ( $microinteractions_enabled && ( is_singular( 'ytrip_tour' ) || is_front_page() ) ) {
            wp_enqueue_script( 
                'ytrip-microinteractions', 
                YTRIP_URL . 'assets/js/microinteractions.js', 
                array(), 
                YTRIP_VERSION, 
                true 
            );
        }
    }

    /**
     * Localize scripts with data
     */
    private function localize_scripts() {
        $data = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ytrip_filter_nonce' ),
        );

        // Only add feature flags if features are enabled
        if ( ! empty( $this->options['enable_animations'] ) ) {
            $data['enable_animations'] = '1';
        }
        if ( ! empty( $this->options['enable_parallax'] ) ) {
            $data['enable_parallax'] = '1';
        }
        if ( ! empty( $this->options['card_hover_effect'] ) ) {
            $data['card_hover_effect'] = $this->options['card_hover_effect'];
        }

        // Skeleton Loading
        if ( ! empty( $this->options['enable_skeleton_loading'] ) ) {
            $data['enable_skeleton'] = '1';
            
            // Generate skeleton HTML
            ob_start();
            $card_style = $this->options['tour_card_style'] ?? 'style_3';
            // Simple skeleton structure matching the CSS
            ?>
            <div class="ytrip-card ytrip-skeleton-card ytrip-card--<?php echo esc_attr( str_replace('style_', 'style-', $card_style) ); ?>">
                <div class="ytrip-skeleton-image"></div>
                <div class="ytrip-skeleton-content">
                    <div class="ytrip-skeleton-text ytrip-skeleton-tag"></div>
                    <div class="ytrip-skeleton-text ytrip-skeleton-title"></div>
                    <div class="ytrip-skeleton-text ytrip-skeleton-meta"></div>
                    <div class="ytrip-skeleton-footer">
                        <div class="ytrip-skeleton-text ytrip-skeleton-price"></div>
                        <div class="ytrip-skeleton-btn"></div>
                    </div>
                </div>
            </div>
            <?php
            $skeleton_html = ob_get_clean();
            $data['skeleton_html'] = $skeleton_html;
        }

        wp_localize_script( 'ytrip-main', 'ytrip_vars', $data );
    }

    /**
     * Generate minimal dynamic CSS based on settings
     */
    private function add_dynamic_css() {
        $primary     = $this->options['opt_color_primary'] ?? '#2563eb';
        $secondary   = $this->options['opt_color_secondary'] ?? '#1e40af';
        $accent      = $this->options['opt_color_accent'] ?? '#f59e0b';
        $radius_btn  = $this->options['opt_border_radius_btn'] ?? '6px';
        $radius_card = $this->options['opt_border_radius_card'] ?? '12px';

        // Minimal CSS - only custom properties
        $css = ":root{--ytrip-primary:{$primary};--ytrip-primary-dark:{$secondary};--ytrip-accent:{$accent};--ytrip-radius-btn:{$radius_btn};--ytrip-radius-card:{$radius_card};--ytrip-font-heading:'Outfit',sans-serif;--ytrip-font-body:'Inter',sans-serif}";

        // Smooth scroll (optional)
        if ( ! empty( $this->options['enable_smooth_scroll'] ) ) {
            $css .= "html{scroll-behavior:smooth}";
        }

        wp_add_inline_style( 'ytrip-main', $css );
    }
}

new YTrip_Template_Loader();
