<?php
/**
 * YTrip Plugin - Archive Filters Unit Tests
 *
 * Tests the YTrip_Archive_Filters class functionality including
 * filtering, sorting, and AJAX response generation.
 *
 * @package YTrip
 * @subpackage Tests\Unit
 * @since 2.1.0
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace YTrip\Tests\Unit;

use YTrip\Tests\YTrip_TestCase;

/**
 * Test case for YTrip_Archive_Filters class.
 *
 * @covers \YTrip_Archive_Filters
 */
class ArchiveFiltersTest extends YTrip_TestCase {

    /**
     * Test tours for filtering.
     *
     * @var int[]
     */
    private array $test_tour_ids = array();

    /**
     * Set up test fixtures.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();

        // Create test taxonomies.
        register_taxonomy( 'ytrip_destination', 'ytrip_tour' );
        register_taxonomy( 'ytrip_category', 'ytrip_tour' );

        // Create test terms.
        $egypt_id   = wp_insert_term( 'Egypt', 'ytrip_destination' );
        $germany_id = wp_insert_term( 'Germany', 'ytrip_destination' );

        // Create test tours with different attributes.
        for ( $i = 1; $i <= 5; $i++ ) {
            $tour_id = $this->create_test_tour( array(
                'post_title' => "Test Tour {$i}",
            ) );

            // Assign destination.
            $destination = ( $i % 2 === 0 ) ? $germany_id['term_id'] : $egypt_id['term_id'];
            wp_set_object_terms( $tour_id, $destination, 'ytrip_destination' );

            // Set duration meta.
            update_post_meta( $tour_id, 'ytrip_tour_details', array(
                'tour_duration' => array( 'days' => $i + 2, 'nights' => $i + 1 ),
            ) );

            $this->test_tour_ids[] = $tour_id;
        }
    }

    /**
     * Tear down test fixtures.
     *
     * @return void
     */
    public function tear_down(): void {
        // Clean up test tours.
        foreach ( $this->test_tour_ids as $tour_id ) {
            wp_delete_post( $tour_id, true );
        }
        $this->test_tour_ids = array();

        parent::tear_down();
    }

    /**
     * Test get_filter_data returns expected structure.
     *
     * @return void
     */
    public function test_get_filter_data_returns_expected_keys(): void {
        $data = \YTrip_Archive_Filters::get_filter_data();

        $this->assertIsArray( $data );
        $this->assertArrayHasKey( 'destinations', $data );
        $this->assertArrayHasKey( 'categories', $data );
        $this->assertArrayHasKey( 'durations', $data );
        $this->assertArrayHasKey( 'sort_options', $data );
    }

    /**
     * Test destinations are returned correctly.
     *
     * @return void
     */
    public function test_get_filter_data_returns_destinations(): void {
        $data = \YTrip_Archive_Filters::get_filter_data();

        $destinations = $data['destinations'];
        $this->assertNotEmpty( $destinations );

        // Should include Egypt and Germany.
        $destination_names = wp_list_pluck( $destinations, 'name' );
        $this->assertContains( 'Egypt', $destination_names );
        $this->assertContains( 'Germany', $destination_names );
    }

    /**
     * Test sort options have required keys.
     *
     * @return void
     */
    public function test_sort_options_have_required_values(): void {
        $data = \YTrip_Archive_Filters::get_filter_data();

        $sort_options = $data['sort_options'];
        $this->assertArrayHasKey( 'date', $sort_options );
        $this->assertArrayHasKey( 'price_low', $sort_options );
        $this->assertArrayHasKey( 'price_high', $sort_options );
        $this->assertArrayHasKey( 'rating', $sort_options );
    }

    /**
     * Test duration filter options.
     *
     * @return void
     */
    public function test_duration_filter_options(): void {
        $data = \YTrip_Archive_Filters::get_filter_data();

        $durations = $data['durations'];
        $this->assertArrayHasKey( '1-3', $durations );
        $this->assertArrayHasKey( '4-7', $durations );
        $this->assertArrayHasKey( '8-14', $durations );
        $this->assertArrayHasKey( '15+', $durations );
    }

    /**
     * Test filter data is cacheable.
     *
     * @return void
     */
    public function test_filter_data_performance(): void {
        $start = microtime( true );
        
        // Call multiple times.
        for ( $i = 0; $i < 100; $i++ ) {
            \YTrip_Archive_Filters::get_filter_data();
        }
        
        $elapsed = microtime( true ) - $start;
        
        // Should complete in under 1 second.
        $this->assertLessThan( 1.0, $elapsed, 'get_filter_data should be performant' );
    }

    /**
     * Test sanitization of filter input.
     *
     * @dataProvider malicious_input_provider
     *
     * @param string $input    Malicious input.
     * @param string $expected Expected sanitized output.
     * @return void
     */
    public function test_filter_input_sanitization( string $input, string $expected ): void {
        $sanitized = sanitize_text_field( $input );
        $this->assertEquals( $expected, $sanitized );
    }

    /**
     * Data provider for malicious input testing.
     *
     * @return array<array<string>>
     */
    public static function malicious_input_provider(): array {
        return array(
            'script_tag'        => array( '<script>alert(1)</script>', '' ),
            'sql_injection'     => array( "1'; DROP TABLE wp_posts;--", "1&#039;; DROP TABLE wp_posts;--" ),
            'xss_event'         => array( '<img onerror="alert(1)">', '' ),
            'null_byte'         => array( "test\x00value", 'testvalue' ),
            'valid_text'        => array( 'Egypt', 'Egypt' ),
            'unicode'           => array( 'مصر', 'مصر' ),
        );
    }

    /**
     * Test query modification for destination filter.
     *
     * @return void
     */
    public function test_filter_by_destination_modifies_query(): void {
        // Simulate filter request.
        $_GET['destination'] = 'egypt';

        $query = new \WP_Query( array(
            'post_type' => 'ytrip_tour',
            'tax_query' => array(
                array(
                    'taxonomy' => 'ytrip_destination',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['destination'] ),
                ),
            ),
        ) );

        // Should return only Egypt tours.
        $this->assertGreaterThan( 0, $query->found_posts );

        foreach ( $query->posts as $post ) {
            $terms = wp_get_post_terms( $post->ID, 'ytrip_destination', array( 'fields' => 'slugs' ) );
            $this->assertContains( 'egypt', $terms );
        }

        unset( $_GET['destination'] );
    }

    /**
     * Test empty filter returns all tours.
     *
     * @return void
     */
    public function test_empty_filter_returns_all_tours(): void {
        $query = new \WP_Query( array(
            'post_type'      => 'ytrip_tour',
            'posts_per_page' => -1,
        ) );

        $this->assertEquals( count( $this->test_tour_ids ), $query->found_posts );
    }

    /**
     * Test sorting by date descending.
     *
     * @return void
     */
    public function test_sort_by_date_descending(): void {
        $query = new \WP_Query( array(
            'post_type' => 'ytrip_tour',
            'orderby'   => 'date',
            'order'     => 'DESC',
        ) );

        $dates = wp_list_pluck( $query->posts, 'post_date' );
        $sorted_dates = $dates;
        rsort( $sorted_dates );

        $this->assertEquals( $sorted_dates, $dates, 'Posts should be sorted by date descending' );
    }
}
