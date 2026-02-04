<?php
/**
 * Search Form Section
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$destinations = get_terms( array( 'taxonomy' => 'ytrip_destination', 'hide_empty' => false ) );
?>

<section class="ytrip-search" id="search">
    <form class="ytrip-search__form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'ytrip_tour' ) ); ?>">
        <div class="ytrip-search__field">
            <label class="ytrip-search__label"><?php esc_html_e( 'Destination', 'ytrip' ); ?></label>
            <select name="destination" class="ytrip-search__input ytrip-search__select">
                <option value=""><?php esc_html_e( 'Where do you want to go?', 'ytrip' ); ?></option>
                <?php if ( $destinations && ! is_wp_error( $destinations ) ) : ?>
                    <?php foreach ( $destinations as $dest ) : ?>
                        <option value="<?php echo esc_attr( $dest->slug ); ?>"><?php echo esc_html( $dest->name ); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="ytrip-search__field">
            <label class="ytrip-search__label"><?php esc_html_e( 'Date', 'ytrip' ); ?></label>
            <input type="date" name="tour_date" class="ytrip-search__input ytrip-search__date" placeholder="<?php esc_attr_e( 'Select date', 'ytrip' ); ?>">
        </div>
        
        <div class="ytrip-search__field">
            <label class="ytrip-search__label"><?php esc_html_e( 'Guests', 'ytrip' ); ?></label>
            <select name="guests" class="ytrip-search__input">
                <option value="1">1 <?php esc_html_e( 'Guest', 'ytrip' ); ?></option>
                <option value="2" selected>2 <?php esc_html_e( 'Guests', 'ytrip' ); ?></option>
                <option value="3">3 <?php esc_html_e( 'Guests', 'ytrip' ); ?></option>
                <option value="4">4 <?php esc_html_e( 'Guests', 'ytrip' ); ?></option>
                <option value="5">5+ <?php esc_html_e( 'Guests', 'ytrip' ); ?></option>
            </select>
        </div>
        
        <div class="ytrip-search__field ytrip-search__submit">
            <button type="submit" class="ytrip-btn ytrip-btn-primary" style="width: 100%;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <?php esc_html_e( 'Search', 'ytrip' ); ?>
            </button>
        </div>
    </form>
</section>
