<?php
/**
 * Card Style 5: Glassmorphism
 * Ultra-modern frosted glass effect
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

<article class="ytrip-card ytrip-card--glassmorphism" data-hover="<?php echo esc_attr( $options['card_hover_effect'] ?? 'lift' ); ?>">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__bg">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'large' ); ?>
            <?php endif; ?>
        </div>
        
        <div class="ytrip-card__glass">
            <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
            <span class="ytrip-card__location"><?php echo esc_html( $dest[0]->name ); ?></span>
            <?php endif; ?>
            
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <div class="ytrip-card__details">
                <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <?php echo esc_html( $meta['duration'] ); ?>
                </span>
                <?php endif; ?>
                
                <?php if ( ! empty( $meta['group_size'] ) ) : ?>
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <?php echo esc_html( $meta['group_size'] ); ?>
                </span>
                <?php endif; ?>
            </div>
            
            <div class="ytrip-card__bottom">
                <?php if ( $product ) : ?>
                <span class="ytrip-card__price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
                
                <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                <span class="ytrip-card__rating">★ <?php echo esc_html( $product->get_average_rating() ); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    
    <?php if ( isset( $options['card_show_wishlist'] ) && $options['card_show_wishlist'] ) : ?>
    <button class="ytrip-card__wishlist" data-id="<?php echo esc_attr( $tour_id ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'ytrip' ); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>
    <?php endif; ?>
</article>
