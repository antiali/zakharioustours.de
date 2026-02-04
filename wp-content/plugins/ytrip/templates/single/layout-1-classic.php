<?php
/**
 * Single Tour Layout 1: Classic Sidebar
 * Traditional sidebar layout with gallery at top
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$tour_id = get_the_ID();
$meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
$options = get_option( 'ytrip_settings' );
$product_id = get_post_meta( $tour_id, '_ytrip_wc_product_id', true );
$product = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
$sidebar_pos = isset( $options['single_sidebar_position'] ) ? $options['single_sidebar_position'] : 'right';
$sticky = isset( $options['single_sticky_booking'] ) ? $options['single_sticky_booking'] : true;
?>

<article class="ytrip-single ytrip-layout-classic">
    
    <!-- Gallery Section -->
    <section class="ytrip-gallery-section">
        <div class="ytrip-container">
            <div class="ytrip-gallery ytrip-gallery--slider">
                <div class="ytrip-gallery__main">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'full', array( 'class' => 'ytrip-gallery__image' ) ); ?>
                    <?php endif; ?>
                </div>
                
                <?php if ( ! empty( $meta['tour_gallery'] ) ) : ?>
                <div class="ytrip-gallery__thumbs">
                    <?php 
                    $gallery = explode( ',', $meta['tour_gallery'] );
                    foreach ( $gallery as $img_id ) :
                    ?>
                        <div class="ytrip-gallery__thumb" data-id="<?php echo esc_attr( $img_id ); ?>">
                            <?php echo wp_get_attachment_image( $img_id, 'thumbnail' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="ytrip-content-section">
        <div class="ytrip-container">
            <div class="ytrip-layout-grid ytrip-layout-grid--<?php echo esc_attr( $sidebar_pos ); ?>">
                
                <!-- Content Column -->
                <div class="ytrip-content-main">
                    
                    <!-- Header -->
                    <header class="ytrip-tour-header">
                        <?php if ( isset( $options['single_show_breadcrumb'] ) && $options['single_show_breadcrumb'] ) : ?>
                        <nav class="ytrip-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'ytrip' ); ?>">
                            <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'ytrip' ); ?></a>
                            <span class="ytrip-breadcrumb__sep">/</span>
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'ytrip_tour' ) ); ?>"><?php esc_html_e( 'Tours', 'ytrip' ); ?></a>
                            <span class="ytrip-breadcrumb__sep">/</span>
                            <span class="ytrip-breadcrumb__current"><?php the_title(); ?></span>
                        </nav>
                        <?php endif; ?>
                        
                        <h1 class="ytrip-tour-title"><?php the_title(); ?></h1>
                        
                        <div class="ytrip-tour-meta">
                            <?php if ( ! empty( $meta['duration'] ) ) : ?>
                            <span class="ytrip-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                <?php echo esc_html( $meta['duration'] ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $meta['group_size'] ) ) : ?>
                            <span class="ytrip-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <?php echo esc_html( $meta['group_size'] ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                            <span class="ytrip-meta-item ytrip-meta-rating">
                                <span class="ytrip-stars"><?php echo str_repeat( '★', round( $product->get_average_rating() ) ); ?></span>
                                <?php echo esc_html( $product->get_average_rating() ); ?>
                                (<?php echo esc_html( $product->get_review_count() ); ?> <?php esc_html_e( 'reviews', 'ytrip' ); ?>)
                            </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <!-- Description -->
                    <div class="ytrip-section ytrip-description">
                        <h2 class="ytrip-section__title"><?php esc_html_e( 'Overview', 'ytrip' ); ?></h2>
                        <div class="ytrip-section__content">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Highlights -->
                    <?php if ( ! empty( $meta['highlights'] ) ) : ?>
                    <div class="ytrip-section ytrip-highlights">
                        <h2 class="ytrip-section__title"><?php esc_html_e( 'Highlights', 'ytrip' ); ?></h2>
                        <ul class="ytrip-highlights__list">
                            <?php foreach ( $meta['highlights'] as $h ) : ?>
                            <li class="ytrip-highlights__item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <?php echo esc_html( $h['highlight'] ); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Itinerary -->
                    <?php if ( ! empty( $meta['itinerary'] ) ) : ?>
                    <div class="ytrip-section ytrip-itinerary">
                        <h2 class="ytrip-section__title"><?php esc_html_e( 'Itinerary', 'ytrip' ); ?></h2>
                        <div class="ytrip-itinerary__timeline">
                            <?php foreach ( $meta['itinerary'] as $i => $day ) : ?>
                            <div class="ytrip-itinerary__day">
                                <div class="ytrip-itinerary__marker"><?php echo esc_html( $i + 1 ); ?></div>
                                <div class="ytrip-itinerary__content">
                                    <h4 class="ytrip-itinerary__title"><?php echo esc_html( $day['title'] ); ?></h4>
                                    <div class="ytrip-itinerary__desc"><?php echo wp_kses_post( $day['description'] ); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Included/Excluded -->
                    <div class="ytrip-section ytrip-inc-exc">
                        <div class="ytrip-inc-exc__grid">
                            <div class="ytrip-inc-exc__col">
                                <h3 class="ytrip-inc-exc__title ytrip-inc-exc__title--included"><?php esc_html_e( 'Included', 'ytrip' ); ?></h3>
                                <?php if ( ! empty( $meta['included'] ) ) : ?>
                                <ul class="ytrip-inc-exc__list">
                                    <?php foreach ( $meta['included'] as $item ) : ?>
                                    <li class="ytrip-inc-exc__item ytrip-inc-exc__item--yes">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                        <?php echo esc_html( $item['item'] ); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                            <div class="ytrip-inc-exc__col">
                                <h3 class="ytrip-inc-exc__title ytrip-inc-exc__title--excluded"><?php esc_html_e( 'Excluded', 'ytrip' ); ?></h3>
                                <?php if ( ! empty( $meta['excluded'] ) ) : ?>
                                <ul class="ytrip-inc-exc__list">
                                    <?php foreach ( $meta['excluded'] as $item ) : ?>
                                    <li class="ytrip-inc-exc__item ytrip-inc-exc__item--no">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                        <?php echo esc_html( $item['item'] ); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ -->
                    <?php if ( ! empty( $meta['faq'] ) ) : ?>
                    <div class="ytrip-section ytrip-faq">
                        <h2 class="ytrip-section__title"><?php esc_html_e( 'Frequently Asked Questions', 'ytrip' ); ?></h2>
                        <div class="ytrip-faq__list">
                            <?php foreach ( $meta['faq'] as $faq ) : ?>
                            <div class="ytrip-faq__item">
                                <button class="ytrip-faq__question">
                                    <?php echo esc_html( $faq['question'] ); ?>
                                    <svg class="ytrip-faq__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <div class="ytrip-faq__answer"><?php echo wp_kses_post( $faq['answer'] ); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Reviews -->
                    <?php if ( $product ) : ?>
                    <div class="ytrip-section ytrip-reviews">
                        <h2 class="ytrip-section__title"><?php esc_html_e( 'Reviews', 'ytrip' ); ?></h2>
                        <?php comments_template(); ?>
                    </div>
                    <?php endif; ?>
                    
                </div>

                <!-- Sidebar -->
                <aside class="ytrip-sidebar <?php echo $sticky ? 'ytrip-sidebar--sticky' : ''; ?>">
                    <?php include YTRIP_PATH . 'templates/parts/booking-card.php'; ?>
                </aside>
                
            </div>
        </div>
    </section>
    
</article>
