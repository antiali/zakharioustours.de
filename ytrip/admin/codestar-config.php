<?php
/**
 * YTrip Admin Options Configuration
 * 
 * Standard Codestar Framework implementation
 * @package YTrip
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if CSF is loaded
if ( ! class_exists( 'CSF' ) ) {
    return;
}

// Define unique prefix for options
$prefix = 'ytrip_settings';

// Create admin options page
CSF::createOptions( $prefix, array(
    'framework_title' => 'YTrip Settings <small>v1.0.0</small>',
    'menu_title'      => 'YTrip Settings',
    'menu_slug'       => 'ytrip-settings',
    'menu_type'       => 'menu',
    'menu_icon'       => 'dashicons-airplane',
    'menu_position'   => 25,
    'menu_capability'  => 'manage_options', // ✅ CORRECT: menu_capability
    'menu_hidden'     => false,
    
    // Show in admin bar
    'show_bar_menu'           => true,
    'admin_bar_menu_icon'     => 'dashicons-airplane',
    'admin_bar_menu_priority' => 50,
    
    // Show submenus
    'show_sub_menu'           => true,
    'show_in_network'         => true,
    'show_in_customizer'      => false,
    
    // Theme settings
    'theme'                  => 'light',
    'nav'                    => 'normal',
    
    // AJAX settings
    'ajax_save'              => true,
    'show_reset_all'          => true,
    'show_reset_section'      => true,
    'show_footer'             => true,
    'show_all_options'        => true,
    'sticky_header'           => true,
    'save_defaults'           => true,
    
    // Typography
    'enqueue_webfont'         => false,
    'async_webfont'           => false,
    
    // Other
    'show_search'             => true,
    'show_form_warning'       => true,
) );

// Section 1: Typography
CSF::createSection( $prefix, array(
    'title'  => 'Typography | الخطوط',
    'icon'   => 'fa fa-font',
    'fields' => array(
        array(
            'id'           => 'body_typography',
            'type'         => 'typography',
            'title'        => 'Body Font | خط النصوص',
            'desc'         => 'Configure the body typography settings.',
            'font_family'  => true,
            'font_weight'  => true,
            'font_size'    => true,
            'line_height'  => true,
            'font_style'   => true,
            'letter_spacing' => true,
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
            'desc'         => 'Configure the headings typography settings.',
            'font_family'  => true,
            'font_weight'  => true,
            'default'      => array(
                'font-family'  => 'Poppins',
                'font-weight'  => '600',
            ),
        ),
    ),
) );

// Section 2: Colors
CSF::createSection( $prefix, array(
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
            'desc'    => 'Define your main brand colors.',
            'options' => array(
                'primary'   => 'Primary (Main Actions)',
                'secondary' => 'Secondary (Highlights)',
                'accent'    => 'Accent (Call to Action)',
            ),
            'default' => array(
                'primary'   => '#0f4c81',
                'secondary' => '#ff6b6b',
                'accent'    => '#f9a825',
            ),
        ),
        array(
            'id'      => 'base_colors',
            'type'    => 'color_group',
            'title'   => 'Base Colors | الألوان الأساسية',
            'desc'    => 'Define your base UI colors.',
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
    ),
) );

// Section 3: Spacing & Layout
CSF::createSection( $prefix, array(
    'title'  => 'Layout | التخطيط',
    'icon'   => 'fa fa-columns',
    'fields' => array(
        array(
            'id'      => 'section_spacing',
            'type'    => 'slider',
            'title'   => 'Section Spacing',
            'desc'    => 'Adjust the spacing between sections.',
            'min'     => 40,
            'max'     => 120,
            'step'    => 5,
            'default' => 80,
            'unit'    => 'px',
        ),
        array(
            'id'      => 'border_radius',
            'type'    => 'slider',
            'title'   => 'Border Radius',
            'desc'    => 'Adjust the border radius for elements.',
            'min'     => 0,
            'max'     => 50,
            'step'    => 1,
            'default' => 12,
            'unit'    => 'px',
        ),
        array(
            'id'      => 'container_width',
            'type'    => 'slider',
            'title'   => 'Container Width',
            'desc'    => 'Maximum width for content containers.',
            'min'     => 800,
            'max'     => 1600,
            'step'    => 10,
            'default' => 1200,
            'unit'    => 'px',
        ),
    ),
) );

// Section 4: SEO
CSF::createSection( $prefix, array(
    'title'  => 'SEO | تحسين محركات البحث',
    'icon'   => 'fa fa-google',
    'fields' => array(
        array(
            'id'      => 'schema_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Schema Markup',
            'desc'    => 'Add structured data markup for better search visibility.',
            'default' => true,
        ),
        array(
            'id'      => 'schema_type',
            'type'    => 'select',
            'title'   => 'Schema Type',
            'desc'    => 'Choose the default schema type for tours.',
            'options' => array(
                'TravelAction' => 'Travel Action',
                'Trip'         => 'Trip',
                'Product'      => 'Product',
            ),
            'default' => 'TravelAction',
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_conflict_mode',
            'type'    => 'switcher',
            'title'   => 'Conflict Mode',
            'desc'    => 'Handle conflicts with other SEO plugins.',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
    ),
) );

// Section 5: Performance
CSF::createSection( $prefix, array(
    'title'  => 'Performance | الأداء',
    'icon'   => 'fa fa-rocket',
    'fields' => array(
        array(
            'id'      => 'enable_lazy_load',
            'type'    => 'switcher',
            'title'   => 'Lazy Load Images',
            'desc'    => 'Lazy load images for better page load times.',
            'default' => true,
        ),
        array(
            'id'      => 'enable_db_maintenance',
            'type'    => 'switcher',
            'title'   => 'Database Maintenance',
            'desc'    => 'Optimize database tables periodically.',
            'default' => true,
        ),
        array(
            'id'      => 'enable_minification',
            'type'    => 'switcher',
            'title'   => 'CSS/JS Minification',
            'desc'    => 'Minify CSS and JavaScript files.',
            'default' => false,
        ),
        array(
            'type'    => 'submessage',
            'style'   => 'success',
            'content' => '<strong>Performance Tips:</strong><br>- Use image optimization<br>- Enable caching<br>- Use CDN for assets',
        ),
    ),
) );

// Section 6: General Settings
CSF::createSection( $prefix, array(
    'title'  => 'General | الإعدادات العامة',
    'icon'   => 'fa fa-cog',
    'fields' => array(
        array(
            'id'      => 'currency',
            'type'    => 'select',
            'title'   => 'Currency | العملة',
            'desc'    => 'Select the default currency for bookings.',
            'options' => array(
                'USD' => 'US Dollar ($)',
                'EUR' => 'Euro (€)',
                'GBP' => 'British Pound (£)',
                'EGP' => 'Egyptian Pound (E£)',
                'AED' => 'UAE Dirham (د.إ)',
                'SAR' => 'Saudi Riyal (ر.س)',
            ),
            'default' => 'EUR',
        ),
        array(
            'id'      => 'date_format',
            'type'    => 'select',
            'title'   => 'Date Format',
            'desc'    => 'Select how dates are displayed.',
            'options' => array(
                'd/m/Y' => 'DD/MM/YYYY',
                'm/d/Y' => 'MM/DD/YYYY',
                'Y-m-d' => 'YYYY-MM-DD',
            ),
            'default' => 'd/m/Y',
        ),
        array(
            'id'      => 'language',
            'type'    => 'select',
            'title'   => 'Language | اللغة',
            'desc'    => 'Select the plugin language.',
            'options' => array(
                'en' => 'English',
                'ar' => 'العربية (Arabic)',
                'de' => 'Deutsch (German)',
            ),
            'default' => 'en',
        ),
    ),
) );
