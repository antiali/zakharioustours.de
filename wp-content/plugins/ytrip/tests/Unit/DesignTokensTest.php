<?php
/**
 * YTrip Plugin - Design Tokens Unit Tests
 *
 * Tests the YTrip_Design_Tokens class for correct token management,
 * CSS variable generation, and responsive breakpoint handling.
 *
 * @package YTrip
 * @subpackage Tests\Unit
 * @since 2.2.0
 */

declare(strict_types=1);

namespace YTrip\Tests\Unit;

use YTrip\Tests\YTrip_TestCase;

/**
 * Test case for YTrip_Design_Tokens class.
 *
 * @covers \YTrip_Design_Tokens
 */
class DesignTokensTest extends YTrip_TestCase {

    /**
     * Design tokens instance
     *
     * @var \YTrip_Design_Tokens
     */
    private $design_tokens;

    /**
     * Set up test fixtures.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();
        
        // Clear any existing options
        delete_option('ytrip_settings');
        
        // Create fresh instance
        $this->design_tokens = new \YTrip_Design_Tokens();
    }

    /**
     * Tear down test fixtures.
     *
     * @return void
     */
    public function tear_down(): void {
        delete_option('ytrip_settings');
        parent::tear_down();
    }

    /**
     * Test design tokens can be instantiated.
     *
     * @return void
     */
    public function test_design_tokens_can_be_instantiated(): void {
        $this->assertInstanceOf(\YTrip_Design_Tokens::class, $this->design_tokens);
    }

    /**
     * Test default token values are returned when no options are set.
     *
     * @return void
     */
    public function test_get_token_returns_default_value(): void {
        $bg = $this->design_tokens->get_token('cards', 'tour', 'bg');
        $this->assertEquals('#ffffff', $bg);
    }

    /**
     * Test saved option values override defaults.
     *
     * @return void
     */
    public function test_get_token_returns_saved_option(): void {
        update_option('ytrip_settings', array(
            'design_tokens_cards_tour_bg' => '#ff0000',
        ));
        
        // Recreate instance to load new options
        $tokens = new \YTrip_Design_Tokens();
        $bg = $tokens->get_token('cards', 'tour', 'bg');
        
        $this->assertEquals('#ff0000', $bg);
    }

    /**
     * Test responsive breakpoint token retrieval.
     *
     * @return void
     */
    public function test_get_token_with_breakpoint(): void {
        update_option('ytrip_settings', array(
            'design_tokens_cards_tour_padding' => '16px',
            'design_tokens_cards_tour_padding_tablet' => '14px',
            'design_tokens_cards_tour_padding_mobile' => '12px',
        ));
        
        $tokens = new \YTrip_Design_Tokens();
        
        $desktop = $tokens->get_token('cards', 'tour', 'padding', 'desktop');
        $tablet = $tokens->get_token('cards', 'tour', 'padding', 'tablet');
        $mobile = $tokens->get_token('cards', 'tour', 'padding', 'mobile');
        
        $this->assertEquals('16px', $desktop);
        $this->assertEquals('14px', $tablet);
        $this->assertEquals('12px', $mobile);
    }

    /**
     * Test get_category_tokens returns all tokens for a category.
     *
     * @return void
     */
    public function test_get_category_tokens(): void {
        $cards = $this->design_tokens->get_category_tokens('cards');
        
        $this->assertIsArray($cards);
        $this->assertArrayHasKey('tour', $cards);
        $this->assertArrayHasKey('destination', $cards);
        $this->assertArrayHasKey('badge', $cards);
    }

    /**
     * Test CSS variable generation format.
     *
     * @return void
     */
    public function test_generate_css_variables_format(): void {
        $css = $this->design_tokens->generate_css_variables();
        
        $this->assertStringContainsString(':root {', $css);
        $this->assertStringContainsString('--ytrip-cards-tour-bg:', $css);
        $this->assertStringContainsString('--ytrip-buttons-primary-bg:', $css);
        $this->assertStringContainsString('}', $css);
    }

    /**
     * Test CSS variables include correct values.
     *
     * @return void
     */
    public function test_generate_css_variables_values(): void {
        update_option('ytrip_settings', array(
            'design_tokens_cards_tour_bg' => '#123456',
        ));
        
        $tokens = new \YTrip_Design_Tokens();
        $css = $tokens->generate_css_variables();
        
        $this->assertStringContainsString('--ytrip-cards-tour-bg: #123456', $css);
    }

