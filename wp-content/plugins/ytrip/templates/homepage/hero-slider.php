<?php
/**
 * Hero Slider Section
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option( 'ytrip_homepage' );
$slides = isset( $options['hero_slides'] ) ? $options['hero_slides'] : array();
?>

<section class="ytrip-hero">
    <div class="ytrip-hero__bg">
        <?php if ( ! empty( $slides[0]['image']['url'] ) ) : ?>
            <img src="<?php echo esc_url( $slides[0]['image']['url'] ); ?>" alt="<?php echo esc_attr( $slides[0]['title'] ?? '' ); ?>">
        <?php endif; ?>
    </div>
    <div class="ytrip-hero__overlay"></div>
    
    <div class="ytrip-hero__content">
        <span class="ytrip-hero__badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <?php esc_html_e( 'Award Winning Travel Agency', 'ytrip' ); ?>
        </span>
        
        <h1 class="ytrip-hero__title">
            <?php if ( ! empty( $slides[0]['title'] ) ) : ?>
                <?php echo wp_kses_post( $slides[0]['title'] ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Discover Your Next', 'ytrip' ); ?> <span><?php esc_html_e( 'Adventure', 'ytrip' ); ?></span>
            <?php endif; ?>
        </h1>
        
        <p class="ytrip-hero__subtitle">
            <?php if ( ! empty( $slides[0]['subtitle'] ) ) : ?>
                <?php echo esc_html( $slides[0]['subtitle'] ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Explore the world with our premium travel experiences. Book your dream vacation today.', 'ytrip' ); ?>
            <?php endif; ?>
        </p>
        
        <div class="ytrip-hero__cta">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'ytrip_tour' ) ); ?>" class="ytrip-btn ytrip-btn-primary ytrip-btn-lg">
                <?php esc_html_e( 'Explore Tours', 'ytrip' ); ?>
            </a>
            <a href="#search" class="ytrip-btn ytrip-btn-secondary ytrip-btn-lg" style="border-color: rgba(255,255,255,0.3); color: #fff;">
                <?php esc_html_e( 'Search Destinations', 'ytrip' ); ?>
            </a>
        </div>
    </div>
</section>
