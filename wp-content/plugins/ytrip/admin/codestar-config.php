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
    'capability'      => 'manage_options',
));

// Typography
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
            'letter_spacing' => true,
            'text_transform' => false,
            'color'        => false,
            'google_fonts' => true,
            'default'      => array(
                'font-family'    => 'Inter',
                'font-weight'    => '400',
                'font-size'      => '16',
                'line-height'    => '1.6',
                'letter-spacing' => '0',
                'unit'           => 'px',
            ),
            'desc'         => 'اختر نوع وحجم الخط المستخدم للنصوص العامة في الموقع. (Choose the font for general body text).',
        ),
        array(
            'id'           => 'heading_typography',
            'type'         => 'typography',
            'title'        => 'Heading Font | خط العناوين',
            'google_fonts' => true,
            'default'      => array(
                'font-family'  => 'Poppins',
                'font-weight'  => '600',
            ),
            'desc'         => 'اختر نوع الخط المستخدم للعناوين الرئيسية (H1-H6). (Choose the font for headings).',
        ),
        array(
            'id'      => 'font_size_scale',
            'type'    => 'fieldset',
            'title'   => 'Font Scale | مقياس الخطوط',
            'desc'    => 'تحديد أحجام الخطوط للعناوين المختلفة للحفاظ على التناسق. (Define font sizes for different headings).',
            'fields'  => array(
                array(
                    'id'      => 'h1_size',
                    'type'    => 'number',
                    'title'   => 'H1 Size',
                    'default' => 48,
                    'unit'    => 'px',
                    'desc'    => 'حجم العنوان الرئيسي H1 بالبكسل.',
                ),
                array(
                    'id'      => 'h2_size',
                    'type'    => 'number',
                    'title'   => 'H2 Size',
                    'default' => 36,
                    'unit'    => 'px',
                    'desc'    => 'حجم العنوان الثانوي H2 بالبكسل.',
                ),
                array(
                    'id'      => 'h3_size',
                    'type'    => 'number',
                    'title'   => 'H3 Size',
                    'default' => 28,
                    'unit'    => 'px',
                    'desc'    => 'حجم العنوان الفرعي H3 بالبكسل.',
                ),
            ),
        ),
    ),
));

// Colors
CSF::createSection($prefix, array(
    'title'  => 'Colors | الألوان',
    'icon'   => 'fa fa-palette',
    'fields' => array(
        array(
            'id'      => 'color_preset',
            'type'    => 'select', // Changed to select for now as images might be missing
            'title'   => 'Choose Color Preset | اختر نظام الألوان',
            'desc'    => 'اختر واحداً من أنظمة الألوان الجاهزة والمتناسقة لتطبيقها على الموقع بالكامل. (Select a pre-defined color scheme).',
            'options' => array(
                'ocean_breeze'      => 'Ocean Breeze (Blue/Sky)',
                'desert_safari'     => 'Desert Safari (Amber/Emerald)',
                'forest_hike'       => 'Forest Hike (Green/Lime)',
                'sunset_beach'      => 'Sunset Beach (Pink/Violet)',
                'royal_luxury'      => 'Royal Luxury (Indigo/Gold)',
                'urban_explorer'    => 'Urban Explorer (Blue/Slate)',
                'tropical_island'   => 'Tropical Island (Teal/Orange)',
                'mountain_peak'     => 'Mountain Peak (Gray/Blue)',
                'cultural_heritage' => 'Cultural Heritage (Rose/Amber)',
                'northern_lights'   => 'Northern Lights (Violet/Cyan)',
                'custom'            => 'Custom Colors (Manual)',
            ),
            'default' => 'ocean_breeze',
        ),
        
        // Show custom color pickers always (override preset)
        array(
            'id'         => 'custom_colors',
            'type'       => 'fieldset',
            'title'      => 'Color Overrides | تخصيص الألوان',
            'desc'       => 'تعديل الألوان يدوياً (يغطي على الإعدادات المسبقة). (Override preset colors manually).',
            'fields'     => array(
                array(
                    'id'      => 'primary_color',
                    'type'    => 'color',
                    'title'   => 'Primary | اللون الأساسي',
                    'desc'    => 'اللون الرئيسي للأزرار والروابط والعناصر النشطة.',
                ),
                array(
                    'id'      => 'secondary_color',
                    'type'    => 'color',
                    'title'   => 'Secondary | اللون الثانوي',
                    'desc'    => 'اللون الثانوي للعناصر الفرعية.',
                ),
                 array(
                    'id'      => 'accent_color',
                    'type'    => 'color',
                    'title'   => 'Accent | لون التمييز',
                    'desc'    => 'لون يُستخدم لإبراز التنبيهات أو العروض الخاصة.',
                ),
                 array(
                    'id'      => 'bg_color',
                    'type'    => 'color',
                    'title'   => 'Background',
                    'desc'    => 'لون خلفية الموقع العام.',
                ),
                array(
                    'id'      => 'text_color',
                    'type'    => 'color',
                    'title'   => 'Text',
                    'desc'    => 'اللون الأساسي للنصوص.',
                ),
            ),
        ),
    ),
));

