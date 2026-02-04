<?php
/**
 * Dynamic CSS Generator & Design System
 * Handles dynamic coloring, typography, and layout settings with preset palettes.
 * 
 * @package YTrip
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Dynamic_CSS {

    private $options;

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_styles' ), 20 );
        add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
    }

    /**
     * Enqueue the dynamic CSS
     */
    public function enqueue_dynamic_styles() {
        $this->options = get_option( 'ytrip_settings' );
        
        if ( wp_style_is( 'ytrip-main', 'enqueued' ) ) {
            wp_add_inline_style( 'ytrip-main', $this->generate_css() );
        }
    }

    /**
     * Top 10 Tourism Color Palettes (Contrast Safe)
     */
    public static function get_color_presets() {
        return array(
            'ocean_breeze' => array(
                'name'      => __( 'Ocean Breeze', 'ytrip' ),
                'primary'   => '#0284c7', // Sky 600
                'secondary' => '#0ea5e9', // Sky 500
                'accent'    => '#f59e0b', // Amber 500
                'bg'        => '#f0f9ff', // Sky 50
                'text'      => '#0c4a6e', // Sky 900
            ),
            'desert_safari' => array(
                'name'      => __( 'Desert Safari', 'ytrip' ),
                'primary'   => '#d97706', // Amber 600
                'secondary' => '#b45309', // Amber 700
                'accent'    => '#10b981', // Emerald 500 (Oasis)
                'bg'        => '#fffbeb', // Amber 50
                'text'      => '#451a03', // Amber 950
            ),
            'forest_hike' => array(
                'name'      => __( 'Forest Hike', 'ytrip' ),
                'primary'   => '#15803d', // Green 700
                'secondary' => '#166534', // Green 800
                'accent'    => '#84cc16', // Lime 500
                'bg'        => '#f0fdf4', // Green 50
                'text'      => '#052e16', // Green 950
            ),
            'sunset_beach' => array(
                'name'      => __( 'Sunset Beach', 'ytrip' ),
                'primary'   => '#db2777', // Pink 600
                'secondary' => '#7c3aed', // Violet 600
                'accent'    => '#facc15', // Yellow 400
                'bg'        => '#fff1f2', // Rose 50
                'text'      => '#4c0519', // Rose 950
            ),
            'royal_luxury' => array(
                'name'      => __( 'Royal Luxury', 'ytrip' ),
                'primary'   => '#1e1b4b', // Indigo 950
                'secondary' => '#312e81', // Indigo 900
                'accent'    => '#d4af37', // Gold (Custom)
                'bg'        => '#f8fafc', // Slate 50
                'text'      => '#0f172a', // Slate 900
            ),
            'urban_explorer' => array(
                'name'      => __( 'Urban Explorer', 'ytrip' ),
                'primary'   => '#2563eb', // Blue 600
                'secondary' => '#475569', // Slate 600
                'accent'    => '#ef4444', // Red 500
                'bg'        => '#ffffff', // White
                'text'      => '#1e293b', // Slate 800
            ),
            'tropical_island' => array(
                'name'      => __( 'Tropical Island', 'ytrip' ),
                'primary'   => '#0d9488', // Teal 600
                'secondary' => '#0891b2', // Cyan 600
                'accent'    => '#fb923c', // Orange 400
                'bg'        => '#ecfeff', // Cyan 50
                'text'      => '#134e4a', // Teal 900
            ),
            'mountain_peak' => array(
                'name'      => __( 'Mountain Peak', 'ytrip' ),
                'primary'   => '#4b5563', // Gray 600
                'secondary' => '#374151', // Gray 700
                'accent'    => '#3b82f6', // Blue 500
                'bg'        => '#f9fafb', // Gray 50
                'text'      => '#111827', // Gray 900
            ),
            'cultural_heritage' => array(
                'name'      => __( 'Cultural Heritage', 'ytrip' ),
                'primary'   => '#9f1239', // Rose 800
                'secondary' => '#881337', // Rose 900
                'accent'    => '#fbbf24', // Amber 400
                'bg'        => '#fff5f5', // Warm Gray
                'text'      => '#4a044e', // Fuchsia 950
            ),
            'northern_lights' => array(
                'name'      => __( 'Northern Lights', 'ytrip' ),
                'primary'   => '#4c1d95', // Violet 900
                'secondary' => '#5b21b6', // Violet 800
                'accent'    => '#22d3ee', // Cyan 400
                'bg'        => '#f5f3ff', // Violet 50
                'text'      => '#1e1b4b', // Indigo 950
            ),
        );
    }

    /**
     * Generate CSS String
     */
    private function generate_css() {
        // 1. Determine Colors (Preset vs Custom)
        $preset_key = isset( $this->options['color_preset'] ) ? $this->options['color_preset'] : 'ocean_breeze';
        $presets    = self::get_color_presets();
        
        // Default to preset values
        $active_preset = isset( $presets[$preset_key] ) ? $presets[$preset_key] : $presets['ocean_breeze'];
        
        // Allow custom overrides if 'custom' is selected or specific fields are filled
        $primary   = ! empty( $this->options['primary_color'] )   ? $this->options['primary_color']   : $active_preset['primary'];
        $secondary = ! empty( $this->options['secondary_color'] ) ? $this->options['secondary_color'] : $active_preset['secondary'];
        $accent    = ! empty( $this->options['accent_color'] )    ? $this->options['accent_color']    : $active_preset['accent'];
        $bg        = ! empty( $this->options['bg_color'] )        ? $this->options['bg_color']        : $active_preset['bg'];
        $text      = ! empty( $this->options['text_color'] )      ? $this->options['text_color']      : $active_preset['text'];

        $radius    = isset( $this->options['border_radius'] )     ? intval( $this->options['border_radius'] ) : 12;
        $spacing   = isset( $this->options['section_spacing'] )   ? intval( $this->options['section_spacing'] ) : 80;
        
        $font_heading = ! empty( $this->options['font_heading'] ) ? $this->options['font_heading'] : 'Outfit';
        $font_body    = ! empty( $this->options['font_body'] )    ? $this->options['font_body']    : 'Inter';

        // 2. Calculate Variants
        $primary_rgb   = $this->hex2rgb( $primary );
        
        // 3. Build CSS
        ob_start();
        ?>
        :root {
            /* --- Brand Colors --- */
            --ytrip-primary: <?php echo esc_attr( $primary ); ?>;
            --ytrip-primary-rgb: <?php echo esc_attr( $primary_rgb ); ?>;
            --ytrip-primary-light: <?php echo esc_attr( $this->adjust_brightness( $primary, 20 ) ); ?>;
            --ytrip-primary-dark: <?php echo esc_attr( $this->adjust_brightness( $primary, -20 ) ); ?>;
            
            --ytrip-secondary: <?php echo esc_attr( $secondary ); ?>;
            --ytrip-accent: <?php echo esc_attr( $accent ); ?>;
            
            --ytrip-background: <?php echo esc_attr( $bg ); ?>;
            --ytrip-surface: #ffffff; /* Always keep surface white/card-bg */
            --ytrip-text: <?php echo esc_attr( $text ); ?>;
            
            /* --- Typography --- */
            --ytrip-font: "<?php echo esc_attr( $font_body ); ?>", system-ui, sans-serif;
            --ytrip-font-display: "<?php echo esc_attr( $font_heading ); ?>", sans-serif;
            
            /* --- Dimensions --- */
            --ytrip-radius: <?php echo intval( $radius ); ?>px;
            --ytrip-radius-sm: <?php echo intval( $radius / 2 ); ?>px;
            --ytrip-spacing: <?php echo intval( $spacing ); ?>px;
        }
        <?php
        return $this->minify_css( ob_get_clean() );
    }

    private function hex2rgb( $hex ) {
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
            $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
            $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
        } else {
            $r = hexdec( substr( $hex, 0, 2 ) );
            $g = hexdec( substr( $hex, 2, 2 ) );
            $b = hexdec( substr( $hex, 4, 2 ) );
        }
        return "{$r}, {$g}, {$b}";
    }

    private function adjust_brightness( $hex, $steps ) {
        $steps = max( -255, min( 255, $steps ) );
        $hex = str_replace( '#', '', $hex );
        if ( strlen( $hex ) == 3 ) {
            $hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
        }
        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );

        $r = max( 0, min( 255, $r + $steps ) );
        $g = max( 0, min( 255, $g + $steps ) );
        $b = max( 0, min( 255, $b + $steps ) );

        return '#' . str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT ) . str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
    }

    private function minify_css( $css ) {
        return str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
    }
    
    public function customize_preview_js() { }
}

new YTrip_Dynamic_CSS();
