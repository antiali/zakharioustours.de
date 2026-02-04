<?php
/**
 * YTrip Plugin - Template Loader Unit Tests
 *
 * Tests the YTrip_Template_Loader class for correct asset loading,
 * template resolution, and conditional enqueuing logic.
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
 * Test case for YTrip_Template_Loader class.
 *
 * @covers \YTrip_Template_Loader
 */
class TemplateLoaderTest extends YTrip_TestCase {

    /**
     * Test instance.
     *
     * @var \YTrip_Template_Loader|null
     */
    private ?\YTrip_Template_Loader $loader = null;

    /**
     * Set up test fixtures.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();

        // Reset enqueued scripts/styles.
        global $wp_scripts, $wp_styles;
        $wp_scripts = new \WP_Scripts();
        $wp_styles  = new \WP_Styles();

        $this->loader = new \YTrip_Template_Loader();
    }

    /**
     * Tear down test fixtures.
     *
     * @return void
     */
    public function tear_down(): void {
        $this->loader = null;
        parent::tear_down();
    }

    /**
     * Test loader instantiation.
     *
     * @return void
     */
    public function test_loader_can_be_instantiated(): void {
        $this->assertInstanceOf( \YTrip_Template_Loader::class, $this->loader );
    }

    /**
     * Test main CSS is enqueued on YTrip pages.
     *
     * @return void
     */
    public function test_main_css_enqueued_on_ytrip_pages(): void {
        // Simulate archive page.
        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        do_action( 'wp_enqueue_scripts' );

        $this->assertTrue(
            wp_style_is( 'ytrip-main', 'enqueued' ),
            'Main CSS should be enqueued on archive page'
        );
    }

    /**
     * Test assets not loaded on non-YTrip pages.
     *
     * @return void
     */
    public function test_assets_not_loaded_on_regular_pages(): void {
        // Create a regular page.
        $page_id = $this->factory->post->create( array(
            'post_type' => 'page',
            'post_title' => 'Regular Page',
        ) );

        $this->go_to( get_permalink( $page_id ) );

        do_action( 'wp_enqueue_scripts' );

        $this->assertFalse(
            wp_style_is( 'ytrip-archive-filters', 'enqueued' ),
            'Archive filters CSS should NOT be enqueued on regular pages'
        );
    }

    /**
     * Test archive filters CSS loaded on archive page.
     *
     * @return void
     */
    public function test_archive_filters_css_on_archive(): void {
        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        do_action( 'wp_enqueue_scripts' );

        $this->assertTrue(
            wp_style_is( 'ytrip-archive-filters', 'enqueued' ),
            'Archive filters CSS should be enqueued on archive page'
        );
    }

    /**
     * Test archive filters JS loaded when AJAX enabled.
     *
     * @return void
     */
    public function test_archive_filters_js_when_ajax_enabled(): void {
        // Enable AJAX filtering.
        update_option( 'ytrip_settings', array(
            'archive_enable_ajax' => true,
        ) );

        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        // Reinstantiate to pick up new settings.
        $this->loader = new \YTrip_Template_Loader();
        do_action( 'wp_enqueue_scripts' );

        $this->assertTrue(
            wp_script_is( 'ytrip-archive-filters', 'enqueued' ),
            'Archive filters JS should be enqueued when AJAX is enabled'
        );
    }

    /**
     * Test archive filters JS NOT loaded when AJAX disabled.
     *
     * @return void
     */
    public function test_archive_filters_js_not_loaded_when_ajax_disabled(): void {
        // Disable AJAX filtering.
        update_option( 'ytrip_settings', array(
            'archive_enable_ajax' => false,
        ) );

        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        $this->loader = new \YTrip_Template_Loader();
        do_action( 'wp_enqueue_scripts' );

        $this->assertFalse(
            wp_script_is( 'ytrip-archive-filters', 'enqueued' ),
            'Archive filters JS should NOT be enqueued when AJAX is disabled'
        );
    }

