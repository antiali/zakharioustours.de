<?php
/**
 * YTrip Plugin - Design Presets Unit Tests
 *
 * Tests the YTrip_Design_Presets class for preset application,
 * JSON import/export, and AJAX handlers.
 *
 * @package YTrip
 * @subpackage Tests\Unit
 * @since 2.2.0
 */

declare(strict_types=1);

namespace YTrip\Tests\Unit;

use YTrip\Tests\YTrip_TestCase;

/**
 * Test case for YTrip_Design_Presets class.
 *
 * @covers \YTrip_Design_Presets
 */
class DesignPresetsTest extends YTrip_TestCase {

    /**
     * Design presets instance
     *
     * @var \YTrip_Design_Presets
     */
    private $presets;

    /**
     * Set up test fixtures.
     *
     * @return void
     */
    public function set_up(): void {
        parent::set_up();
        
        delete_option('ytrip_settings');
        $this->presets = new \YTrip_Design_Presets();
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
     * Test presets can be instantiated.
     *
     * @return void
     */
    public function test_presets_can_be_instantiated(): void {
        $this->assertInstanceOf(\YTrip_Design_Presets::class, $this->presets);
    }

    /**
     * Test get_presets returns all presets.
     *
     * @return void
     */
    public function test_get_presets_returns_all(): void {
        $presets = $this->presets->get_presets();
        
        $this->assertIsArray($presets);
        $this->assertCount(4, $presets);
        $this->assertArrayHasKey('modern_minimal', $presets);
        $this->assertArrayHasKey('bold_travel', $presets);
        $this->assertArrayHasKey('luxury_gold', $presets);
        $this->assertArrayHasKey('clean_corporate', $presets);
    }

    /**
     * Test get_preset returns specific preset.
     *
     * @return void
     */
    public function test_get_preset_returns_specific(): void {
        $preset = $this->presets->get_preset('modern_minimal');
        
        $this->assertIsArray($preset);
        $this->assertArrayHasKey('name', $preset);
        $this->assertArrayHasKey('colors', $preset);
        $this->assertArrayHasKey('tokens', $preset);
        $this->assertArrayHasKey('typography', $preset);
    }

    /**
     * Test get_preset returns null for invalid ID.
     *
     * @return void
     */
    public function test_get_preset_returns_null_for_invalid(): void {
        $preset = $this->presets->get_preset('nonexistent');
        $this->assertNull($preset);
    }

    /**
     * Test modern_minimal preset structure.
     *
     * @return void
     */
    public function test_modern_minimal_preset_structure(): void {
        $preset = $this->presets->get_preset('modern_minimal');
        
        $this->assertEquals('Modern Minimal', $preset['name']);
        $this->assertEquals('#2563eb', $preset['colors']['primary']);
        $this->assertEquals('Inter', $preset['typography']['body_font']);
    }

    /**
     * Test bold_travel preset structure.
     *
     * @return void
     */
    public function test_bold_travel_preset_structure(): void {
        $preset = $this->presets->get_preset('bold_travel');
        
        $this->assertEquals('Bold Travel', $preset['name']);
        $this->assertEquals('#dc2626', $preset['colors']['primary']);
        $this->assertEquals('Outfit', $preset['typography']['body_font']);
    }

    /**
     * Test luxury_gold preset structure.
     *
     * @return void
     */
    public function test_luxury_gold_preset_structure(): void {
        $preset = $this->presets->get_preset('luxury_gold');
        
        $this->assertEquals('Luxury Gold', $preset['name']);
        $this->assertEquals('#d4a574', $preset['colors']['primary']);
        $this->assertEquals('#1a1a1a', $preset['colors']['background']);
    }

    /**
     * Test clean_corporate preset structure.
     *
     * @return void
     */
    public function test_clean_corporate_preset_structure(): void {
        $preset = $this->presets->get_preset('clean_corporate');
        
        $this->assertEquals('Clean Corporate', $preset['name']);
        $this->assertEquals('#0ea5e9', $preset['colors']['primary']);
        $this->assertEquals('Roboto', $preset['typography']['body_font']);
    }

    /**
     * Test apply_preset applies colors.
     *
     * @return void
     */
    public function test_apply_preset_applies_colors(): void {
        $result = $this->presets->apply_preset('modern_minimal');
        
        $this->assertTrue($result);
        
        $options = get_option('ytrip_settings');
        $this->assertEquals('#2563eb', $options['custom_colors_primary']);
        $this->assertEquals('custom', $options['color_preset']);
    }

    /**
     * Test apply_preset applies design tokens.
     *
     * @return void
     */
    public function test_apply_preset_applies_tokens(): void {
        $this->presets->apply_preset('modern_minimal');
        $options = get_option('ytrip_settings');
        
        $this->assertEquals('#ffffff', $options['design_tokens_cards_tour_bg']);
        $this->assertEquals('12px', $options['design_tokens_cards_tour_border_radius']);
    }

    /**
     * Test apply_preset sets active_preset.
     *
     * @return void
     */
    public function test_apply_preset_sets_active_preset(): void {
        $this->presets->apply_preset('bold_travel');
        $options = get_option('ytrip_settings');
        
        $this->assertEquals('bold_travel', $options['active_preset']);
    }

    /**
     * Test apply_preset returns false for invalid preset.
     *
     * @return void
     */
    public function test_apply_preset_returns_false_for_invalid(): void {
        $result = $this->presets->apply_preset('nonexistent');
        $this->assertFalse($result);
    }

    /**
     * Test export_current_settings format.
     *
     * @return void
     */
    public function test_export_current_settings_format(): void {
        update_option('ytrip_settings', array(
            'custom_colors_primary' => '#ff0000',
        ));
        
        $presets = new \YTrip_Design_Presets();
        $json = $presets->export_current_settings('Test Preset', 'Test Description');
        $data = json_decode($json, true);
        
        $this->assertIsArray($data);
        $this->assertEquals('Test Preset', $data['name']);
        $this->assertEquals('Test Description', $data['description']);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('colors', $data);
        $this->assertArrayHasKey('tokens', $data);
    }

    /**
     * Test export includes saved colors.
     *
     * @return void
     */
    public function test_export_includes_saved_colors(): void {
        update_option('ytrip_settings', array(
            'custom_colors_primary' => '#ff0000',
            'custom_colors_secondary' => '#00ff00',
        ));
        
        $presets = new \YTrip_Design_Presets();
        $json = $presets->export_current_settings();
        $data = json_decode($json, true);
        
        $this->assertEquals('#ff0000', $data['colors']['primary']);
        $this->assertEquals('#00ff00', $data['colors']['secondary']);
    }

    /**
     * Test import_preset with valid JSON.
     *
     * @return void
     */
    public function test_import_preset_valid_json(): void {
        $json = json_encode(array(
            'name' => 'Custom Preset',
            'colors' => array(
                'primary' => '#abcdef',
            ),
            'tokens' => array(
                'cards' => array(
                    'tour' => array(
                        'bg' => '#123456',
                    ),
                ),
            ),
        ));
        
        $result = $this->presets->import_preset($json);
        $this->assertTrue($result);
        
        $options = get_option('ytrip_settings');
        $this->assertEquals('#abcdef', $options['custom_colors_primary']);
        $this->assertEquals('#123456', $options['design_tokens_cards_tour_bg']);
    }

    /**
     * Test import_preset with invalid JSON returns error.
     *
     * @return void
     */
    public function test_import_preset_invalid_json(): void {
        $result = $this->presets->import_preset('not valid json');
        
        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertEquals('invalid_json', $result->get_error_code());
    }

    /**
     * Test import_preset with empty preset returns error.
     *
     * @return void
     */
    public function test_import_preset_empty_preset(): void {
        $json = json_encode(array(
            'name' => 'Empty',
        ));
        
        $result = $this->presets->import_preset($json);
        
        $this->assertInstanceOf(\WP_Error::class, $result);
        $this->assertEquals('invalid_preset', $result->get_error_code());
    }

    /**
     * Test import sanitizes color values.
     *
     * @return void
     */
    public function test_import_sanitizes_colors(): void {
        $json = json_encode(array(
            'colors' => array(
                'primary' => '#abc', // Short hex
            ),
        ));
        
        $this->presets->import_preset($json);
        $options = get_option('ytrip_settings');
        
        // Should be sanitized (short hex is valid)
        $this->assertEquals('#abc', $options['custom_colors_primary']);
    }

    /**
     * Test get_active_preset returns custom by default.
     *
     * @return void
     */
    public function test_get_active_preset_default(): void {
        $active = $this->presets->get_active_preset();
        $this->assertEquals('custom', $active);
    }

    /**
     * Test get_active_preset after applying preset.
     *
     * @return void
     */
    public function test_get_active_preset_after_apply(): void {
        $this->presets->apply_preset('luxury_gold');
        
        $presets = new \YTrip_Design_Presets();
        $active = $presets->get_active_preset();
        
        $this->assertEquals('luxury_gold', $active);
    }

    /**
     * Test is_preset_active.
     *
     * @return void
     */
    public function test_is_preset_active(): void {
        $this->presets->apply_preset('bold_travel');
        $presets = new \YTrip_Design_Presets();
        
        $this->assertTrue($presets->is_preset_active('bold_travel'));
        $this->assertFalse($presets->is_preset_active('modern_minimal'));
    }

    /**
     * Test preset thumbnails are defined.
     *
     * @return void
     */
    public function test_preset_thumbnails_defined(): void {
        $presets = $this->presets->get_presets();
        
        foreach ($presets as $id => $preset) {
            $this->assertArrayHasKey('thumbnail', $preset, "Preset {$id} missing thumbnail");
            $this->assertStringContainsString('assets/images/presets/', $preset['thumbnail']);
        }
    }

    /**
     * Test preset descriptions are defined.
     *
     * @return void
     */
    public function test_preset_descriptions_defined(): void {
        $presets = $this->presets->get_presets();
        
        foreach ($presets as $id => $preset) {
            $this->assertArrayHasKey('description', $preset, "Preset {$id} missing description");
            $this->assertNotEmpty($preset['description']);
        }
    }

    /**
     * Test helper function ytrip_get_design_presets.
     *
     * @return void
     */
    public function test_helper_function_returns_instance(): void {
        $instance = ytrip_get_design_presets();
        $this->assertInstanceOf(\YTrip_Design_Presets::class, $instance);
        
        // Should return same instance (singleton pattern)
        $instance2 = ytrip_get_design_presets();
        $this->assertSame($instance, $instance2);
    }
}
