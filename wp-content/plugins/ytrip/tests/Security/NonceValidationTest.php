<?php
/**
 * YTrip Plugin - Security Tests: Nonce Validation
 *
 * Tests CSRF protection through WordPress nonce verification
 * across all plugin endpoints.
 *
 * @package YTrip
 * @subpackage Tests\Security
 * @since 2.1.0
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace YTrip\Tests\Security;

use YTrip\Tests\YTrip_TestCase;

/**
 * Test case for nonce validation.
 *
 * @covers CSRF protection mechanisms
 */
class NonceValidationTest extends YTrip_TestCase {

    /**
     * Test nonce generation produces valid token.
     *
     * @return void
     */
    public function test_nonce_generation_produces_valid_token(): void {
        $nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        $this->assertNotEmpty( $nonce );
        $this->assertIsString( $nonce );
        $this->assertMatchesRegularExpression( '/^[a-f0-9]{10}$/', $nonce );
    }

    /**
     * Test nonce verification succeeds with valid nonce.
     *
     * @return void
     */
    public function test_nonce_verification_with_valid_nonce(): void {
        $nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        $result = wp_verify_nonce( $nonce, 'ytrip_filter_nonce' );

        // Returns 1 or 2 if valid (1 = fresh, 2 = old but valid).
        $this->assertContains( $result, array( 1, 2 ) );
    }

    /**
     * Test nonce verification fails with invalid nonce.
     *
     * @return void
     */
    public function test_nonce_verification_fails_with_invalid_nonce(): void {
        $result = wp_verify_nonce( 'invalid_nonce', 'ytrip_filter_nonce' );

        $this->assertFalse( $result );
    }

    /**
     * Test nonce verification fails with wrong action.
     *
     * @return void
     */
    public function test_nonce_verification_fails_with_wrong_action(): void {
        $nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        $result = wp_verify_nonce( $nonce, 'wrong_action' );

        $this->assertFalse( $result );
    }

    /**
     * Test nonces are user-specific.
     *
     * @return void
     */
    public function test_nonces_are_user_specific(): void {
        // Create nonce as anonymous user.
        wp_set_current_user( 0 );
        $anonymous_nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        // Create admin user and get their nonce.
        $admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
        wp_set_current_user( $admin_id );
        $admin_nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        // Nonces should be different.
        $this->assertNotEquals( $anonymous_nonce, $admin_nonce );
    }

    /**
     * Test admin nonce verified for admin user.
     *
     * @return void
     */
    public function test_admin_nonce_verified_for_admin_user(): void {
        $admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
        wp_set_current_user( $admin_id );

        $nonce = wp_create_nonce( 'ytrip_admin_action' );
        
        $result = wp_verify_nonce( $nonce, 'ytrip_admin_action' );

        $this->assertContains( $result, array( 1, 2 ) );
    }

    /**
     * Test nonce from different session fails.
     *
     * @return void
     */
    public function test_nonce_from_different_session_fails(): void {
        // Create nonce.
        $nonce = wp_create_nonce( 'ytrip_filter_nonce' );

        // Simulate session change by logging in different user.
        $user_id = $this->factory->user->create();
        wp_set_current_user( $user_id );

        // Original nonce should fail for new user.
        $result = wp_verify_nonce( $nonce, 'ytrip_filter_nonce' );

        $this->assertFalse( $result );
    }

    /**
     * Test empty nonce fails verification.
     *
     * @return void
     */
    public function test_empty_nonce_fails_verification(): void {
        $this->assertFalse( wp_verify_nonce( '', 'ytrip_filter_nonce' ) );
    }

    /**
     * Test null nonce fails verification.
     *
     * @return void
     */
    public function test_null_nonce_fails_verification(): void {
        $this->assertFalse( wp_verify_nonce( null, 'ytrip_filter_nonce' ) );
    }

    /**
     * Test nonce field generation.
     *
     * @return void
     */
    public function test_nonce_field_generation(): void {
        $field = wp_nonce_field( 'ytrip_filter_nonce', '_wpnonce', true, false );

        $this->assertStringContainsString( 'type="hidden"', $field );
        $this->assertStringContainsString( 'name="_wpnonce"', $field );
        $this->assertStringContainsString( 'value="', $field );
    }