    /**
     * Test responsive CSS generation.
     *
     * @return void
     */
    public function test_get_responsive_css(): void {
        $css = $this->design_tokens->get_responsive_css();
        
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':root', $css);
    }

    /**
     * Test breakpoint definitions.
     *
     * @return void
     */
    public function test_get_breakpoints(): void {
        $breakpoints = $this->design_tokens->get_breakpoints();
        
        $this->assertIsArray($breakpoints);
        $this->assertArrayHasKey('mobile', $breakpoints);
        $this->assertArrayHasKey('tablet', $breakpoints);
        $this->assertArrayHasKey('desktop', $breakpoints);
        $this->assertEquals(767, $breakpoints['mobile']);
        $this->assertEquals(1024, $breakpoints['tablet']);
    }

    /**
     * Test custom breakpoint setting.
     *
     * @return void
     */
    public function test_set_breakpoint(): void {
        $this->design_tokens->set_breakpoint('mobile', 600);
        $breakpoints = $this->design_tokens->get_breakpoints();
        
        $this->assertEquals(600, $breakpoints['mobile']);
    }

    /**
     * Test invalid breakpoint is ignored.
     *
     * @return void
     */
    public function test_set_invalid_breakpoint(): void {
        $original = $this->design_tokens->get_breakpoints();
        $this->design_tokens->set_breakpoint('nonexistent', 500);
        $after = $this->design_tokens->get_breakpoints();
        
        $this->assertEquals($original, $after);
    }

    /**
     * Test token export to JSON.
     *
     * @return void
     */
    public function test_export_tokens(): void {
        $json = $this->design_tokens->export_tokens();
        $data = json_decode($json, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('cards', $data);
        $this->assertArrayHasKey('buttons', $data);
        $this->assertArrayHasKey('forms', $data);
    }

    /**
     * Test token import from valid JSON.
     *
     * @return void
     */
    public function test_import_tokens_valid_json(): void {
        $json = json_encode(array(
            'cards' => array(
                'tour' => array(
                    'bg' => '#aabbcc',
                ),
            ),
        ));
        
        $result = $this->design_tokens->import_tokens($json);
        $this->assertTrue($result);
        
        $options = get_option('ytrip_settings');
        $this->assertEquals('#aabbcc', $options['design_tokens_cards_tour_bg']);
    }

    /**
     * Test token import fails with invalid JSON.
     *
     * @return void
     */
    public function test_import_tokens_invalid_json(): void {
        $result = $this->design_tokens->import_tokens('not valid json');
        $this->assertFalse($result);
    }

    /**
     * Test badge position options.
     *
     * @return void
     */
    public function test_get_badge_positions(): void {
        $positions = \YTrip_Design_Tokens::get_badge_positions();
        
        $this->assertIsArray($positions);
        $this->assertArrayHasKey('top-left', $positions);
        $this->assertArrayHasKey('top-right', $positions);
        $this->assertArrayHasKey('bottom-left', $positions);
        $this->assertArrayHasKey('bottom-right', $positions);
        $this->assertArrayHasKey('overlay-center', $positions);
    }

    /**
     * Test badge style options.
     *
     * @return void
     */
    public function test_get_badge_styles(): void {
        $styles = \YTrip_Design_Tokens::get_badge_styles();
        
        $this->assertIsArray($styles);
        $this->assertArrayHasKey('pill', $styles);
        $this->assertArrayHasKey('square', $styles);
        $this->assertArrayHasKey('ribbon', $styles);
        $this->assertArrayHasKey('bubble', $styles);
    }

    /**
     * Test price format options.
     *
     * @return void
     */
    public function test_get_price_formats(): void {
        $formats = \YTrip_Design_Tokens::get_price_formats();
        
        $this->assertIsArray($formats);
        $this->assertArrayHasKey('price_only', $formats);
        $this->assertArrayHasKey('from_price', $formats);
        $this->assertArrayHasKey('per_person', $formats);
        $this->assertArrayHasKey('starting_at', $formats);
    }

    /**
     * Test timing function options.
     *
     * @return void
     */
    public function test_get_timing_functions(): void {
        $timings = \YTrip_Design_Tokens::get_timing_functions();
        
        $this->assertIsArray($timings);
        $this->assertArrayHasKey('ease', $timings);
        $this->assertArrayHasKey('ease-in', $timings);
        $this->assertArrayHasKey('ease-out', $timings);
        $this->assertArrayHasKey('linear', $timings);
    }

    /**
     * Test boolean token values are converted correctly.
     *
     * @return void
     */
    public function test_boolean_token_in_css(): void {
        $css = $this->design_tokens->generate_css_variables();
        
        // Boolean values should be converted to 1 or 0
        $this->assertStringContainsString('--ytrip-cards-meta-show-rating: 1', $css);
    }

    /**
     * Test that nested arrays are skipped in CSS generation.
     *
     * @return void
     */
    public function test_nested_arrays_skipped_in_css(): void {
        $css = $this->design_tokens->generate_css_variables();
        
        // Should not contain "Array" or broken CSS
        $this->assertStringNotContainsString('Array', $css);
    }

    /**
     * Test performance: CSS generation should be fast.
     *
     * @return void
     */
    public function test_css_generation_performance(): void {
        $start = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $this->design_tokens->generate_css_variables();
        }
        
        $elapsed = microtime(true) - $start;
        
        // 100 generations should take less than 1 second
        $this->assertLessThan(1.0, $elapsed, 'CSS generation too slow');
    }
}