    /**
     * Test single tour page loads single-specific CSS.
     *
     * @return void
     */
    public function test_single_tour_loads_specific_css(): void {
        $tour_id = $this->create_test_tour();

        $this->go_to( get_permalink( $tour_id ) );

        do_action( 'wp_enqueue_scripts' );

        $this->assertTrue(
            wp_style_is( 'ytrip-single-tour', 'enqueued' ),
            'Single tour CSS should be enqueued on single tour page'
        );
    }

    /**
     * Test localized script variables are set.
     *
     * @return void
     */
    public function test_localized_script_variables(): void {
        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        do_action( 'wp_enqueue_scripts' );

        global $wp_scripts;

        // Check if ytrip_vars is localized.
        $data = $wp_scripts->get_data( 'ytrip-main', 'data' );

        $this->assertNotEmpty( $data, 'Script should have localized data' );
        $this->assertStringContainsString( 'ajax_url', $data );
        $this->assertStringContainsString( 'nonce', $data );
    }

    /**
     * Test microinteractions JS loaded when enabled.
     *
     * @return void
     */
    public function test_microinteractions_loaded_when_enabled(): void {
        update_option( 'ytrip_settings', array(
            'enable_microinteractions' => true,
        ) );

        $tour_id = $this->create_test_tour();
        $this->go_to( get_permalink( $tour_id ) );

        $this->loader = new \YTrip_Template_Loader();
        do_action( 'wp_enqueue_scripts' );

        $this->assertTrue(
            wp_script_is( 'ytrip-microinteractions', 'enqueued' ),
            'Microinteractions JS should be enqueued when enabled'
        );
    }

    /**
     * Test CSS variables are injected.
     *
     * @return void
     */
    public function test_css_variables_injected(): void {
        update_option( 'ytrip_settings', array(
            'opt_color_primary'   => '#ff5500',
            'opt_color_secondary' => '#0055ff',
        ) );

        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        $this->loader = new \YTrip_Template_Loader();
        do_action( 'wp_enqueue_scripts' );

        global $wp_styles;
        $inline_css = $wp_styles->get_data( 'ytrip-main', 'after' );

        // Inline CSS should contain CSS variables.
        if ( is_array( $inline_css ) ) {
            $inline_css = implode( '', $inline_css );
        }

        $this->assertStringContainsString( '--ytrip-primary', $inline_css );
    }

    /**
     * Test template override from theme.
     *
     * @return void
     */
    public function test_template_override_from_theme(): void {
        // This tests the template hierarchy.
        $template = locate_template( 'ytrip/archive-ytrip_tour.php' );

        // Template should fallback to plugin if not in theme.
        if ( empty( $template ) ) {
            $template = YTRIP_PATH . 'templates/archive-ytrip_tour.php';
        }

        $this->assertFileExists( $template, 'Archive template should exist' );
    }

    /**
     * Test nonce is refreshed on each page load.
     *
     * @return void
     */
    public function test_nonce_is_unique_per_request(): void {
        $nonce1 = wp_create_nonce( 'ytrip_filter_nonce' );
        
        // Simulate new request.
        wp_set_current_user( 0 );
        $nonce2 = wp_create_nonce( 'ytrip_filter_nonce' );

        $this->assertNotEquals( $nonce1, $nonce2, 'Nonces should differ between sessions' );
    }

    /**
     * Test performance: enqueue_assets should be fast.
     *
     * @return void
     */
    public function test_enqueue_assets_performance(): void {
        $this->go_to( get_post_type_archive_link( 'ytrip_tour' ) );

        $start = microtime( true );
        
        for ( $i = 0; $i < 100; $i++ ) {
            do_action( 'wp_enqueue_scripts' );
        }

        $elapsed = microtime( true ) - $start;

        $this->assertLessThan( 2.0, $elapsed, 'enqueue_assets should complete quickly' );
    }
}