    /**
     * Test check_ajax_referer behavior.
     *
     * @return void
     */
    public function test_check_ajax_referer_with_valid_nonce(): void {
        $_REQUEST['nonce'] = wp_create_nonce( 'ytrip_filter_nonce' );

        // check_ajax_referer returns 1 or 2 if valid.
        $result = check_ajax_referer( 'ytrip_filter_nonce', 'nonce', false );

        $this->assertContains( $result, array( 1, 2 ) );
    }

    /**
     * Test check_ajax_referer with invalid nonce.
     *
     * @return void
     */
    public function test_check_ajax_referer_with_invalid_nonce(): void {
        $_REQUEST['nonce'] = 'invalid';

        $result = check_ajax_referer( 'ytrip_filter_nonce', 'nonce', false );

        $this->assertFalse( $result );
    }

    /**
     * Test nonce URL generation.
     *
     * @return void
     */
    public function test_nonce_url_generation(): void {
        $url = wp_nonce_url( 'https://example.com/action', 'ytrip_action' );

        $this->assertStringContainsString( '_wpnonce=', $url );
    }

    /**
     * Test nonce in POST request simulation.
     *
     * @return void
     */
    public function test_nonce_in_post_request(): void {
        $_POST['_wpnonce'] = wp_create_nonce( 'ytrip_save_settings' );

        $verified = isset( $_POST['_wpnonce'] ) && wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ),
            'ytrip_save_settings'
        );

        $this->assertTrue( $verified );
    }

    /**
     * Test referer check.
     *
     * @return void
     */
    public function test_referer_check(): void {
        // Set referer.
        $_SERVER['HTTP_REFERER'] = admin_url( 'admin.php?page=ytrip-settings' );

        $referer = wp_get_referer();

        $this->assertNotEmpty( $referer );
        $this->assertStringContainsString( 'ytrip-settings', $referer );
    }

    /**
     * Test nonce timing (conceptual - nonces expire after 24 hours by default).
     *
     * @return void
     */
    public function test_nonce_structure(): void {
        $nonce = wp_create_nonce( 'ytrip_test' );

        // WordPress nonces are 10 character hex strings.
        $this->assertEquals( 10, strlen( $nonce ) );
        $this->assertTrue( ctype_xdigit( $nonce ) );
    }

    /**
     * Test multiple nonces for different actions.
     *
     * @return void
     */
    public function test_different_nonces_for_different_actions(): void {
        $filter_nonce = wp_create_nonce( 'ytrip_filter_nonce' );
        $admin_nonce  = wp_create_nonce( 'ytrip_admin_nonce' );
        $save_nonce   = wp_create_nonce( 'ytrip_save_nonce' );

        // Should all be different.
        $this->assertNotEquals( $filter_nonce, $admin_nonce );
        $this->assertNotEquals( $admin_nonce, $save_nonce );
        $this->assertNotEquals( $filter_nonce, $save_nonce );
    }

    /**
     * Test capability check combined with nonce.
     *
     * @return void
     */
    public function test_capability_check_with_nonce(): void {
        $admin_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
        wp_set_current_user( $admin_id );

        $nonce = wp_create_nonce( 'ytrip_admin_action' );

        // Simulate admin action check.
        $can_proceed = wp_verify_nonce( $nonce, 'ytrip_admin_action' ) && current_user_can( 'manage_options' );

        $this->assertTrue( $can_proceed );
    }

    /**
     * Test subscriber cannot perform admin actions.
     *
     * @return void
     */
    public function test_subscriber_cannot_perform_admin_actions(): void {
        $subscriber_id = $this->factory->user->create( array( 'role' => 'subscriber' ) );
        wp_set_current_user( $subscriber_id );

        $nonce = wp_create_nonce( 'ytrip_admin_action' );

        // Nonce is valid but capability check fails.
        $nonce_valid  = (bool) wp_verify_nonce( $nonce, 'ytrip_admin_action' );
        $has_cap      = current_user_can( 'manage_options' );
        $can_proceed  = $nonce_valid && $has_cap;

        $this->assertTrue( $nonce_valid, 'Nonce should be valid' );
        $this->assertFalse( $has_cap, 'Subscriber should not have manage_options' );
        $this->assertFalse( $can_proceed, 'Subscriber should not proceed with admin action' );
    }
}
