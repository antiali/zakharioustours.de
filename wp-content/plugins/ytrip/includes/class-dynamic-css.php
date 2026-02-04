<?php
/**
 * Dynamic CSS Generator & Design System
 * Handles dynamic coloring, typography, and layout settings using Color Groups.
 * 
 * @package YTrip
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Dynamic_CSS {

    private $options;

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_dynamic_styles' ), 20 );
    }

    public function enqueue_dynamic_styles() {
        $this->options = get_option( 'ytrip_settings' );
        if ( wp_style_is( 'ytrip-main', 'enqueued' ) ) {
            wp_add_inline_style( 'ytrip-main', $this->generate_css() );
        }
    }

    private function generate_css() {
        // 1. Get Color Groups with Fallbacks
        $brand = isset( $this->options['brand_colors'] ) ? $this->options['brand_colors'] : array();
        $base  = isset( $this->options['base_colors'] )  ? $this->options['base_colors']  : array();

        // Default Fallbacks
        $primary   = ! empty( $brand['primary'] )   ? $brand['primary']   : '#0f4c81';
        $secondary = ! empty( $brand['secondary'] ) ? $brand['secondary'] : '#ff6b6b';
        $accent    = ! empty( $brand['accent'] )    ? $brand['accent']    : '#f9a825';
        
        $bg      = ! empty( $base['background'] ) ? $base['background'] : '#f8fafc';
        $surface = ! empty( $base['surface'] )    ? $base['surface']    : '#ffffff';
        $text    = ! empty( $base['text'] )       ? $base['text']       : '#1e293b';
        
        $radius  = isset( $this->options['border_radius'] ) ? intval( $this->options['border_radius'] ) : 12;
        $spacing = isset( $this->options['section_spacing'] ) ? intval( $this->options['section_spacing'] ) : 80;

        // 2. Generate Variants
        $primary_rgb = $this->hex2rgb( $primary );

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
            
            /* --- Base Colors --- */
            --ytrip-background: <?php echo esc_attr( $bg ); ?>;
            --ytrip-surface: <?php echo esc_attr( $surface ); ?>;
            --ytrip-text: <?php echo esc_attr( $text ); ?>;
            
            /* --- Dimensions --- */
            --ytrip-radius: <?php echo intval( $radius ); ?>px;
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
        $hex = str_replace( '#', '', $hex );
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
}

new YTrip_Dynamic_CSS();
