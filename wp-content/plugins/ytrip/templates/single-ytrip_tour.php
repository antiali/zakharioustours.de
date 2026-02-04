<?php
/**
 * Single Tour Template
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$tour_id = get_the_ID();
$meta = get_post_meta( $tour_id, 'ytrip_tour_details', true );
$product_id = get_post_meta( $tour_id, '_ytrip_linked_product_id', true );
$product = $product_id ? wc_get_product( $product_id ) : null;
?>

<div class="ytrip-wrapper">
    <article class="ytrip-single-tour">
        <div class="ytrip-container">
            
            <!-- Gallery -->
            <div class="ytrip-tour-gallery">
                <div class="ytrip-tour-gallery__main">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'full' ); ?>
                    <?php endif; ?>
                </div>
                <?php if ( ! empty( $meta['tour_gallery'] ) ) : ?>
                <div class="ytrip-tour-gallery__thumbs">
                    <?php 
                    $gallery = explode( ',', $meta['tour_gallery'] );
                    $count = 0;
                    foreach ( $gallery as $img_id ) :
                        if ( $count >= 3 ) break;
                        $count++;
                    ?>
                        <div class="ytrip-tour-gallery__thumb">
                            <?php echo wp_get_attachment_image( $img_id, 'medium' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="ytrip-tour-layout">
                <!-- Content -->
                <div class="ytrip-tour-content">
                    <header class="ytrip-tour-header">
                        <nav class="ytrip-tour-header__breadcrumb">
                            <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', 'ytrip' ); ?></a>
                            <span>/</span>
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'ytrip_tour' ) ); ?>"><?php esc_html_e( 'Tours', 'ytrip' ); ?></a>
                            <span>/</span>
                            <span><?php the_title(); ?></span>
                        </nav>
                        
                        <h1 class="ytrip-tour-header__title"><?php the_title(); ?></h1>
                        
                        <div class="ytrip-tour-header__meta">
                            <?php if ( ! empty( $meta['duration'] ) ) : ?>
                            <span class="ytrip-tour-header__meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                <?php echo esc_html( $meta['duration'] ); ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ( $product && $product->get_review_count() > 0 ) : ?>
                            <span class="ytrip-tour-header__meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="ytrip-text-primary"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <?php echo esc_html( $product->get_average_rating() ); ?> (<?php echo esc_html( $product->get_review_count() ); ?> <?php esc_html_e( 'reviews', 'ytrip' ); ?>)
                            </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <!-- Tabs -->
                    <div class="ytrip-tour-tabs">
                        <nav class="ytrip-tour-tabs__nav">
                            <button class="ytrip-tour-tabs__btn active" data-tab="overview"><?php esc_html_e( 'Overview', 'ytrip' ); ?></button>
                            <button class="ytrip-tour-tabs__btn" data-tab="itinerary"><?php esc_html_e( 'Itinerary', 'ytrip' ); ?></button>
                            <button class="ytrip-tour-tabs__btn" data-tab="included"><?php esc_html_e( 'Included/Excluded', 'ytrip' ); ?></button>
                            <button class="ytrip-tour-tabs__btn" data-tab="faq"><?php esc_html_e( 'FAQ', 'ytrip' ); ?></button>
                        </nav>

                        <!-- Overview -->
                        <div class="ytrip-tour-tabs__content active" data-content="overview">
                            <?php the_content(); ?>
                            
                            <?php if ( ! empty( $meta['highlights'] ) ) : ?>
                            <h3><?php esc_html_e( 'Highlights', 'ytrip' ); ?></h3>
                            <ul class="ytrip-highlights">
                                <?php foreach ( $meta['highlights'] as $h ) : ?>
                                    <li><?php echo esc_html( $h['highlight'] ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>

                        <!-- Itinerary -->
                        <div class="ytrip-tour-tabs__content" data-content="itinerary">
                            <?php if ( ! empty( $meta['itinerary'] ) ) : ?>
                            <div class="ytrip-itinerary">
                                <?php foreach ( $meta['itinerary'] as $i => $day ) : ?>
                                <div class="ytrip-itinerary__day">
                                    <div class="ytrip-itinerary__marker"><?php echo esc_html( $i + 1 ); ?></div>
                                    <div class="ytrip-itinerary__content">
                                        <h4 class="ytrip-itinerary__title"><?php echo esc_html( $day['title'] ); ?></h4>
                                        <p class="ytrip-itinerary__desc"><?php echo wp_kses_post( $day['description'] ); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else : ?>
                            <p><?php esc_html_e( 'No itinerary available.', 'ytrip' ); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Included/Excluded -->
                        <div class="ytrip-tour-tabs__content" data-content="included">
                            <div class="ytrip-inc-exc">
                                <div>
                                    <h4><?php esc_html_e( 'Included', 'ytrip' ); ?></h4>
                                    <?php if ( ! empty( $meta['included'] ) ) : ?>
                                    <ul class="ytrip-inc-exc__list">
                                        <?php foreach ( $meta['included'] as $item ) : ?>
                                        <li class="ytrip-inc-exc__item">
                                            <svg class="ytrip-inc-exc__icon ytrip-inc-exc__icon--check" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                            <?php echo esc_html( $item['item'] ); ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4><?php esc_html_e( 'Excluded', 'ytrip' ); ?></h4>
                                    <?php if ( ! empty( $meta['excluded'] ) ) : ?>
                                    <ul class="ytrip-inc-exc__list">
                                        <?php foreach ( $meta['excluded'] as $item ) : ?>
                                        <li class="ytrip-inc-exc__item">
                                            <svg class="ytrip-inc-exc__icon ytrip-inc-exc__icon--cross" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            <?php echo esc_html( $item['item'] ); ?>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ -->
                        <div class="ytrip-tour-tabs__content" data-content="faq">
                            <?php if ( ! empty( $meta['faq'] ) ) : ?>
                            <div class="ytrip-faq">
                                <?php foreach ( $meta['faq'] as $faq ) : ?>
                                <div class="ytrip-faq__item">
                                    <button class="ytrip-faq__question">
                                        <?php echo esc_html( $faq['question'] ); ?>
                                        <svg class="ytrip-faq__icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                    </button>
                                    <div class="ytrip-faq__answer">
                                        <?php echo wp_kses_post( $faq['answer'] ); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else : ?>
                            <p><?php esc_html_e( 'No FAQs available.', 'ytrip' ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Booking Sidebar -->
                <aside class="ytrip-booking-sidebar">
                    <div class="ytrip-booking-card">
                        <?php 
                        $booking_method = isset( $meta['booking_method'] ) ? $meta['booking_method'] : 'woocommerce';
                        
                        if ( 'inquiry' === $booking_method ) : 
                        ?>
                            <!-- Inquiry Form -->
                            <div class="ytrip-booking-card__price">
                                <span class="ytrip-booking-card__price-value"><?php esc_html_e( 'Request a Quote', 'ytrip' ); ?></span>
                            </div>
                            
                            <form id="ytrip-inquiry-form" class="ytrip-booking-form">
                                <input type="hidden" name="tour_id" value="<?php echo esc_attr( $tour_id ); ?>">
                                <input type="hidden" name="action" value="ytrip_submit_inquiry">
                                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'ytrip_inquiry_nonce' ); ?>">
                                
                                <div class="ytrip-booking-form__field">
                                    <label class="ytrip-booking-form__label"><?php esc_html_e( 'Name', 'ytrip' ); ?></label>
                                    <input type="text" class="ytrip-booking-form__input" name="name" required>
                                </div>
                                <div class="ytrip-booking-form__field">
                                    <label class="ytrip-booking-form__label"><?php esc_html_e( 'Email', 'ytrip' ); ?></label>
                                    <input type="email" class="ytrip-booking-form__input" name="email" required>
                                </div>
                                <div class="ytrip-booking-form__field">
                                    <label class="ytrip-booking-form__label"><?php esc_html_e( 'Message', 'ytrip' ); ?></label>
                                    <textarea class="ytrip-booking-form__input" name="message" rows="3" required></textarea>
                                </div>

                                <button type="submit" class="ytrip-btn ytrip-btn-primary ytrip-booking-form__submit" style="width: 100%;">
                                    <?php esc_html_e( 'Send Inquiry', 'ytrip' ); ?>
                                </button>
                                <div class="ytrip-form-message" style="display:none; margin-top: 10px;"></div>
                            </form>

                        <?php else : ?>
                            <!-- WooCommerce Booking Form -->
                            <?php if ( $product ) : ?>
                            <div class="ytrip-booking-card__price">
                                <span class="ytrip-booking-card__price-value"><?php echo $product->get_price_html(); ?></span>
                                <span class="ytrip-booking-card__price-unit"><?php esc_html_e( '/ person', 'ytrip' ); ?></span>
                            </div>
                            <?php endif; ?>
    
                            <form class="ytrip-booking-form" method="post" action="<?php echo esc_url( get_permalink() ); ?>">
                                <div class="ytrip-booking-form__field">
                                    <label class="ytrip-booking-form__label"><?php esc_html_e( 'Select Date', 'ytrip' ); ?></label>
                                    <input type="date" class="ytrip-booking-form__input" name="tour_date" required>
                                </div>
                                
                                <div class="ytrip-booking-form__guests">
                                    <div class="ytrip-booking-form__field">
                                        <label class="ytrip-booking-form__label"><?php esc_html_e( 'Adults', 'ytrip' ); ?></label>
                                        <input type="number" class="ytrip-booking-form__input" name="adults" min="1" value="2">
                                    </div>
                                    <div class="ytrip-booking-form__field">
                                        <label class="ytrip-booking-form__label"><?php esc_html_e( 'Children', 'ytrip' ); ?></label>
                                        <input type="number" class="ytrip-booking-form__input" name="children" min="0" value="0">
                                    </div>
                                </div>
    
                                <?php if ( $product ) : ?>
                                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>">
                                <?php endif; ?>
                                
                                <button type="submit" class="ytrip-btn ytrip-btn-primary ytrip-booking-form__submit" style="width: 100%;">
                                    <?php esc_html_e( 'Book Now', 'ytrip' ); ?>
                                </button>
                            </form>
                        <?php endif; ?>

                        <div class="ytrip-booking-card__features">
                            <div class="ytrip-booking-card__feature">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <?php esc_html_e( 'Free Cancellation', 'ytrip' ); ?>
                            </div>
                            <div class="ytrip-booking-card__feature">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <?php esc_html_e( 'Instant Confirmation', 'ytrip' ); ?>
                            </div>
                            <div class="ytrip-booking-card__feature">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                                <?php esc_html_e( 'Secure Payment', 'ytrip' ); ?>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

        </div>
    </article>
</div>

<?php get_footer(); ?>
