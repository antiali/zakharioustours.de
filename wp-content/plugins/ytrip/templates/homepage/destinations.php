<?php
/**
 * Destinations Section
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$destinations = get_terms( array(
    'taxonomy'   => 'ytrip_destination',
    'hide_empty' => false,
    'number'     => 5,
) );
?>

<section class="ytrip-section ytrip-section--gray">
    <div class="ytrip-container">
        <div class="ytrip-section__header">
            <h2 class="ytrip-section__title ytrip-h2"><?php esc_html_e( 'Popular Destinations', 'ytrip' ); ?></h2>
            <p class="ytrip-section__subtitle"><?php esc_html_e( 'Explore breathtaking locations around the world.', 'ytrip' ); ?></p>
        </div>
        
        <?php if ( $destinations && ! is_wp_error( $destinations ) ) : ?>
            <div class="ytrip-destinations-grid">
                <?php foreach ( $destinations as $dest ) : 
                    $image_id = get_term_meta( $dest->term_id, 'thumbnail_id', true );
                    $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
                ?>
                    <a href="<?php echo esc_url( get_term_link( $dest ) ); ?>" class="ytrip-destination-card">
                        <?php if ( $image_url ) : ?>
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $dest->name ); ?>" class="ytrip-destination-card__image">
                        <?php else : ?>
                            <div class="ytrip-destination-card__image" style="background: linear-gradient(135deg, var(--ytrip-primary), var(--ytrip-secondary));"></div>
                        <?php endif; ?>
                        <div class="ytrip-destination-card__overlay"></div>
                        <div class="ytrip-destination-card__content">
                            <h3 class="ytrip-destination-card__name"><?php echo esc_html( $dest->name ); ?></h3>
                            <span class="ytrip-destination-card__count">
                                <?php 
                                /* translators: %d: number of tours */
                                printf( esc_html( _n( '%d Tour', '%d Tours', $dest->count, 'ytrip' ) ), $dest->count ); 
                                ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
