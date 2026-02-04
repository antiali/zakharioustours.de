<?php
/**
 * YTrip Plugin - PHPUnit Test Bootstrap
 *
 * Initializes the WordPress testing environment for YTrip plugin tests.
 *
 * @package YTrip
 * @subpackage Tests
 * @since 2.1.0
 * @license GPL-2.0-or-later
 *
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
 */

declare(strict_types=1);

namespace YTrip\Tests;

// Define testing constant.
define( 'YTRIP_TESTING', true );

// Composer autoloader.
$composer_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
    require_once $composer_autoload;
}

/**
 * Determine the WordPress tests directory.
 *
 * Priority:
 * 1. WP_TESTS_DIR environment variable
 * 2. /tmp/wordpress-tests-lib (common CI/CD path)
 * 3. ../wordpress-develop/tests/phpunit (local dev)
 */
$wp_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $wp_tests_dir ) {
    $possible_paths = array(
        '/tmp/wordpress-tests-lib',
        dirname( __DIR__, 3 ) . '/wordpress-develop/tests/phpunit',
        dirname( __DIR__, 2 ) . '/wordpress-tests-lib',
    );

    foreach ( $possible_paths as $path ) {
        if ( is_dir( $path ) ) {
            $wp_tests_dir = $path;
            break;
        }
    }
}

if ( ! $wp_tests_dir ) {
    echo "\033[31mWordPress test library not found. Set WP_TESTS_DIR environment variable.\033[0m\n";
    echo "Install with: bash bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]\n";
    exit( 1 );
}

// Load WordPress test config.
$wp_tests_config = $wp_tests_dir . '/includes/functions.php';

if ( ! file_exists( $wp_tests_config ) ) {
    echo "\033[31mWordPress test functions not found at: {$wp_tests_config}\033[0m\n";
    exit( 1 );
}

require_once $wp_tests_config;

/**
 * Manually load the plugin before WP boots.
 *
 * @return void
 */
function ytrip_tests_manually_load_plugin(): void {
    // Load CodeStar Framework if available.
    $csf_path = dirname( __DIR__ ) . '/vendor/codestar-framework/codestar-framework.php';
    if ( file_exists( $csf_path ) ) {
        require_once $csf_path;
    }

    // Load YTrip plugin.
    require_once dirname( __DIR__ ) . '/ytrip.php';
}

tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\ytrip_tests_manually_load_plugin' );

// Load WordPress testing bootstrap.
require $wp_tests_dir . '/includes/bootstrap.php';

/**
 * Base Test Case for YTrip tests.
 *
 * Provides common setup and teardown functionality.
 */
abstract class YTrip_TestCase extends \WP_UnitTestCase {

    /**
     * Default YTrip settings for testing.
     *
     * @var array<string, mixed>
     */
    protected array $default_settings = array(
        'archive_default_view'     => 'grid',
        'archive_default_columns'  => 3,
        'archive_per_page'         => 12,
        'archive_enable_ajax'      => true,
        'archive_pagination_style' => 'numbered',
        'enable_animations'        => false,
        'enable_parallax'          => false,
    );

    /**
     * Set up test environment.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();
        
        // Reset YTrip settings to defaults.
        update_option( 'ytrip_settings', $this->default_settings );
    }

    /**
     * Tear down test environment.
     *
     * @return void
     */
    public function tear_down(): void {
        parent::tear_down();
        
        // Clean up.
        delete_option( 'ytrip_settings' );
    }

    /**
     * Create a test tour post.
     *
     * @param array<string, mixed> $args Optional post arguments.
     * @return int Post ID.
     */
    protected function create_test_tour( array $args = array() ): int {
        $defaults = array(
            'post_type'   => 'ytrip_tour',
            'post_status' => 'publish',
            'post_title'  => 'Test Tour ' . wp_generate_password( 6, false ),
        );

        $post_id = wp_insert_post( array_merge( $defaults, $args ) );

        // Add default meta.
        update_post_meta( $post_id, 'ytrip_tour_details', array(
            'tour_duration' => array( 'days' => 3, 'nights' => 2 ),
            'group_size'    => array( 'min' => 1, 'max' => 20 ),
            'difficulty'    => 'moderate',
        ) );

        return $post_id;
    }

    /**
     * Simulate an AJAX request.
     *
     * @param string              $action AJAX action name.
     * @param array<string, mixed> $data   POST data.
     * @param bool                $logged_in Whether to simulate logged-in user.
     * @return string Response content.
     */
    protected function do_ajax( string $action, array $data = array(), bool $logged_in = false ): string {
        if ( $logged_in ) {
            $user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
            wp_set_current_user( $user_id );
        }

        // Set up the request.
        $_POST['action'] = $action;
        $_POST['nonce']  = wp_create_nonce( 'ytrip_filter_nonce' );

        foreach ( $data as $key => $value ) {
            $_POST[ $key ] = $value;
        }

        // Capture output.
        ob_start();
        
        try {
            do_action( 'wp_ajax_' . $action );
        } catch ( \WPDieException $e ) {
            // Expected for wp_die() in AJAX handlers.
        }

        return ob_get_clean();
    }
}

echo "\033[32m✓ YTrip Test Bootstrap loaded successfully.\033[0m\n";
