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
        
        // Subject & Body - translation ready
        $subject = sprintf( __( 'New Inquiry: %s', 'ytrip' ), get_the_title( $tour_id ) );
        $body    = sprintf(
            /* translators: 1: name, 2: email, 3: tour title, 4: message */
            __( 'Name: %1$s\nEmail: %2$s\nTour: %3$s\n\nMessage:', 'ytrip' ),
            $name,
            $email,
            get_the_title( $tour_id )
        ) . "\n$message";

        $headers = array(
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Reply-To' => $name . ' <' . $email . '>',
        );
            wp_send_json_success( array( 'message' => __( 'Your inquiry has been sent successfully.', 'ytrip' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to send email. Please try again later.', 'ytrip' ) );
        }
    }

    public function toggle_wishlist() {
        // Placeholder for wishlist logic if needed
        wp_send_json_success();
    }
}
new YTrip_AJAX();
