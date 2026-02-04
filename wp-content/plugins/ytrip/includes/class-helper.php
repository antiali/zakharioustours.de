<?php
/**
 * YTrip Helper Functions
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Helper {

    /**
     * Get Term Image URL
     * 
     * @param int $term_id Term ID.
     * @param string $taxonomy Taxonomy slug.
     * @param string $size Image size.
     * @return string Image URL.
     */
    public static function get_term_image( $term_id, $taxonomy = 'ytrip_destination', $size = 'large' ) {
        // 1. Try to get specific term image
        $term_meta = get_term_meta( $term_id, 'ytrip_tour_details', true ); // Verify meta key if using CSF taxonomy options, usually it stores in a specific key or directly if not using 'metabox' style. 
        // CSF uses `get_term_meta( $term_id, $key, true )` if not using a specific prefix for everything.
        // Wait, CSF createTaxonomyOptions stores data in a single array under the $prefix key usually? No, for taxonomy it might settle as individual meta or array.
        // Let's verify standard CSF behavior: normally it saves as an array under the $prefix key.
        // Prefix used in config is $prefix = 'ytrip_settings'; -> NO that's for options. 
        // The taxonomy options prefix should be distinct or it uses 'ytrip_tour_details' or similar?
        // Actually, let's use the same prefix for simplicity if allowed, or specific keys.
        
        // CORRECTION: checking codestar-config.php I used $prefix = 'ytrip_settings' for options.
        // For taxonomy, I will use a distinct key in the next step when I append the code.
        
        $prefix = 'ytrip_term_options'; 
        $meta = get_term_meta( $term_id, $prefix, true );

        if ( ! empty( $meta['term_image']['url'] ) ) {
            return $meta['term_image']['url'];
        }

        // 2. Fallback to Global Default
        $options = get_option( 'ytrip_settings' );
        if ( ! empty( $options['default_term_image']['url'] ) ) {
            return $options['default_term_image']['url'];
        }

        // 3. Fallback to placeholder
        return YTRIP_URL . 'assets/images/placeholder.png';
    }

    /**
     * Get Term Background URL
     */
    public static function get_term_background( $term_id ) {
        $prefix = 'ytrip_term_options';
        $meta = get_term_meta( $term_id, $prefix, true );

        if ( ! empty( $meta['term_background']['url'] ) ) {
            return $meta['term_background']['url'];
        }

        $options = get_option( 'ytrip_settings' );
        if ( ! empty( $options['default_term_background']['url'] ) ) {
            return $options['default_term_background']['url'];
        }

        return ''; // No background
    }
}