// Spacing & Shadows
CSF::createSection($prefix, array(
    'title'  => 'Spacing | المسافات',
    'icon'   => 'fa fa-arrows-alt',
    'fields' => array(
        array(
            'id'      => 'section_spacing',
            'type'    => 'slider',
            'title'   => 'Section Spacing | المسافة بين الأقسام',
            'min'     => 40,
            'max'     => 120,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 80,
            'desc'    => 'المسافة الرأسية الفاصلة بين أقسام الصفحة الرئيسية. (Vertical spacing between page sections).',
        ),
        array(
            'id'      => 'border_radius',
            'type'    => 'slider',
            'title'   => 'Border Radius | استدارة الحواف',
            'min'     => 0,
            'max'     => 50,
            'step'    => 2,
            'unit'    => 'px',
            'default' => 12,
            'desc'    => 'مقدار انحناء زوايا البطاقات والصور. (Corner rounding for cards and images).',
        ),
    ),
));

// SEO Settings
CSF::createSection($prefix, array(
    'title'  => 'SEO Schema | تحسين المحركات',
    'icon'   => 'fa fa-google',
    'fields' => array(
        array(
            'id'      => 'schema_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Schema Output | تفعيل المخططات',
            'default' => true,
            'desc'    => 'تفعيل إخراج بيانات Schema.org المهيكلة. (Enable Schema.org structured data output).',
        ),
        array(
            'id'      => 'schema_conflict_mode',
            'type'    => 'switcher',
            'title'   => 'Smart Conflict Prevention | منع التعارض الذكي',
            'default' => true,
            'desc'    => 'تعطيل المخططات العامة تلقائياً إذا تم اكتشاف Yoast أو RankMath. (Disable generic schemas if Yoast/RankMath is detected).',
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_organization',
            'type'    => 'switcher',
            'title'   => 'Organization Schema | مخطط المؤسسة',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_website',
            'type'    => 'switcher',
            'title'   => 'WebSite Schema | مخطط الموقع',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_product',
            'type'    => 'switcher',
            'title'   => 'Product/Tour Schema | مخطط الرحلات',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_faq',
            'type'    => 'switcher',
            'title'   => 'FAQ Schema | الأسئلة الشائعة',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
        array(
            'id'      => 'schema_breadcrumb',
            'type'    => 'switcher',
            'title'   => 'Breadcrumb Schema | مسار التنقل',
            'default' => true,
            'dependency' => array('schema_enable', '==', 'true'),
        ),
    ),
));

// Performance Settings
CSF::createSection($prefix, array(
    'title'  => 'Performance | الأداء',
    'icon'   => 'fa fa-rocket',
    'fields' => array(
        array(
            'id'      => 'enable_db_maintenance',
            'type'    => 'switcher',
            'title'   => 'Database Maintenance | صيانة القاعدة',
            'default' => true,
            'desc'    => 'تنظيف يومي لقاعدة البيانات من المخلفات. (Daily cleanup of transients).',
        ),
        array(
            'id'      => 'enable_lazy_load',
            'type'    => 'switcher',
            'title'   => 'Native Lazy Loading | التحميل الكسول',
            'default' => true,
            'desc'    => 'تفعيل خاصية loading="lazy" للصور. (Add loading="lazy" to images).',
        ),
        array(
            'id'      => 'enable_object_cache',
            'type'    => 'switcher',
            'title'   => 'Object Caching | تخزين مؤقت للكائنات',
            'default' => true,
            'desc'    => 'تخزين نتائج الاستعلامات المعقدة لتخفيف الحمل. (Cache complex queries).',
        ),
        array(
            'type'    => 'content',
            'content' => '<p><strong>Cache Management:</strong></p>',
        ),
        array(
            'type'    => 'callback',
            'function' => 'ytrip_clear_cache_button', // Defined in class-performance.php
        ),
    ),
));
