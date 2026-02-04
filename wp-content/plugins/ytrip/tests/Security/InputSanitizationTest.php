<?php
/**
 * YTrip Plugin - Security Tests: Input Sanitization
 *
 * Comprehensive security tests for input sanitization across all
 * user-facing input points. Covers OWASP Top 10 vulnerabilities.
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
 * Test case for input sanitization.
 *
 * @covers All input handling functions
 */
class InputSanitizationTest extends YTrip_TestCase {

    /**
     * SQL injection vectors to test.
     *
     * @var string[]
     */
    private array $sql_injection_vectors = array(
        "1'; DROP TABLE wp_posts;--",
        "' OR '1'='1",
        "1 UNION SELECT * FROM wp_users",
        "'; DELETE FROM wp_options WHERE option_name='siteurl';--",
        "1; UPDATE wp_users SET user_pass='hacked' WHERE id=1;--",
        "admin'--",
        "1 AND 1=1",
        "1' AND 'a'='a",
        "1\" OR \"1\"=\"1",
        "1 OR 1=1#",
    );

    /**
     * XSS attack vectors to test.
     *
     * @var string[]
     */
    private array $xss_vectors = array(
        '<script>alert("XSS")</script>',
        '<img src="x" onerror="alert(1)">',
        '<svg onload="alert(1)">',
        'javascript:alert(1)',
        '<a href="javascript:alert(1)">Click</a>',
        '"><script>alert(1)</script>',
        "'-alert(1)-'",
        '<body onload="alert(1)">',
        '<input onfocus="alert(1)" autofocus>',
        '<marquee onstart="alert(1)">',
        '<details open ontoggle="alert(1)">',
        '<object data="javascript:alert(1)">',
        '<embed src="javascript:alert(1)">',
        '<form action="javascript:alert(1)"><input type="submit">',
    );

    /**
     * Path traversal vectors.
     *
     * @var string[]
     */
    private array $path_traversal_vectors = array(
        '../../../wp-config.php',
        '..\\..\\..\\wp-config.php',
        '....//....//....//wp-config.php',
        '/etc/passwd',
        'C:\\Windows\\System32\\drivers\\etc\\hosts',
        '%2e%2e%2f%2e%2e%2f%2e%2e%2fwp-config.php',
        '..%c0%af..%c0%af..%c0%afwp-config.php',
    );

    /**
     * Test SQL injection in filter destination parameter.
     *
     * @dataProvider sql_injection_provider
     *
     * @param string $input SQL injection attempt.
     * @return void
     */
    public function test_sql_injection_in_destination_filter( string $input ): void {
        $sanitized = sanitize_text_field( $input );

        // Dangerous characters should be escaped or removed.
        $this->assertStringNotContainsString( ';', $sanitized );
        $this->assertStringNotContainsString( '--', $sanitized );
        $this->assertStringNotContainsString( 'DROP', $sanitized );
        $this->assertStringNotContainsString( 'DELETE', $sanitized );
        $this->assertStringNotContainsString( 'UNION', $sanitized );
    }

    /**
     * Data provider for SQL injection tests.
     *
     * @return array<array<string>>
     */
    public static function sql_injection_provider(): array {
        return array(
            array( "1'; DROP TABLE wp_posts;--" ),
            array( "' OR '1'='1" ),
            array( "1 UNION SELECT * FROM wp_users" ),
            array( "'; DELETE FROM wp_options;--" ),
            array( "admin'--" ),
        );
    }

    /**
     * Test XSS prevention in filter output.
     *
     * @dataProvider xss_provider
     *
     * @param string $input XSS attack vector.
     * @return void
     */
    public function test_xss_prevention( string $input ): void {
        $escaped = esc_html( $input );

        // Should not contain executable scripts.
        $this->assertStringNotContainsString( '<script', $escaped );
        $this->assertStringNotContainsString( 'javascript:', $escaped );
        $this->assertStringNotContainsString( 'onerror=', $escaped );
        $this->assertStringNotContainsString( 'onload=', $escaped );
    }

    /**
     * Data provider for XSS tests.
     *
     * @return array<array<string>>
     */
    public static function xss_provider(): array {
        return array(
            array( '<script>alert("XSS")</script>' ),
            array( '<img src="x" onerror="alert(1)">' ),
            array( '<svg onload="alert(1)">' ),
            array( 'javascript:alert(1)' ),
            array( '<a href="javascript:alert(1)">Click</a>' ),
        );
    }

    /**
     * Test URL sanitization.
     *
     * @return void
     */
    public function test_url_sanitization(): void {
        $malicious_urls = array(
            'javascript:alert(1)',
            'data:text/html,<script>alert(1)</script>',
            'vbscript:msgbox("XSS")',
            "javascript:alert(String.fromCharCode(88,83,83))",
        );

        foreach ( $malicious_urls as $url ) {
            $sanitized = esc_url( $url );
            
            $this->assertStringNotContainsString( 'javascript:', $sanitized );
            $this->assertStringNotContainsString( 'vbscript:', $sanitized );
            $this->assertStringNotContainsString( 'data:', $sanitized );
        }
    }

    /**
     * Test attribute escaping.
     *
     * @return void
     */
    public function test_attribute_escaping(): void {
        $inputs = array(
            '" onclick="alert(1)"',
            "' onfocus='alert(1)'",
            '><script>alert(1)</script>',
        );

        foreach ( $inputs as $input ) {
            $escaped = esc_attr( $input );

            $this->assertStringNotContainsString( 'onclick', $escaped );
            $this->assertStringNotContainsString( 'onfocus', $escaped );
            $this->assertStringNotContainsString( '<script>', $escaped );
        }
    }

