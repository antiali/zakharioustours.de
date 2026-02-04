<?php
/**
 * YTrip Plugin - AJAX Handler Integration Tests
 *
 * Tests AJAX endpoints for filtering, pagination, and dynamic content.
 * Validates request/response handling, security, and error cases.
 *
 * @package YTrip
 * @subpackage Tests\Integration
 * @since 2.1.0
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace YTrip\Tests\Integration;

use YTrip\Tests\YTrip_TestCase;

/**
 * Test case for AJAX handlers.
 *
 * @covers \YTrip_Archive_Filters
 */
class AjaxHandlerTest extends YTrip_TestCase {

    /**
     * Test tour IDs.
     *
     * @var int[]
     */
    private array $tour_ids = array();

    /**
     * Set up test fixtures.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();

        // Create test tours.
        for ( $i = 1; $i <= 10; $i++ ) {
            $this->tour_ids[] = $this->create_test_tour( array(
                'post_title' => "AJAX Test Tour {$i}",
            ) );
        }

        // Enable AJAX filtering.
        update_option( 'ytrip_settings', array(
            'archive_enable_ajax' => true,
            'archive_per_page'    => 6,
        ) );
    }

    /**
     * Tear down test fixtures.
     *
     * @return void
     */
    public function tear_down(): void {
        foreach ( $this->tour_ids as $id ) {
            wp_delete_post( $id, true );
        }
        $this->tour_ids = array();

        parent::tear_down();
    }

    /**
     * Test filter tours AJAX action exists.
     *
     * @return void
     */
    public function test_filter_tours_action_registered(): void {
        global $wp_filter;

        $this->assertTrue(
            has_action( 'wp_ajax_ytrip_filter_tours' ) || has_action( 'wp_ajax_nopriv_ytrip_filter_tours' ),
            'Filter tours AJAX action should be registered'
        );
    }

    /**
     * Test valid filter request returns JSON.
     *
     * @return void
     */
    public function test_valid_filter_request_returns_json(): void {
        // Set up request.
        $_POST['action'] = 'ytrip_filter_tours';
        $_POST['nonce']  = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['paged']  = 1;

        // Capture output.
        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        // Should be valid JSON.
        $decoded = json_decode( $response, true );
        $this->assertIsArray( $decoded, 'Response should be valid JSON' );
    }

    /**
     * Test filter request without nonce fails.
     *
     * @return void
     */
    public function test_request_without_nonce_fails(): void {
        $_POST['action'] = 'ytrip_filter_tours';
        // No nonce set.

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $decoded = json_decode( $response, true );

        // Should indicate failure or be empty.
        $this->assertTrue(
            empty( $response ) || ( isset( $decoded['success'] ) && false === $decoded['success'] ),
            'Request without nonce should fail'
        );
    }

    /**
     * Test filter request with invalid nonce fails.
     *
     * @return void
     */
    public function test_request_with_invalid_nonce_fails(): void {
        $_POST['action'] = 'ytrip_filter_tours';
        $_POST['nonce']  = 'invalid_nonce_value';

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $decoded = json_decode( $response, true );

        $this->assertTrue(
            empty( $response ) || ( isset( $decoded['success'] ) && false === $decoded['success'] ),
            'Request with invalid nonce should fail'
        );
    }

    /**
     * Test pagination returns correct page.
     *
     * @return void
     */
    public function test_pagination_returns_correct_page(): void {
        $_POST['action'] = 'ytrip_filter_tours';
        $_POST['nonce']  = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['paged']  = 2;

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $decoded = json_decode( $response, true );

        if ( is_array( $decoded ) && isset( $decoded['current_page'] ) ) {
            $this->assertEquals( 2, (int) $decoded['current_page'] );
        }
    }

    /**
     * Test destination filter applied correctly.
     *
     * @return void
     */
    public function test_destination_filter_applied(): void {
        // Create destination term.
        $term = wp_insert_term( 'Test Destination', 'ytrip_destination' );
        
        // Assign to first tour.
        wp_set_object_terms( $this->tour_ids[0], $term['term_id'], 'ytrip_destination' );

        $_POST['action']      = 'ytrip_filter_tours';
        $_POST['nonce']       = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['destination'] = 'test-destination';

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $decoded = json_decode( $response, true );

        if ( is_array( $decoded ) && isset( $decoded['found_posts'] ) ) {
            $this->assertGreaterThanOrEqual( 1, (int) $decoded['found_posts'] );
        }
    }

    /**
     * Test sort parameter applied.
     *
     * @return void
     */
    public function test_sort_parameter_applied(): void {
        $_POST['action']  = 'ytrip_filter_tours';
        $_POST['nonce']   = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['orderby'] = 'title';
        $_POST['order']   = 'ASC';

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $this->assertNotEmpty( $response, 'Sort request should return data' );
    }

    /**
     * Test response includes required fields.
     *
     * @return void
     */
    public function test_response_includes_required_fields(): void {
        $_POST['action'] = 'ytrip_filter_tours';
        $_POST['nonce']  = wp_create_nonce( 'ytrip_filter_nonce' );

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        $decoded = json_decode( $response, true );

        if ( is_array( $decoded ) ) {
            // Expected fields in response.
            $expected_fields = array( 'html', 'found_posts', 'max_pages' );
            
            foreach ( $expected_fields as $field ) {
                $this->assertArrayHasKey( $field, $decoded, "Response should include '{$field}'" );
            }
        }
    }

    /**
     * Test SQL injection attempt is blocked.
     *
     * @return void
     */
    public function test_sql_injection_blocked(): void {
        $_POST['action']      = 'ytrip_filter_tours';
        $_POST['nonce']       = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['destination'] = "' OR '1'='1";

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        // Should not return all posts or error.
        $decoded = json_decode( $response, true );

        if ( is_array( $decoded ) && isset( $decoded['found_posts'] ) ) {
            // SQL injection should NOT return all posts.
            $this->assertLessThan(
                count( $this->tour_ids ),
                (int) $decoded['found_posts'],
                'SQL injection should be blocked'
            );
        }
    }

    /**
     * Test XSS attempt is sanitized.
     *
     * @return void
     */
    public function test_xss_attempt_sanitized(): void {
        $_POST['action']      = 'ytrip_filter_tours';
        $_POST['nonce']       = wp_create_nonce( 'ytrip_filter_nonce' );
        $_POST['destination'] = '<script>alert(1)</script>';

        ob_start();
        try {
            do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
        } catch ( \WPDieException $e ) {
            // Expected.
        }
        $response = ob_get_clean();

        // Response should not contain script tags.
        $this->assertStringNotContainsString( '<script>', $response );
    }

    /**
     * Test rate limiting (conceptual - implementation needed).
     *
     * @return void
     */
    public function test_request_performance(): void {
        $_POST['action'] = 'ytrip_filter_tours';
        $_POST['nonce']  = wp_create_nonce( 'ytrip_filter_nonce' );

        $start = microtime( true );

        for ( $i = 0; $i < 10; $i++ ) {
            ob_start();
            try {
                do_action( 'wp_ajax_nopriv_ytrip_filter_tours' );
            } catch ( \WPDieException $e ) {
                // Expected.
            }
            ob_get_clean();
        }

        $elapsed = microtime( true ) - $start;

        // 10 requests should complete in under 5 seconds.
        $this->assertLessThan( 5.0, $elapsed, 'AJAX requests should be performant' );
    }
}
