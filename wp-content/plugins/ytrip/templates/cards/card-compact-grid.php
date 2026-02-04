<?php
/**
 * Card Style 10: Compact Grid
 * Very compact card for dense grids
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tour_id = get_the_ID();
$meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
$options = get_option( 'ytrip_settings' );
$product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
$product = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
?>

<article class="ytrip-card ytrip-card--compact-grid" data-hover="<?php echo esc_attr( $options['card_hover_effect'] ?? 'lift' ); ?>">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__image">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium' ); ?>
            <?php endif; ?>
            
            <!-- Quick Overlay on Hover -->
            <div class="ytrip-card__quick-view">
                <span><?php esc_html_e( 'View Details', 'ytrip' ); ?></span>
            </div>
        </div>
        
        <div class="ytrip-card__info">
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <div class="ytrip-card__bottom">
                <?php if ( $product ) : ?>
                <span class="ytrip-card__price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
                
                <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span class="ytrip-card__duration"><?php echo esc_html( $meta['duration'] ); ?></span>
                <?php endif; ?>
            </div>
            
            <?php if ( $product && $product->get_review_count() > 0 ) : ?>
            <div class="ytrip-card__rating">
                <span class="ytrip-stars-sm">★</span>
                <span><?php echo esc_html( $product->get_average_rating() ); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </a>
    
    <?php if ( isset( $options['card_show_wishlist'] ) && $options['card_show_wishlist'] ) : ?>
    <button class="ytrip-card__wishlist ytrip-card__wishlist--sm" data-id="<?php echo esc_attr( $tour_id ); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>
    <?php endif; ?>
</article>
