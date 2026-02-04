<?php
/**
 * Single Tour Layout 3: Split Screen
 * 50/50 split with fixed media and scrollable content
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

<article class="ytrip-single ytrip-layout-split">
    
    <!-- Left Fixed Side - Media -->
    <div class="ytrip-split-left">
        <div class="ytrip-split-media">
            <!-- Main Image/Slider -->
            <div class="ytrip-split-media__main">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'full' ); ?>
                <?php endif; ?>
            </div>
            
            <!-- Thumbnail Strip -->
            <?php if ( ! empty( $meta['tour_gallery'] ) ) : ?>
            <div class="ytrip-split-media__thumbs">
                <?php 
                $gallery = explode( ',', $meta['tour_gallery'] );
                foreach ( array_slice( $gallery, 0, 4 ) as $img_id ) :
                ?>
                <div class="ytrip-split-media__thumb">
                    <?php echo wp_get_attachment_image( $img_id, 'thumbnail' ); ?>
                </div>
                <?php endforeach; ?>
                <?php if ( count( $gallery ) > 4 ) : ?>
                <div class="ytrip-split-media__more">
                    +<?php echo count( $gallery ) - 4; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Video Button (if video exists) -->
            <?php if ( ! empty( $meta['tour_video'] ) ) : ?>
            <button class="ytrip-split-media__video-btn" data-video="<?php echo esc_url( $meta['tour_video'] ); ?>">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                <span><?php esc_html_e( 'Watch Video', 'ytrip' ); ?></span>
            </button>
            <?php endif; ?>
            
            <!-- Map Toggle -->
            <button class="ytrip-split-media__map-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span><?php esc_html_e( 'View Map', 'ytrip' ); ?></span>
            </button>
        </div>
    </div>
    
    <!-- Right Scrollable Side - Content -->
    <div class="ytrip-split-right">
        <div class="ytrip-split-content">
            
            <!-- Header -->
            <header class="ytrip-split-header">
                <div class="ytrip-split-header__badges">
                    <?php if ( ! empty( $meta['featured'] ) ) : ?>
                    <span class="ytrip-badge ytrip-badge--featured"><?php esc_html_e( 'Featured', 'ytrip' ); ?></span>
                    <?php endif; ?>
                    
                    <?php $dest = get_the_terms( $tour_id, 'ytrip_destination' ); ?>
                    <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
                    <span class="ytrip-badge ytrip-badge--location"><?php echo esc_html( $dest[0]->name ); ?></span>
                    <?php endif; ?>
                </div>
                
                <h1 class="ytrip-split-header__title"><?php the_title(); ?></h1>
                
                <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                <div class="ytrip-split-header__rating">
                    <div class="ytrip-stars"><?php echo str_repeat( '★', round( $product->get_average_rating() ) ); ?><?php echo str_repeat( '☆', 5 - round( $product->get_average_rating() ) ); ?></div>
                    <span><?php echo esc_html( $product->get_average_rating() ); ?> (<?php echo esc_html( $product->get_review_count() ); ?> <?php esc_html_e( 'reviews', 'ytrip' ); ?>)</span>
                </div>
                <?php endif; ?>
            </header>

            <!-- Price Block -->
            <div class="ytrip-split-price">
                <?php if ( $product ) : ?>
                <div class="ytrip-split-price__main">
                    <span class="ytrip-split-price__label"><?php esc_html_e( 'From', 'ytrip' ); ?></span>
                    <span class="ytrip-split-price__value"><?php echo $product->get_price_html(); ?></span>
                    <span class="ytrip-split-price__unit"><?php esc_html_e( '/ person', 'ytrip' ); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="ytrip-split-price__meta">
                    <?php if ( ! empty( $meta['duration'] ) ) : ?>
                    <div class="ytrip-split-price__item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        <span><?php echo esc_html( $meta['duration'] ); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( ! empty( $meta['group_size'] ) ) : ?>
                    <div class="ytrip-split-price__item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span><?php echo esc_html( $meta['group_size'] ); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div class="ytrip-split-section">
                <h2><?php esc_html_e( 'About This Tour', 'ytrip' ); ?></h2>
                <div class="ytrip-split-section__content">
                    <?php the_content(); ?>
                </div>
            </div>

            <!-- Highlights -->
            <?php if ( ! empty( $meta['highlights'] ) ) : ?>
            <div class="ytrip-split-section">
                <h2><?php esc_html_e( 'Highlights', 'ytrip' ); ?></h2>
                <ul class="ytrip-split-highlights">
                    <?php foreach ( $meta['highlights'] as $h ) : ?>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="ytrip-text-primary"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <?php echo esc_html( $h['highlight'] ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Itinerary -->
            <?php if ( ! empty( $meta['itinerary'] ) ) : ?>
            <div class="ytrip-split-section">
                <h2><?php esc_html_e( 'Day by Day', 'ytrip' ); ?></h2>
                <div class="ytrip-split-itinerary">
                    <?php foreach ( $meta['itinerary'] as $i => $day ) : ?>
                    <details class="ytrip-split-itinerary__day">
                        <summary>
                            <span class="ytrip-split-itinerary__num"><?php printf( esc_html__( 'Day %d', 'ytrip' ), $i + 1 ); ?></span>
                            <span class="ytrip-split-itinerary__title"><?php echo esc_html( $day['title'] ); ?></span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <div class="ytrip-split-itinerary__content">
                            <?php echo wp_kses_post( $day['description'] ); ?>
                        </div>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Included/Excluded -->
            <div class="ytrip-split-section">
                <div class="ytrip-split-inc-exc">
                    <div>
                        <h3><?php esc_html_e( 'Included', 'ytrip' ); ?></h3>
                        <?php if ( ! empty( $meta['included'] ) ) : ?>
                        <ul class="ytrip-split-inc-exc__list ytrip-split-inc-exc__list--yes">
                            <?php foreach ( $meta['included'] as $item ) : ?>
                            <li><?php echo esc_html( $item['item'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3><?php esc_html_e( 'Excluded', 'ytrip' ); ?></h3>
                        <?php if ( ! empty( $meta['excluded'] ) ) : ?>
                        <ul class="ytrip-split-inc-exc__list ytrip-split-inc-exc__list--no">
                            <?php foreach ( $meta['excluded'] as $item ) : ?>
                            <li><?php echo esc_html( $item['item'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="ytrip-split-section ytrip-split-booking">
                <h2><?php esc_html_e( 'Book This Tour', 'ytrip' ); ?></h2>
                <?php include YTRIP_PATH . 'templates/parts/booking-form.php'; ?>
            </div>

            <!-- Reviews -->
            <?php if ( $product ) : ?>
            <div class="ytrip-split-section">
                <h2><?php esc_html_e( 'Guest Reviews', 'ytrip' ); ?></h2>
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</article>
