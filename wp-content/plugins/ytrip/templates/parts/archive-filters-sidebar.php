<?php
/**
 * Archive Filters - Sidebar
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$filter_data = YTrip_Archive_Filters::get_filter_data();
?>

<form class="ytrip-filters-form" id="ytrip-filters-form" method="get">
    
    <div class="ytrip-filter-section">
        <h4 class="ytrip-filter-section__title"><?php esc_html_e( 'Destination', 'ytrip' ); ?></h4>
        <div class="ytrip-filter-section__content">
            <select name="destination" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'All Destinations', 'ytrip' ); ?></option>
                <?php foreach ( $filter_data['destinations'] as $term ) : ?>
                <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( isset( $_GET['destination'] ) ? $_GET['destination'] : '', $term->slug ); ?>>
                    <?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="ytrip-filter-section">
        <h4 class="ytrip-filter-section__title"><?php esc_html_e( 'Category', 'ytrip' ); ?></h4>
        <div class="ytrip-filter-section__content">
            <select name="category" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'All Categories', 'ytrip' ); ?></option>
                <?php foreach ( $filter_data['categories'] as $term ) : ?>
                <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( isset( $_GET['category'] ) ? $_GET['category'] : '', $term->slug ); ?>>
                    <?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="ytrip-filter-section">
        <h4 class="ytrip-filter-section__title"><?php esc_html_e( 'Price Range', 'ytrip' ); ?></h4>
        <div class="ytrip-filter-section__content">
            <div class="ytrip-price-range">
                <input type="number" name="min_price" id="min_price" class="ytrip-input" placeholder="<?php esc_attr_e( 'Min', 'ytrip' ); ?>" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : ''; ?>">
                <span class="ytrip-price-range__sep">-</span>
                <input type="number" name="max_price" id="max_price" class="ytrip-input" placeholder="<?php esc_attr_e( 'Max', 'ytrip' ); ?>" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''; ?>">
            </div>
            <div class="ytrip-range-slider" id="ytrip-price-slider" data-min="0" data-max="5000"></div>
        </div>
    </div>

    <div class="ytrip-filter-section">
        <h4 class="ytrip-filter-section__title"><?php esc_html_e( 'Duration', 'ytrip' ); ?></h4>
        <div class="ytrip-filter-section__content ytrip-filter-checkboxes">
            <?php foreach ( $filter_data['durations'] as $value => $label ) : ?>
            <label class="ytrip-checkbox">
                <input type="radio" name="duration" value="<?php echo esc_attr( $value ); ?>" <?php checked( isset( $_GET['duration'] ) ? $_GET['duration'] : '', $value ); ?>>
                <span class="ytrip-checkbox__label"><?php echo esc_html( $label ); ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="ytrip-filter-section">
        <h4 class="ytrip-filter-section__title"><?php esc_html_e( 'Rating', 'ytrip' ); ?></h4>
        <div class="ytrip-filter-section__content ytrip-filter-checkboxes">
            <?php for ( $i = 5; $i >= 3; $i-- ) : ?>
            <label class="ytrip-checkbox">
                <input type="radio" name="rating" value="<?php echo $i; ?>" <?php checked( isset( $_GET['rating'] ) ? absint( $_GET['rating'] ) : 0, $i ); ?>>
                <span class="ytrip-checkbox__label">
                    <span class="ytrip-stars"><?php echo str_repeat( '★', $i ); ?><?php echo str_repeat( '☆', 5 - $i ); ?></span>
                    <?php esc_html_e( '& Up', 'ytrip' ); ?>
                </span>
            </label>
            <?php endfor; ?>
        </div>
    </div>

    <div class="ytrip-filter-actions">
        <button type="submit" class="ytrip-btn ytrip-btn-primary ytrip-btn-block">
            <?php esc_html_e( 'Apply Filters', 'ytrip' ); ?>
        </button>
        <button type="button" class="ytrip-btn ytrip-btn-outline ytrip-btn-block ytrip-clear-filters">
            <?php esc_html_e( 'Clear All', 'ytrip' ); ?>
        </button>
    </div>

</form>
