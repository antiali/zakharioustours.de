<?php
/**
 * Card Style 1: Overlay Gradient
 * Full image background with gradient overlay
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tour_id = get_the_ID();
$meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
$options = get_option( 'ytrip_settings' );
$product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
$product = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
$dest = get_the_terms( $tour_id, 'ytrip_destination' );
?>

<article class="ytrip-card ytrip-card--overlay-gradient" data-hover="<?php echo esc_attr( $options['card_hover_effect'] ?? 'lift' ); ?>">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__image">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large' ); ?>
            <?php endif; ?>
            <div class="ytrip-card__gradient"></div>
        </div>
        
        <div class="ytrip-card__content">
            <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
            <span class="ytrip-card__location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3" fill="none"/></svg>
                <?php echo esc_html( $dest[0]->name ); ?>
            </span>
            <?php endif; ?>
            
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <div class="ytrip-card__meta">
                <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span class="ytrip-card__duration"><?php echo esc_html( $meta['duration'] ); ?></span>
                <?php endif; ?>
                
                <?php if ( $product ) : ?>
                <span class="ytrip-card__price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ( isset( $options['card_show_wishlist'] ) && $options['card_show_wishlist'] ) : ?>
        <button class="ytrip-card__wishlist" data-id="<?php echo esc_attr( $tour_id ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'ytrip' ); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
        <?php endif; ?>
    </a>
</article>
