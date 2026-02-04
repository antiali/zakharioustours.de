<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class YTrip_AJAX {
    
    public function __construct() {
        add_action( 'wp_ajax_ytrip_submit_inquiry', array( $this, 'submit_inquiry' ) );
        add_action( 'wp_ajax_nopriv_ytrip_submit_inquiry', array( $this, 'submit_inquiry' ) );
        add_action( 'wp_ajax_ytrip_toggle_wishlist', array( $this, 'toggle_wishlist' ) );
    }

    public function submit_inquiry() {
        check_ajax_referer( 'ytrip_inquiry_nonce', 'nonce' );

        $tour_id = isset( $_POST['tour_id'] ) ? absint( $_POST['tour_id'] ) : 0;
        $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

        if ( ! $tour_id || ! $email || ! $name ) {
            wp_send_json_error( array( 'message' => __( 'Please fill in all required fields.', 'ytrip' ) ) );
        }

        // Get Recipient
        $meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
        $to   = isset( $meta['inquiry_email'] ) && ! empty( $meta['inquiry_email'] ) ? $meta['inquiry_email'] : get_option( 'admin_email' );
        
        // Subject & Body
        $subject = sprintf( __( 'New Inquiry: %s', 'ytrip' ), get_the_title( $tour_id ) );
        $body    = "Name: $name\n";
        $body   .= "Email: $email\n";
        $body   .= "Tour: " . get_the_title( $tour_id ) . " (" . get_permalink( $tour_id ) . ")\n\n";
        $body   .= "Message:\n$message\n";

        $headers = array( 'Content-Type: text/plain; charset=UTF-8' );
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';

        if ( wp_mail( $to, $subject, $body, $headers ) ) {
            wp_send_json_success( array( 'message' => __( 'Your inquiry has been sent successfully.', 'ytrip' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to send email. Please try again later.', 'ytrip' ) ) );
        }
    }

    public function toggle_wishlist() {
        // Placeholder for wishlist logic if needed
        wp_send_json_success();
    }
}
new YTrip_AJAX();
