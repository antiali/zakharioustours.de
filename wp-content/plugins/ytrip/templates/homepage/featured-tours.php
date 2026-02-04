<?php
/**
 * Featured Tours Section
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$options = get_option( 'ytrip_homepage' );
$count = isset( $options['featured_count'] ) ? intval( $options['featured_count'] ) : 6;

$tours = new WP_Query( array(
    'post_type'      => 'ytrip_tour',
    'posts_per_page' => $count,
    'meta_query'     => array(
        array(
            'key'     => 'ytrip_tour_details',
            'compare' => 'EXISTS',
        ),
    ),
) );
?>

<section class="ytrip-section">
    <div class="ytrip-container">
        <div class="ytrip-section__header">
            <h2 class="ytrip-section__title ytrip-h2"><?php esc_html_e( 'Featured Tours', 'ytrip' ); ?></h2>
            <p class="ytrip-section__subtitle"><?php esc_html_e( 'Discover our most popular travel experiences handpicked for you.', 'ytrip' ); ?></p>
        </div>
        
        <?php if ( $tours->have_posts() ) : ?>
            <div class="ytrip-tours-grid">
                <?php while ( $tours->have_posts() ) : $tours->the_post(); ?>
                    <?php include YTRIP_PATH . 'templates/parts/tour-card.php'; ?>
                <?php endwhile; ?>
            </div>
            
            <div class="ytrip-text-center" style="margin-top: 3rem;">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'ytrip_tour' ) ); ?>" class="ytrip-btn ytrip-btn-secondary">
                    <?php esc_html_e( 'View All Tours', 'ytrip' ); ?>
                </a>
            </div>
        <?php else : ?>
            <p class="ytrip-text-center"><?php esc_html_e( 'No tours found.', 'ytrip' ); ?></p>
        <?php endif; ?>
        
        <?php wp_reset_postdata(); ?>
    </div>
</section>
