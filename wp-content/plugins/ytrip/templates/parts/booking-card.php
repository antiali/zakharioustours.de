<?php
/**
 * Booking Card Part
 * Reusable booking widget for single tour pages
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tour_id = get_the_ID();
$product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
$product = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
?>

<div class="ytrip-booking-card">
    <?php if ( $product ) : ?>
    <div class="ytrip-booking-card__price">
        <span class="ytrip-booking-card__from"><?php esc_html_e( 'From', 'ytrip' ); ?></span>
        <span class="ytrip-booking-card__amount"><?php echo $product->get_price_html(); ?></span>
        <span class="ytrip-booking-card__per"><?php esc_html_e( '/ person', 'ytrip' ); ?></span>
    </div>
    <?php endif; ?>

    <form class="ytrip-booking-card__form" method="post" action="<?php echo esc_url( function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '' ); ?>">
        
        <div class="ytrip-booking-card__field">
            <label><?php esc_html_e( 'Select Date', 'ytrip' ); ?></label>
            <input type="date" name="tour_date" required min="<?php echo date( 'Y-m-d' ); ?>">
        </div>
        
        <div class="ytrip-booking-card__row">
            <div class="ytrip-booking-card__field">
                <label><?php esc_html_e( 'Adults', 'ytrip' ); ?></label>
                <select name="adults">
                    <?php for ( $i = 1; $i <= 10; $i++ ) : ?>
                    <option value="<?php echo $i; ?>" <?php selected( $i, 2 ); ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="ytrip-booking-card__field">
                <label><?php esc_html_e( 'Children', 'ytrip' ); ?></label>
                <select name="children">
                    <?php for ( $i = 0; $i <= 5; $i++ ) : ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        
        <?php if ( $product ) : ?>
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>">
        <?php endif; ?>
        <input type="hidden" name="tour_id" value="<?php echo esc_attr( $tour_id ); ?>">
        <?php wp_nonce_field( 'ytrip_booking', 'ytrip_booking_nonce' ); ?>
        
        <button type="submit" class="ytrip-btn ytrip-btn-primary ytrip-btn-block">
            <?php esc_html_e( 'Book Now', 'ytrip' ); ?>
        </button>
        
    </form>

    <div class="ytrip-booking-card__features">
        <div class="ytrip-booking-card__feature">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            <?php esc_html_e( 'Free Cancellation', 'ytrip' ); ?>
        </div>
        <div class="ytrip-booking-card__feature">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            <?php esc_html_e( 'Instant Confirmation', 'ytrip' ); ?>
        </div>
        <div class="ytrip-booking-card__feature">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            <?php esc_html_e( 'Secure Payment', 'ytrip' ); ?>
        </div>
    </div>
    
    <div class="ytrip-booking-card__contact">
        <p><?php esc_html_e( 'Need help booking?', 'ytrip' ); ?></p>
        <a href="tel:+1234567890" class="ytrip-booking-card__phone">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <?php esc_html_e( 'Call Us', 'ytrip' ); ?>
        </a>
    </div>
</div>