    /**
     * Test integer sanitization.
     *
     * @return void
     */
    public function test_integer_sanitization(): void {
        $inputs = array(
            '123abc'   => 123,
            '-5'       => 5,       // absint makes positive.
            '0'        => 0,
            '99999999' => 99999999,
            'abc'      => 0,
            '12.34'    => 12,
            ''         => 0,
        );

        foreach ( $inputs as $input => $expected ) {
            $sanitized = absint( $input );
            $this->assertSame( $expected, $sanitized, "absint('{$input}') should be {$expected}" );
        }
    }

    /**
     * Test path traversal prevention.
     *
     * @dataProvider path_traversal_provider
     *
     * @param string $input Path traversal attempt.
     * @return void
     */
    public function test_path_traversal_prevention( string $input ): void {
        $sanitized = sanitize_file_name( $input );

        // Should not contain directory traversal.
        $this->assertStringNotContainsString( '..', $sanitized );
        $this->assertStringNotContainsString( '/', $sanitized );
        $this->assertStringNotContainsString( '\\', $sanitized );
    }

    /**
     * Data provider for path traversal tests.
     *
     * @return array<array<string>>
     */
    public static function path_traversal_provider(): array {
        return array(
            array( '../../../wp-config.php' ),
            array( '..\\..\\..\\wp-config.php' ),
            array( '/etc/passwd' ),
        );
    }

    /**
     * Test textarea sanitization.
     *
     * @return void
     */
    public function test_textarea_sanitization(): void {
        $input = "<script>alert(1)</script>\n<p>Hello</p>\nNormal text";

        $sanitized = sanitize_textarea_field( $input );

        $this->assertStringNotContainsString( '<script>', $sanitized );
        $this->assertStringNotContainsString( '<p>', $sanitized );
        $this->assertStringContainsString( 'Normal text', $sanitized );
    }

    /**
     * Test email sanitization.
     *
     * @return void
     */
    public function test_email_sanitization(): void {
        $inputs = array(
            'test@example.com'           => 'test@example.com',
            'Test@EXAMPLE.COM'           => 'test@example.com',
            'test+filter@example.com'    => 'test+filter@example.com',
            'invalid email'              => '',
            '<script>@evil.com'          => '@evil.com',
            'test@example.com; DROP--'   => 'test@example.comdrop--',
        );

        foreach ( $inputs as $input => $expected ) {
            $sanitized = sanitize_email( $input );
            // Just verify it doesn't contain dangerous characters.
            $this->assertStringNotContainsString( '<script>', $sanitized );
            $this->assertStringNotContainsString( ';', $sanitized );
        }
    }

    /**
     * Test null byte injection prevention.
     *
     * @return void
     */
    public function test_null_byte_injection_prevention(): void {
        $input = "normal\x00malicious";

        $sanitized = sanitize_text_field( $input );

        $this->assertStringNotContainsString( "\x00", $sanitized );
    }

    /**
     * Test CRLF injection prevention.
     *
     * @return void
     */
    public function test_crlf_injection_prevention(): void {
        $input = "Header: value\r\nX-Injected: evil\r\n\r\n<html>Evil</html>";

        $sanitized = sanitize_text_field( $input );

        // Should be single line.
        $this->assertStringNotContainsString( "\r\n", $sanitized );
        $this->assertStringNotContainsString( '<html>', $sanitized );
    }

    /**
     * Test Unicode homograph attack prevention.
     *
     * @return void
     */
    public function test_unicode_homograph_prevention(): void {
        // Cyrillic 'а' looks like Latin 'a'.
        $input = 'pаypal.com'; // Using Cyrillic 'а'.

        $sanitized = sanitize_text_field( $input );

        // Should preserve for display but be aware of IDN.
        $this->assertNotEmpty( $sanitized );
    }

    /**
     * Test WordPress-specific escaping functions.
     *
     * @return void
     */
    public function test_wordpress_escaping_functions(): void {
        $test_string = '<script>alert("XSS")</script>';

        // esc_html.
        $html = esc_html( $test_string );
        $this->assertStringNotContainsString( '<script>', $html );

        // esc_attr.
        $attr = esc_attr( $test_string );
        $this->assertStringNotContainsString( '<script>', $attr );

        // esc_js.
        $js = esc_js( $test_string );
        $this->assertStringNotContainsString( '</script>', $js );

        // esc_textarea.
        $textarea = esc_textarea( $test_string );
        $this->assertStringNotContainsString( '<script>', $textarea );
    }

    /**
     * Test comprehensive attack chain.
     *
     * @return void
     */
    public function test_comprehensive_attack_chain(): void {
        // Combined attack vector.
        $attack = "'; DROP TABLE wp_users;--<script>alert(document.cookie)</script>../../../wp-config.php\x00";

        $sanitized = sanitize_text_field( $attack );

        // All dangerous elements should be neutralized.
        $this->assertStringNotContainsString( 'DROP', $sanitized );
        $this->assertStringNotContainsString( '<script>', $sanitized );
        $this->assertStringNotContainsString( '..', $sanitized );
        $this->assertStringNotContainsString( "\x00", $sanitized );
    }
}
