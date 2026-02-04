<?php
/**
 * Card Style 7: Split Content
 * Horizontal layout with 50/50 split
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

<article class="ytrip-card ytrip-card--split-content" data-hover="<?php echo esc_attr( $options['card_hover_effect'] ?? 'lift' ); ?>">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__image-side">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large' ); ?>
            <?php endif; ?>
            
            <?php if ( ! empty( $meta['featured'] ) ) : ?>
            <span class="ytrip-card__badge"><?php esc_html_e( 'Featured', 'ytrip' ); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="ytrip-card__content-side">
            <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
            <span class="ytrip-card__location">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html( $dest[0]->name ); ?>
            </span>
            <?php endif; ?>
            
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <?php if ( has_excerpt() ) : ?>
            <p class="ytrip-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
            <?php endif; ?>
            
            <div class="ytrip-card__meta">
                <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span class="ytrip-card__duration"><?php echo esc_html( $meta['duration'] ); ?></span>
                <?php endif; ?>
                
                <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                <span class="ytrip-card__rating">★ <?php echo esc_html( $product->get_average_rating() ); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="ytrip-card__footer">
                <?php if ( $product ) : ?>
                <span class="ytrip-card__price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
                
                <span class="ytrip-card__btn"><?php esc_html_e( 'View Tour', 'ytrip' ); ?></span>
            </div>
        </div>
    </a>
</article>
