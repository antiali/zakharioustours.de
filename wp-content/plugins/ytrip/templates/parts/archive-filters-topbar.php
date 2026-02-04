<?php
/**
 * Archive Filters - Top Bar
 * 
 * @package YTrip
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$filter_data = YTrip_Archive_Filters::get_filter_data();
?>

<form class="ytrip-filters-topbar" id="ytrip-filters-topbar" method="get">
    <div class="ytrip-filters-topbar__row">
        
        <div class="ytrip-filter-item">
            <label><?php esc_html_e( 'Destination', 'ytrip' ); ?></label>
            <select name="destination" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'All', 'ytrip' ); ?></option>
                <?php foreach ( $filter_data['destinations'] as $term ) : ?>
                <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( isset( $_GET['destination'] ) ? $_GET['destination'] : '', $term->slug ); ?>>
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ytrip-filter-item">
            <label><?php esc_html_e( 'Category', 'ytrip' ); ?></label>
            <select name="category" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'All', 'ytrip' ); ?></option>
                <?php foreach ( $filter_data['categories'] as $term ) : ?>
                <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( isset( $_GET['category'] ) ? $_GET['category'] : '', $term->slug ); ?>>
                    <?php echo esc_html( $term->name ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ytrip-filter-item">
            <label><?php esc_html_e( 'Duration', 'ytrip' ); ?></label>
            <select name="duration" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'Any', 'ytrip' ); ?></option>
                <?php foreach ( $filter_data['durations'] as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( isset( $_GET['duration'] ) ? $_GET['duration'] : '', $value ); ?>>
                    <?php echo esc_html( $label ); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="ytrip-filter-item ytrip-filter-item--price">
            <label><?php esc_html_e( 'Price', 'ytrip' ); ?></label>
            <div class="ytrip-filter-price-inputs">
                <input type="number" name="min_price" placeholder="<?php esc_attr_e( 'Min', 'ytrip' ); ?>" value="<?php echo isset( $_GET['min_price'] ) ? esc_attr( $_GET['min_price'] ) : ''; ?>">
                <span>-</span>
                <input type="number" name="max_price" placeholder="<?php esc_attr_e( 'Max', 'ytrip' ); ?>" value="<?php echo isset( $_GET['max_price'] ) ? esc_attr( $_GET['max_price'] ) : ''; ?>">
            </div>
        </div>

        <div class="ytrip-filter-item">
            <label><?php esc_html_e( 'Rating', 'ytrip' ); ?></label>
            <select name="rating" class="ytrip-filter-select">
                <option value=""><?php esc_html_e( 'Any', 'ytrip' ); ?></option>
                <?php for ( $i = 5; $i >= 3; $i-- ) : ?>
                <option value="<?php echo $i; ?>" <?php selected( isset( $_GET['rating'] ) ? absint( $_GET['rating'] ) : 0, $i ); ?>>
                    <?php echo $i; ?>+ <?php esc_html_e( 'Stars', 'ytrip' ); ?>
                </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="ytrip-filter-item ytrip-filter-item--actions">
            <button type="submit" class="ytrip-btn ytrip-btn-primary">
                <?php esc_html_e( 'Apply', 'ytrip' ); ?>
            </button>
            <button type="button" class="ytrip-btn ytrip-btn-outline ytrip-clear-filters">
                <?php esc_html_e( 'Clear', 'ytrip' ); ?>
            </button>
        </div>

    </div>
</form>
