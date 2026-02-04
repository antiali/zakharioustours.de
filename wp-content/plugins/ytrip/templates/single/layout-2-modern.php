<?php
/**
 * Single Tour Layout 2: Full-Width Modern
 * Hero section with floating booking and tabs navigation
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

<article class="ytrip-single ytrip-layout-modern">
    
    <!-- Hero Section -->
    <section class="ytrip-hero-tour">
        <div class="ytrip-hero-tour__bg">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'full' ); ?>
            <?php endif; ?>
        </div>
        <div class="ytrip-hero-tour__overlay"></div>
        
        <div class="ytrip-hero-tour__content">
            <div class="ytrip-container">
                <div class="ytrip-hero-tour__grid">
                    <div class="ytrip-hero-tour__info">
                        <h1 class="ytrip-hero-tour__title"><?php the_title(); ?></h1>
                        
                        <div class="ytrip-hero-tour__meta">
                            <?php if ( ! empty( $meta['duration'] ) ) : ?>
                            <span class="ytrip-meta-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                <?php echo esc_html( $meta['duration'] ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php $dest = get_the_terms( $tour_id, 'ytrip_destination' ); ?>
                            <?php if ( $dest && ! is_wp_error( $dest ) ) : ?>
                            <span class="ytrip-meta-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <?php echo esc_html( $dest[0]->name ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                            <span class="ytrip-meta-item">
                                <span class="ytrip-stars-white"><?php echo str_repeat( '★', round( $product->get_average_rating() ) ); ?></span>
                                <?php echo esc_html( $product->get_average_rating() ); ?>
                                (<?php echo esc_html( $product->get_review_count() ); ?>)
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Floating Booking Card -->
                    <div class="ytrip-hero-tour__booking">
                        <?php include YTRIP_PATH . 'templates/parts/booking-card.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sticky Tabs Navigation -->
    <nav class="ytrip-tabs-nav ytrip-tabs-nav--sticky">
        <div class="ytrip-container">
            <div class="ytrip-tabs-nav__inner">
                <button class="ytrip-tabs-nav__btn active" data-tab="overview"><?php esc_html_e( 'Overview', 'ytrip' ); ?></button>
                <button class="ytrip-tabs-nav__btn" data-tab="itinerary"><?php esc_html_e( 'Itinerary', 'ytrip' ); ?></button>
                <button class="ytrip-tabs-nav__btn" data-tab="photos"><?php esc_html_e( 'Photos', 'ytrip' ); ?></button>
                <button class="ytrip-tabs-nav__btn" data-tab="included"><?php esc_html_e( 'What\'s Included', 'ytrip' ); ?></button>
                <button class="ytrip-tabs-nav__btn" data-tab="faq"><?php esc_html_e( 'FAQ', 'ytrip' ); ?></button>
                <button class="ytrip-tabs-nav__btn" data-tab="reviews"><?php esc_html_e( 'Reviews', 'ytrip' ); ?></button>
            </div>
        </div>
    </nav>

    <!-- Tab Contents -->
    <div class="ytrip-tabs-container">
        
        <!-- Overview Tab -->
        <section class="ytrip-tab-content active" data-content="overview" id="overview">
            <div class="ytrip-container ytrip-container--narrow">
                <div class="ytrip-content-block">
                    <?php the_content(); ?>
                </div>
                
                <?php if ( ! empty( $meta['highlights'] ) ) : ?>
                <div class="ytrip-highlights-modern">
                    <h3><?php esc_html_e( 'Tour Highlights', 'ytrip' ); ?></h3>
                    <div class="ytrip-highlights-modern__grid">
                        <?php foreach ( $meta['highlights'] as $h ) : ?>
                        <div class="ytrip-highlights-modern__item">
                            <div class="ytrip-highlights-modern__icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <span><?php echo esc_html( $h['highlight'] ); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Itinerary Tab -->
        <section class="ytrip-tab-content" data-content="itinerary" id="itinerary">
            <div class="ytrip-container ytrip-container--narrow">
                <?php if ( ! empty( $meta['itinerary'] ) ) : ?>
                <div class="ytrip-itinerary-modern">
                    <?php foreach ( $meta['itinerary'] as $i => $day ) : ?>
                    <div class="ytrip-itinerary-modern__day" data-aos="fade-up">
                        <div class="ytrip-itinerary-modern__number">
                            <span><?php printf( esc_html__( 'Day %d', 'ytrip' ), $i + 1 ); ?></span>
                        </div>
                        <div class="ytrip-itinerary-modern__card">
                            <h4 class="ytrip-itinerary-modern__title"><?php echo esc_html( $day['title'] ); ?></h4>
                            <div class="ytrip-itinerary-modern__desc"><?php echo wp_kses_post( $day['description'] ); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else : ?>
                <p class="ytrip-empty"><?php esc_html_e( 'No itinerary available.', 'ytrip' ); ?></p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Photos Tab -->
        <section class="ytrip-tab-content" data-content="photos" id="photos">
            <div class="ytrip-container">
                <?php if ( ! empty( $meta['tour_gallery'] ) ) : ?>
                <div class="ytrip-gallery-grid">
                    <?php 
                    $gallery = explode( ',', $meta['tour_gallery'] );
                    foreach ( $gallery as $img_id ) :
                    ?>
                    <div class="ytrip-gallery-grid__item" data-lightbox>
                        <?php echo wp_get_attachment_image( $img_id, 'large' ); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Included Tab -->
        <section class="ytrip-tab-content" data-content="included" id="included">
            <div class="ytrip-container ytrip-container--narrow">
                <div class="ytrip-inc-exc-modern">
                    <div class="ytrip-inc-exc-modern__col ytrip-inc-exc-modern__col--yes">
                        <h3><?php esc_html_e( 'What\'s Included', 'ytrip' ); ?></h3>
                        <?php if ( ! empty( $meta['included'] ) ) : ?>
                        <ul>
                            <?php foreach ( $meta['included'] as $item ) : ?>
                            <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( $item['item'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <div class="ytrip-inc-exc-modern__col ytrip-inc-exc-modern__col--no">
                        <h3><?php esc_html_e( 'Not Included', 'ytrip' ); ?></h3>
                        <?php if ( ! empty( $meta['excluded'] ) ) : ?>
                        <ul>
                            <?php foreach ( $meta['excluded'] as $item ) : ?>
                            <li><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg><?php echo esc_html( $item['item'] ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Tab -->
        <section class="ytrip-tab-content" data-content="faq" id="faq">
            <div class="ytrip-container ytrip-container--narrow">
                <?php if ( ! empty( $meta['faq'] ) ) : ?>
                <div class="ytrip-faq-modern">
                    <?php foreach ( $meta['faq'] as $faq ) : ?>
                    <div class="ytrip-faq-modern__item">
                        <button class="ytrip-faq-modern__question">
                            <span><?php echo esc_html( $faq['question'] ); ?></span>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <div class="ytrip-faq-modern__answer"><?php echo wp_kses_post( $faq['answer'] ); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Reviews Tab -->
        <section class="ytrip-tab-content" data-content="reviews" id="reviews">
            <div class="ytrip-container ytrip-container--narrow">
                <?php if ( $product ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            </div>
        </section>
        
    </div>

    <!-- Similar Tours Carousel -->
    <section class="ytrip-related-section">
        <div class="ytrip-container">
            <h2 class="ytrip-section-title"><?php esc_html_e( 'Similar Tours', 'ytrip' ); ?></h2>
            <?php include YTRIP_PATH . 'templates/parts/related-tours.php'; ?>
        </div>
    </section>
    
</article>
