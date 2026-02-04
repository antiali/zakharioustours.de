<?php
/**
 * Tour Card Part
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tour_id = get_the_ID();
$meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
$product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
$product = $product_id ? wc_get_product( $product_id ) : null;
$terms = get_the_terms( $tour_id, 'ytrip_destination' );
$destination = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : '';
?>

<div class="ytrip-tour-card">
    <div class="ytrip-tour-card__image">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium_large' ); ?>
            </a>
        <?php endif; ?>
        
        <?php if ( ! empty( $meta['featured'] ) ) : ?>
            <span class="ytrip-tour-card__badge"><?php esc_html_e( 'Featured', 'ytrip' ); ?></span>
        <?php endif; ?>
        
        <button class="ytrip-tour-card__wishlist" data-tour-id="<?php echo esc_attr( $tour_id ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'ytrip' ); ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
    </div>
    
    <div class="ytrip-tour-card__content">
        <?php if ( $destination ) : ?>
            <div class="ytrip-tour-card__location">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html( $destination ); ?>
            </div>
        <?php endif; ?>
        
        <h3 class="ytrip-tour-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <div class="ytrip-tour-card__meta">
            <?php if ( ! empty( $meta['duration'] ) ) : ?>
                <span class="ytrip-tour-card__meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    <?php echo esc_html( $meta['duration'] ); ?>
                </span>
            <?php endif; ?>
            
            <?php if ( ! empty( $meta['group_size'] ) ) : ?>
                <span class="ytrip-tour-card__meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?php echo esc_html( $meta['group_size'] ); ?>
                </span>
            <?php endif; ?>
        </div>
        
        <?php if ( $product && $product->get_review_count() > 0 ) : ?>
            <div class="ytrip-tour-card__rating">
                <div class="ytrip-tour-card__stars">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="<?php echo $i <= round( $product->get_average_rating() ) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <?php endfor; ?>
                </div>
                <span class="ytrip-tour-card__rating-text">(<?php echo esc_html( $product->get_review_count() ); ?>)</span>
            </div>
        <?php endif; ?>
        
        <div class="ytrip-tour-card__footer">
            <div class="ytrip-tour-card__price">
                <span><?php esc_html_e( 'From', 'ytrip' ); ?></span>
                <span class="ytrip-tour-card__price-value">
                    <?php echo $product ? $product->get_price_html() : ''; ?>
                </span>
            </div>
            <a href="<?php the_permalink(); ?>" class="ytrip-btn ytrip-btn-sm ytrip-btn-primary">
                <?php esc_html_e( 'View Details', 'ytrip' ); ?>
            </a>
        </div>
    </div>
</div>
