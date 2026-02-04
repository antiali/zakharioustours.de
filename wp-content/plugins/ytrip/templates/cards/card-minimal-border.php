<?php
/**
 * Card Style 4: Minimal Border
 * Clean minimalist design with thin border
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

<article class="ytrip-card ytrip-card--minimal-border" data-hover="<?php echo esc_attr( $options['card_hover_effect'] ?? 'lift' ); ?>">
    <a href="<?php the_permalink(); ?>" class="ytrip-card__link">
        <div class="ytrip-card__image">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'medium_large' ); ?>
            <?php endif; ?>
        </div>
        
        <div class="ytrip-card__body">
            <h3 class="ytrip-card__title"><?php the_title(); ?></h3>
            
            <?php if ( has_excerpt() ) : ?>
            <p class="ytrip-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
            <?php endif; ?>
            
            <div class="ytrip-card__footer">
                <?php if ( $product ) : ?>
                <span class="ytrip-card__price"><?php echo $product->get_price_html(); ?></span>
                <?php endif; ?>
                
                <span class="ytrip-card__arrow">→</span>
            </div>
        </div>
    </a>
</article>
