<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CSF' ) ) {
    return;
}

$prefix = 'ytrip_settings';

CSF::createOptions($prefix, array(
    'menu_title'      => 'YTrip Settings',
    'menu_slug'       => 'ytrip-settings',
    'framework_title' => 'YTrip Options',
    'menu_type'       => 'menu',
    'menu_icon'       => 'dashicons-airplane',
    'menu_position'   => 25,
    'show_bar_menu'   => true,
    'theme'           => 'light',
    'capability'      => 'edit_theme_options', // Changed from manage_options to broader capability
));

// Typography (Kept same)
CSF::createSection($prefix, array(
    'title'  => 'Typography | الخطوط',
    'icon'   => 'fa fa-font',
    'fields' => array(
        array(
            'id'           => 'body_typography',
            'type'         => 'typography',
            'title'        => 'Body Font | خط النصوص',
            'font_family'  => true,
            'font_weight'  => true,
            'font_size'    => true,
            'line_height'  => true,
            'default'      => array(
                'font-family'    => 'Inter',
                'font-weight'    => '400',
                'font-size'      => '16',
                'line-height'    => '1.6',
                'unit'           => 'px',
            ),
        ),
        array(
            'id'           => 'heading_typography',
            'type'         => 'typography',
            'title'        => 'Heading Font | خط العناوين',
            'default'      => array(
                'font-family'  => 'Poppins',
                'font-weight'  => '600',
            ),
        ),
    ),
));

// Colors (UPDATED with color_group)
CSF::createSection($prefix, array(
    'title'  => 'Colors | الألوان',
    'icon'   => 'fa fa-palette',
    'fields' => array(
        
        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => 'Customize your brand colors. These will be applied globally across the site.',
        ),

        array(
            'id'      => 'brand_colors',
            'type'    => 'color_group',
            'title'   => 'Brand Palette | لوحة ألوان العلامة التجارية',
            'options' => array(
                'primary'   => 'Primary (Main Actions)',
                'secondary' => 'Secondary (Highlights)',
                'accent'    => 'Accent (Call to Action)',
            ),
            'default' => array(
                'primary'   => '#0f4c81', // Classic Blue
                'secondary' => '#ff6b6b', // Coral
                'accent'    => '#f9a825', // Gold
            ),
        ),

        array(
            'id'      => 'base_colors',
            'type'    => 'color_group',
            'title'   => 'Base Colors | الألوان الأساسية',
            'options' => array(
                'background' => 'Page Background',
                'surface'    => 'Card Surface',
                'text'       => 'Body Text',
                'heading'    => 'Headings',
            ),
            'default' => array(
                'background' => '#f8fafc',
                'surface'    => '#ffffff',
                'text'       => '#1e293b',
                'heading'    => '#0f172a',
            ),
        ),

        // Color Presets Helper (Using Image Select for Visuals)
        array(
            'id'      => 'apply_preset',
            'type'    => 'image_select',
            'title'   => 'Quick Presets | قوالب جاهزة',
            'desc'    => 'Select a preset to auto-fill the colors above. (Save changes to apply).',
            'options' => array(
                'ocean'   => YTRIP_URL . 'assets/images/presets/preset-1.jpg',
                'desert'  => YTRIP_URL . 'assets/images/presets/preset-2.jpg',
                'forest'  => YTRIP_URL . 'assets/images/presets/preset-3.jpg',
                'luxury'  => YTRIP_URL . 'assets/images/presets/preset-5.jpg',
                'urban'   => YTRIP_URL . 'assets/images/presets/preset-6.jpg',
            ),
            'default' => 'ocean',
        ),

    ),
));

// Spacing (Kept same)
CSF::createSection($prefix, array(
    'title'  => 'Spacing | المسافات',
    'icon'   => 'fa fa-arrows-alt',
    'fields' => array(
        array(
            'id'      => 'section_spacing',
            'type'    => 'slider',
            'title'   => 'Section Spacing',
            'min'     => 40, 'max' => 120, 'default' => 80,
        ),
        array(
            'id'      => 'border_radius',
            'type'    => 'slider',
            'title'   => 'Border Radius',
            'min'     => 0, 'max' => 50, 'default' => 12,
        ),
    ),
));

// SEO Settings
CSF::createSection($prefix, array(
    'title'  => 'SEO Schema',
    'icon'   => 'fa fa-google',
    'fields' => array(
        array(
            'id'      => 'schema_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Schema',
            'default' => true,
        ),
        array(
            'id'         => 'schema_conflict_mode',
            'type'       => 'switcher',
            'title'      => 'Conflict Mode',
            'default'    => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
    ),
));

// Performance Settings
CSF::createSection($prefix, array(
    'title'  => 'Performance',
    'icon'   => 'fa fa-rocket',
    'fields' => array(
        array(
            'id'      => 'enable_db_maintenance',
            'type'    => 'switcher',
            'title'   => 'DB Maintenance',
            'default' => true,
        ),
        array(
            'id'      => 'enable_lazy_load',
            'type'    => 'switcher',
            'title'   => 'Lazy Load',
            'default' => true,
        ),
        array(
            'type'    => 'callback',
            'function' => 'ytrip_clear_cache_button',
        ),
    ),
));
