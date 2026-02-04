<?php
/**
 * Card Style: List View
 * Horizontal card for list display mode
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
$cat = get_the_terms( $tour_id, 'ytrip_category' );
?>

<article class="ytrip-card ytrip-card--list-view">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__image">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large' ); ?>
            <?php endif; ?>
            
            <?php if ( ! empty( $meta['featured'] ) ) : ?>
            <span class="ytrip-card__badge"><?php esc_html_e( 'Featured', 'ytrip' ); ?></span>
            <?php endif; ?>
            
            <?php if ( $product && $product->is_on_sale() ) : ?>
            <span class="ytrip-card__badge ytrip-card__badge--sale"><?php esc_html_e( 'Sale', 'ytrip' ); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="ytrip-card__content">
            <div class="ytrip-card__header">
                <?php if ( $cat && ! is_wp_error( $cat ) ) : ?>
                <span class="ytrip-card__category"><?php echo esc_html( $cat[0]->name ); ?></span>
                <?php endif; ?>
                
                <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                <div class="ytrip-card__rating">
                    <span class="ytrip-stars"><?php echo str_repeat( '★', round( $product->get_average_rating() ) ); ?></span>
                    <span><?php echo esc_html( $product->get_average_rating() ); ?> (<?php echo esc_html( $product->get_review_count() ); ?>)</span>
                </div>
                <?php endif; ?>
            </div>
            
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
            <p class="ytrip-card__location">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html( $dest[0]->name ); ?>
            </p>
            <?php endif; ?>
            
            <?php if ( has_excerpt() ) : ?>
            <p class="ytrip-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 30 ); ?></p>
            <?php endif; ?>
            
            <div class="ytrip-card__meta">
                <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span class="ytrip-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <?php echo esc_html( $meta['duration'] ); ?>
                </span>
                <?php endif; ?>
                
                <?php if ( ! empty( $meta['group_size'] ) ) : ?>
                <span class="ytrip-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <?php echo esc_html( $meta['group_size'] ); ?>
                </span>
                <?php endif; ?>
                
                <?php if ( ! empty( $meta['languages'] ) ) : ?>
                <span class="ytrip-card__meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <?php echo esc_html( $meta['languages'] ); ?>
                </span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="ytrip-card__action">
            <?php if ( $product ) : ?>
            <div class="ytrip-card__price">
                <span class="ytrip-card__from"><?php esc_html_e( 'From', 'ytrip' ); ?></span>
                <span class="ytrip-card__amount"><?php echo $product->get_price_html(); ?></span>
                <span class="ytrip-card__per"><?php esc_html_e( '/ person', 'ytrip' ); ?></span>
            </div>
            <?php endif; ?>
            
            <span class="ytrip-btn ytrip-btn-primary"><?php esc_html_e( 'View Details', 'ytrip' ); ?></span>
        </div>
    </a>
    
    <?php if ( isset( $options['card_show_wishlist'] ) && $options['card_show_wishlist'] ) : ?>
    <button class="ytrip-card__wishlist" data-id="<?php echo esc_attr( $tour_id ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'ytrip' ); ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>
    <?php endif; ?>
</article>
