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
            'google_fonts' => true, // Enable Google Fonts
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
            'type'    => 'image_select',
            'title'   => 'Choose Color Preset | اختر نظام الألوان',
            'desc'    => 'اختر واحداً من أنظمة الألوان الجاهزة والمتناسقة لتطبيقها على الموقع بالكامل. (Select a pre-defined color scheme).',
            'options' => array(
                'preset_1'  => YTRIP_URL . 'assets/images/presets/preset-1.jpg',
                'preset_2'  => YTRIP_URL . 'assets/images/presets/preset-2.jpg',
                'preset_3'  => YTRIP_URL . 'assets/images/presets/preset-3.jpg',
                'preset_4'  => YTRIP_URL . 'assets/images/presets/preset-4.jpg',
                'preset_5'  => YTRIP_URL . 'assets/images/presets/preset-5.jpg',
                'preset_6'  => YTRIP_URL . 'assets/images/presets/preset-6.jpg',
                'preset_7'  => YTRIP_URL . 'assets/images/presets/preset-7.jpg',
                'preset_8'  => YTRIP_URL . 'assets/images/presets/preset-8.jpg',
                'preset_9'  => YTRIP_URL . 'assets/images/presets/preset-9.jpg',
                'preset_10' => YTRIP_URL . 'assets/images/presets/preset-10.jpg',
                'custom'    => YTRIP_URL . 'assets/images/presets/custom.jpg',
            ),
            'default' => 'preset_1',
        ),
        
        // Show custom color pickers only if "custom" selected
        array(
            'id'         => 'custom_colors',
            'type'       => 'fieldset',
            'title'      => 'Custom Colors | ألوان مخصصة',
            'desc'       => 'تخصيص الألوان يدوياً عند اختيار "Custom" من القائمة أعلاه. (Customize colors manually when "Custom" is selected).',
            'dependency' => array('color_preset', '==', 'custom'),
            'fields'     => array(
                array(
                    'id'      => 'primary',
                    'type'    => 'color',
                    'title'   => 'Primary | اللون الأساسي',
                    'default' => '#2563eb',
                    'desc'    => 'اللون الرئيسي للأزرار والروابط والعناصر النشطة.',
                ),
                array(
                    'id'      => 'secondary',
                    'type'    => 'color',
                    'title'   => 'Secondary | اللون الثانوي',
                    'default' => '#7c3aed',
                    'desc'    => 'اللون الثانوي للعناصر الفرعية.',
                ),
                 array(
                    'id'      => 'accent',
                    'type'    => 'color',
                    'title'   => 'Accent | لون التمييز',
                    'default' => '#f59e0b',
                    'desc'    => 'لون يُستخدم لإبراز التنبيهات أو العروض الخاصة.',
                ),
                 array(
                    'id'      => 'background',
                    'type'    => 'color',
                    'title'   => 'Background',
                    'default' => '#ffffff',
                    'desc'    => 'لون خلفية الموقع العام.',
                ),
                array(
                    'id'      => 'surface',
                    'type'    => 'color',
                    'title'   => 'Surface',
                    'default' => '#f9fafb',
                    'desc'    => 'لون خلفية الأقسام الداخلية والبطاقات.',
                ),
                array(
                    'id'      => 'text',
                    'type'    => 'color',
                    'title'   => 'Text',
                    'default' => '#111827',
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
            'id'      => 'spacing_unit',
            'type'    => 'number',
            'title'   => 'Base Spacing Unit | وحدة المسافة الأساسية',
            'default' => 8,
            'unit'    => 'px',
            'desc'    => 'الوحدة الأساسية التي ستُبنى عليها جميع المسافات في التصميم (مضاعفات هذا الرقم). (Base unit for all spacing calculations).',
        ),
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
            'id'      => 'card_padding',
            'type'    => 'spacing',
            'title'   => 'Card Padding | حشوة البطاقة',
            'default' => array(
                'top'    => '24',
                'right'  => '24',
                'bottom' => '24',
                'left'   => '24',
                'unit'   => 'px',
            ),
            'desc'    => 'المساحة الداخلية (الحشوة) داخل بطاقات الرحلات والمحتوى. (Internal padding for cards).',
        ),
        array(
            'id'      => 'container_width',
            'type'    => 'slider',
            'title'   => 'Container Max Width | أقصى عرض',
            'min'     => 1140,
            'max'     => 1920,
            'step'    => 20,
            'unit'    => 'px',
            'default' => 1200,
            'desc'    => 'أقصى عرض للمحتوى في وسط الصفحة. (Maximum width of the content container).',
        ),
    ),
));

CSF::createSection($prefix, array(
    'title'  => 'Shadows & Effects | الظلال',
    'icon'   => 'fa fa-magic',
    'fields' => array(
        array(
            'id'      => 'card_shadow',
            'type'    => 'select',
            'title'   => 'Card Shadow | ظل البطاقة',
            'options' => array(
                'none'   => 'None | بدون',
                'sm'     => 'Small | صغير',
                'md'     => 'Medium | متوسط',
                'lg'     => 'Large | كبير',
                'xl'     => 'Extra Large | كبير جداً',
                'custom' => 'Custom | مخصص',
            ),
            'default' => 'md',
            'desc'    => 'حجم الظل للبطاقات والعناصر العائمة. (Shadow depth for cards).',
        ),
        array(
            'id'         => 'custom_shadow',
            'type'       => 'text',
            'title'      => 'Custom Shadow CSS',
            'default'    => '0 4px 6px rgba(0,0,0,0.1)',
            'dependency' => array('card_shadow', '==', 'custom'),
            'desc'    => 'قيمة CSS للظل المخصص (مثال: 0 4px 6px rgba(0,0,0,0.1)).',
        ),
        array(
            'id'      => 'card_hover_shadow',
            'type'    => 'select',
            'title'   => 'Card Hover Shadow',
            'options' => array(
                'none' => 'None',
                'sm'   => 'Small',
                'md'   => 'Medium',
                'lg'   => 'Large',
                'xl'   => 'Extra Large',
            ),
            'default' => 'lg',
            'desc'    => 'تأثير الظل عند مرور الماوس على البطاقة. (Shadow effect on hover).',
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
        array(
            'id'      => 'button_radius',
            'type'    => 'slider',
            'title'   => 'Button Radius | استدارة الأزرار',
            'min'     => 0,
            'max'     => 50,
            'step'    => 2,
            'unit'    => 'px',
            'default' => 8,
            'desc'    => 'مقدار انحناء زوايا الأزرار. (Corner rounding for buttons).',
        ),
        array(
            'id'      => 'transitions',
            'type'    => 'switcher',
            'title'   => 'Enable Smooth Transitions | تفعيل الحركات',
            'default' => true,
            'desc'    => 'تفعيل حركات الانتقال السلسة عند التفاعل مع العناصر. (Enable smooth visual transitions).',
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
            'desc'    => 'تعطيل المخططات العامة (مثل فتات الخبز، الموقع) تلقائياً إذا تم اكتشاف Yoast أو RankMath لمنع التكرار. (Disable generic schemas automatically if Yoast/RankMath is detected).',
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
    ),
));

// Single Tour Layout
CSF::createSection($prefix, array(
    'title'  => 'Tour Layout | تصميم الرحلة',
    'icon'   => 'fa fa-file',
    'fields' => array(
        array(
            'id'      => 'single_tour_layout',
            'type'    => 'image_select',
            'title'   => 'Single Tour Layout | تصميم صفحة الرحلة',
            'desc'    => 'اختر تصميم صفحة الرحلة المفردة. (Choose the single tour page layout).',
            'options' => array(
                'layout_1' => YTRIP_URL . 'assets/images/single-layouts/layout-1.png',
                'layout_2' => YTRIP_URL . 'assets/images/single-layouts/layout-2.png',
                'layout_3' => YTRIP_URL . 'assets/images/single-layouts/layout-3.png',
                'layout_4' => YTRIP_URL . 'assets/images/single-layouts/layout-4.png',
                'layout_5' => YTRIP_URL . 'assets/images/single-layouts/layout-5.png',
            ),
            'default' => 'layout_1',
        ),
        array(
            'id'      => 'single_sidebar_position',
            'type'    => 'button_set',
            'title'   => 'Sidebar Position | موقع الشريط الجانبي',
            'options' => array(
                'right' => 'Right | يمين',
                'left'  => 'Left | يسار',
            ),
            'default' => 'right',
            'dependency' => array('single_tour_layout', 'any', 'layout_1,layout_4'),
        ),
        array(
            'id'      => 'single_sticky_booking',
            'type'    => 'switcher',
            'title'   => 'Sticky Booking Sidebar | شريط الحجز الثابت',
            'default' => true,
            'desc'    => 'تثبيت نموذج الحجز عند التمرير. (Keep booking form visible while scrolling).',
        ),
        array(
            'id'      => 'single_show_breadcrumb',
            'type'    => 'switcher',
            'title'   => 'Show Breadcrumb | عرض مسار التنقل',
            'default' => true,
        ),
        array(
            'id'      => 'single_gallery_style',
            'type'    => 'select',
            'title'   => 'Gallery Style | نمط المعرض',
            'options' => array(
                'slider'    => 'Slider | منزلق',
                'grid'      => 'Grid | شبكة',
                'masonry'   => 'Masonry | طوب',
                'lightbox'  => 'Lightbox | صندوق ضوئي',
            ),
            'default' => 'slider',
        ),
    ),
));

// Card Styles
CSF::createSection($prefix, array(
    'title'  => esc_html__( 'Card Styles', 'ytrip' ),
    'icon'   => 'fa fa-th-large',
    'fields' => array(
        array(
            'id'      => 'tour_card_style',
            'type'    => 'image_select',
            'title'   => esc_html__( 'Default Card Style', 'ytrip' ),
            'desc'    => esc_html__( 'Choose default tour card style. Can be customized per section.', 'ytrip' ),
            'options' => array(
                'style_1'  => YTRIP_URL . 'assets/images/card-styles/overlay-gradient.png',
                'style_2'  => YTRIP_URL . 'assets/images/card-styles/classic-white.png',
                'style_3'  => YTRIP_URL . 'assets/images/card-styles/modern-shadow.png',
                'style_4'  => YTRIP_URL . 'assets/images/card-styles/minimal-border.png',
                'style_5'  => YTRIP_URL . 'assets/images/card-styles/glassmorphism.png',
                'style_6'  => YTRIP_URL . 'assets/images/card-styles/hover-zoom.png',
                'style_7'  => YTRIP_URL . 'assets/images/card-styles/split-content.png',
                'style_8'  => YTRIP_URL . 'assets/images/card-styles/badge-corner.png',
                'style_9'  => YTRIP_URL . 'assets/images/card-styles/horizontal.png',
                'style_10' => YTRIP_URL . 'assets/images/card-styles/compact-grid.png',
            ),
            'default' => 'style_1',
        ),
        array(
            'id'      => 'card_hover_effect',
            'type'    => 'select',
            'title'   => esc_html__( 'Card Hover Effect', 'ytrip' ),
            'options' => array(
                'none'   => esc_html__( 'None', 'ytrip' ),
                'lift'   => esc_html__( 'Lift Up', 'ytrip' ),
                'zoom'   => esc_html__( 'Image Zoom', 'ytrip' ),
                'tilt'   => esc_html__( '3D Tilt', 'ytrip' ),
                'glow'   => esc_html__( 'Glow', 'ytrip' ),
                'slide'  => esc_html__( 'Slide Content', 'ytrip' ),
                'fade'   => esc_html__( 'Fade Overlay', 'ytrip' ),
                'scale'  => esc_html__( 'Scale', 'ytrip' ),
            ),
            'default' => 'lift',
        ),
        array(
            'id'      => 'card_image_ratio',
            'type'    => 'select',
            'title'   => esc_html__( 'Image Aspect Ratio', 'ytrip' ),
            'options' => array(
                '16-9' => esc_html__( '16:9 (Landscape)', 'ytrip' ),
                '4-3'  => esc_html__( '4:3 (Standard)', 'ytrip' ),
                '3-2'  => esc_html__( '3:2 (Classic)', 'ytrip' ),
                '1-1'  => esc_html__( '1:1 (Square)', 'ytrip' ),
            ),
            'default' => '4-3',
        ),
        array(
            'id'      => 'card_show_rating',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Rating', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'card_show_wishlist',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Wishlist Button', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'card_show_duration',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Duration', 'ytrip' ),
            'default' => true,
        ),
    ),
));

// Design System
CSF::createSection($prefix, array(
    'title'  => 'Design System | نظام التصميم',
    'icon'   => 'fa fa-paint-brush',
    'fields' => array(
        array(
            'id'      => 'enable_animations',
            'type'    => 'switcher',
            'title'   => 'Enable Scroll Animations | تفعيل الحركات',
            'default' => true,
            'desc'    => 'تفعيل الرسوم المتحركة عند التمرير. (Enable scroll-triggered animations).',
        ),
        array(
            'id'         => 'animation_style',
            'type'       => 'select',
            'title'      => 'Animation Style | نوع الحركة',
            'options'    => array(
                'fade'        => 'Fade In | ظهور تدريجي',
                'slide-up'    => 'Slide Up | انزلاق للأعلى',
                'slide-right' => 'Slide Right | انزلاق لليمين',
                'zoom'        => 'Zoom In | تكبير',
                'flip'        => 'Flip | قلب',
                'bounce'      => 'Bounce | ارتداد',
            ),
            'default'    => 'fade',
            'dependency' => array('enable_animations', '==', true),
        ),
        array(
            'id'      => 'enable_parallax',
            'type'    => 'switcher',
            'title'   => 'Enable Parallax Effects | تفعيل البارالاكس',
            'default' => true,
        ),
        array(
            'id'      => 'enable_lazy_load',
            'type'    => 'switcher',
            'title'   => 'Enable Lazy Loading | التحميل الكسول',
            'default' => true,
        ),
        array(
            'id'      => 'enable_smooth_scroll',
            'type'    => 'switcher',
            'title'   => 'Enable Smooth Scroll | التمرير السلس',
            'default' => true,
        ),
        array(
            'id'      => 'enable_microinteractions',
            'type'    => 'switcher',
            'title'   => 'Enable Microinteractions | التفاعلات الدقيقة',
            'default' => true,
            'desc'    => 'تأثيرات تفاعلية للأزرار والكروت. (Button ripples and card hover effects).',
        ),
        array(
            'id'      => 'button_hover_style',
            'type'    => 'select',
            'title'   => 'Button Hover Style | نمط الأزرار',
            'options' => array(
                'grow'     => 'Grow | تكبير',
                'shrink'   => 'Shrink | تصغير',
                'glow'     => 'Glow | توهج',
                'slide'    => 'Slide Background | انزلاق',
                'ripple'   => 'Ripple Effect | موجة',
                'gradient' => 'Gradient Shift | تدرج',
            ),
            'default' => 'grow',
        ),
        array(
            'id'      => 'use_8px_grid',
            'type'    => 'switcher',
            'title'   => 'Use 8px Grid System | شبكة 8 بكسل',
            'default' => true,
            'desc'    => 'جميع المسافات بمضاعفات 8 بكسل. (All spacing in multiples of 8px).',
        ),
        array(
            'id'      => 'typography_scale',
            'type'    => 'select',
            'title'   => 'Typography Scale | مقياس الخطوط',
            'options' => array(
                'minor-third'    => 'Minor Third (1.2)',
                'major-third'    => 'Major Third (1.25)',
                'perfect-fourth' => 'Perfect Fourth (1.333)',
                'golden-ratio'   => 'Golden Ratio (1.618)',
            ),
            'default' => 'major-third',
        ),
        array(
            'id'      => 'image_overlay_opacity',
            'type'    => 'slider',
            'title'   => 'Image Overlay Opacity | شفافية طبقة الصورة',
            'min'     => 0,
            'max'     => 80,
            'step'    => 5,
            'unit'    => '%',
            'default' => 40,
        ),
    ),
));

// Performance Settings
CSF::createSection($prefix, array(
    'title'  => 'Performance | الأداء',
    'icon'   => 'fa fa-rocket',
    'fields' => array(
        
        array(
            'id'    => 'performance_general_tab',
            'type'  => 'tab',
            'title' => 'General',
        ),

        array(
            'id'      => 'enable_skeleton_loading',
            'type'    => 'switcher',
            'title'   => 'Enable Skeleton Loading | التحميل الهيكلي',
            'default' => true,
            'desc'    => 'Show animated skeleton while content loads. (عرض هيكل التحميل أثناء جلب النتائج).',
        ),

        array(
            'id'         => 'skeleton_style',
            'type'       => 'select',
            'title'      => 'Skeleton Style | نمط الهيكل',
            'options'    => array(
                'shimmer' => 'Shimmer (Default)',
                'pulse'   => 'Pulse',
                'wave'    => 'Wave',
            ),
            'default'    => 'shimmer',
            'dependency' => array('enable_skeleton_loading', '==', 'true'),
        ),

        array(
            'id'    => 'performance_db_tab',
            'type'  => 'tab',
            'title' => 'Database',
        ),

        array(
            'id'      => 'enable_db_indexes',
            'type'    => 'switcher',
            'title'   => 'Optimize Database Indexes',
            'default' => true,
            'desc'    => 'Add custom indexes to wp_postmeta for faster lookups.',
        ),

        array(
            'id'      => 'enable_transient_cleaner',
            'type'    => 'switcher',
            'title'   => 'Auto-Clean Transients',
            'default' => true,
            'desc'    => 'Automatically remove expired transients to keep DB size low.',
        ),

        array(
            'id'    => 'performance_assets_tab',
            'type'  => 'tab',
            'title' => 'Assets',
        ),

        array(
            'id'      => 'enable_minification',
            'type'    => 'switcher',
            'title'   => 'Minify CSS/JS',
            'default' => false,
            'desc'    => 'Use minified versions of assets (requires build process).',
        ),

        array(
            'id'      => 'defer_js',
            'type'    => 'switcher',
            'title'   => 'Defer JavaScript',
            'default' => true,
            'desc'    => 'Add "defer" attribute to non-critical JS files.',
        ),

        array(
            'id'    => 'performance_images_tab',
            'type'  => 'tab',
            'title' => 'Images',
        ),

        array(
            'id'      => 'enable_lazy_load',
            'type'    => 'switcher',
            'title'   => 'Native Lazy Loading',
            'default' => true,
            'desc'    => 'Add loading="lazy" to all images.',
        ),

        array(
            'id'      => 'enable_webp',
            'type'    => 'switcher',
            'title'   => 'WebP Generation',
            'default' => false,
            'desc'    => 'Automatically convert uploaded images to WebP format.',
        ),

        array(
            'id'    => 'performance_cache_tab',
            'type'  => 'tab',
            'title' => 'Caching',
        ),

        array(
            'id'      => 'enable_object_cache',
            'type'    => 'switcher',
            'title'   => 'Object Caching (Transients)',
            'default' => true,
            'desc'    => 'Cache complex query results to reduce Database load.',
        ),

        array(
            'type'    => 'notice',
            'style'   => 'info',
            'content' => 'Click the button below to clear all YTrip caches.',
        ),

        array(
            'type'    => 'callback',
            'function' => 'ytrip_clear_cache_button',
        ),

    ),
));

// Archive Settings (Restored without skeleton)
CSF::createSection($prefix, array(
    'title'  => 'Archive Settings | إعدادات الأرشيف',
    'icon'   => 'fa fa-list',
    'fields' => array(
        array(
            'id'      => 'archive_default_view',
            'type'    => 'button_set',
            'title'   => 'Default View Mode | العرض الافتراضي',
            'options' => array(
                'grid' => 'Grid | شبكة',
                'list' => 'List | قائمة',
            ),
            'default' => 'grid',
            'desc'    => 'اختر طريقة العرض الافتراضية للرحلات. (Default display mode for tours).',
        ),
        array(
            'id'      => 'archive_default_columns',
            'type'    => 'slider',
            'title'   => 'Default Columns | الأعمدة الافتراضية',
            'min'     => 2,
            'max'     => 5,
            'step'    => 1,
            'default' => 3,
            'desc'    => 'عدد الأعمدة في وضع الشبكة. (Number of columns in grid view).',
        ),
        array(
            'id'      => 'archive_per_page',
            'type'    => 'slider',
            'title'   => 'Tours Per Page | الرحلات لكل صفحة',
            'min'     => 6,
            'max'     => 48,
            'step'    => 3,
            'default' => 12,
        ),
        array(
            'id'      => 'archive_show_filters',
            'type'    => 'switcher',
            'title'   => 'Show Filters | إظهار الفلاتر',
            'default' => true,
        ),
        array(
            'id'         => 'archive_filter_position',
            'type'       => 'select',
            'title'      => 'Filter Position | موقع الفلاتر',
            'options'    => array(
                'sidebar' => 'Sidebar | شريط جانبي',
                'topbar'  => 'Top Bar | شريط علوي',
            ),
            'default'    => 'sidebar',
            'dependency' => array('archive_show_filters', '==', 'true'),
        ),
        array(
            'id'      => 'archive_enable_ajax',
            'type'    => 'switcher',
            'title'   => 'Enable AJAX Filtering | فلترة AJAX',
            'default' => true,
        ),
        array(
            'id'      => 'archive_enable_sorting',
            'type'    => 'switcher',
            'title'   => 'Enable Sorting | تفعيل الترتيب',
            'default' => true,
        ),
        array(
            'id'      => 'archive_enable_price_filter',
            'type'    => 'switcher',
            'title'   => 'Enable Price Filter | فلتر السعر',
            'default' => true,
        ),
        array(
            'id'      => 'archive_enable_duration_filter',
            'type'    => 'switcher',
            'title'   => 'Enable Duration Filter | فلتر المدة',
            'default' => true,
        ),
        array(
            'id'      => 'archive_pagination_style',
            'type'    => 'button_set',
            'title'   => 'Pagination Style | نوع التقسيم',
            'options' => array(
                'numbered'  => 'Numbered | ترقيم',
                'loadmore'  => 'Load More Button | زر المزيد',
                'infinite'  => 'Infinite Scroll | تمرير لانهائي',
            ),
            'default' => 'numbered',
            'desc'    => 'اختر طريقة تحميل المزيد من الرحلات. جميع الخيارات تعمل بـ AJAX وتحدث عنوان URL. (Choose pagination method. All options use AJAX and update URL).',
        ),
    ),
));

// General Settings
CSF::createSection($prefix, array(
    'title'  => 'General Settings | الإعدادات العامة',
    'icon'   => 'fa fa-cog',
    'fields' => array(
        array(
            'id'    => 'site_logo',
            'type'  => 'media',
            'title' => 'Site Logo | شعار الموقع',
        ),
        array(
            'id'    => 'site_logo_dark',
            'type'  => 'media',
            'title' => 'Dark Mode Logo | شعار الوضع الداكن',
        ),
        array(
            'id'      => 'enable_preloader',
            'type'    => 'switcher',
            'title'   => 'Enable Preloader | محمّل الصفحة',
            'default' => false,
        ),
        array(
            'id'    => 'default_term_image',
            'type'  => 'media',
            'title' => 'Default Term Image | صورة التصنيف الافتراضية',
            'desc'  => 'Fallback image if a specific term image is not set.',
        ),
        array(
            'id'    => 'default_term_background',
            'type'  => 'media',
            'title' => 'Default Term Header | خلفية التصنيف الافتراضية',
            'desc'  => 'Fallback background if a specific term header is not set.',
        ),
        array(
            'id'         => 'preloader_style',
            'type'       => 'select',
            'title'      => 'Preloader Style | نمط المحمّل',
            'options'    => array(
                'spinner' => 'Spinner | دوار',
                'dots'    => 'Dots | نقاط',
                'logo'    => 'Logo Fade | تلاشي الشعار',
            ),
            'default'    => 'spinner',
            'dependency' => array('enable_preloader', '==', 'true'),
        ),
        array(
            'id'      => 'enable_back_to_top',
            'type'    => 'switcher',
            'title'   => 'Back to Top Button | زر العودة للأعلى',
            'default' => true,
        ),
        array(
            'id'      => 'enable_sticky_header',
            'type'    => 'switcher',
            'title'   => 'Sticky Header | هيدر ثابت',
            'default' => true,
        ),
        array(
            'id'      => 'enable_dark_mode',
            'type'    => 'switcher',
            'title'   => 'Enable Dark Mode Toggle | الوضع الداكن',
            'default' => false,
        ),
        array(
            'id'      => 'maintenance_mode',
            'type'    => 'switcher',
            'title'   => 'Maintenance Mode | وضع الصيانة',
            'default' => false,
        ),
        array(
            'id'       => 'custom_css',
            'type'     => 'code_editor',
            'title'    => 'Custom CSS | CSS مخصص',
            'settings' => array('theme' => 'mbo', 'mode' => 'css'),
        ),
        array(
            'id'       => 'custom_js',
            'type'     => 'code_editor',
            'title'    => 'Custom JavaScript | JS مخصص',
            'settings' => array('theme' => 'mbo', 'mode' => 'javascript'),
        ),
    ),
));

// Header Settings
CSF::createSection($prefix, array(
    'title'  => 'Header Settings | إعدادات الهيدر',
    'icon'   => 'fa fa-window-maximize',
    'fields' => array(
        array(
            'id'      => 'header_style',
            'type'    => 'image_select',
            'title'   => 'Header Style | نمط الهيدر',
            'options' => array(
                'style_1' => YTRIP_URL . 'assets/images/header-styles/style-1.png',
                'style_2' => YTRIP_URL . 'assets/images/header-styles/style-2.png',
                'style_3' => YTRIP_URL . 'assets/images/header-styles/style-3.png',
            ),
            'default' => 'style_1',
        ),
        array(
            'id'      => 'header_bg_color',
            'type'    => 'color',
            'title'   => 'Header Background | خلفية الهيدر',
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'header_show_phone',
            'type'    => 'switcher',
            'title'   => 'Show Phone Number | إظهار الهاتف',
            'default' => true,
        ),
        array(
            'id'         => 'header_phone',
            'type'       => 'text',
            'title'      => 'Phone Number | رقم الهاتف',
            'default'    => '+1 234 567 890',
            'dependency' => array('header_show_phone', '==', 'true'),
        ),
        array(
            'id'      => 'header_show_cta',
            'type'    => 'switcher',
            'title'   => 'Show CTA Button | زر الإجراء',
            'default' => true,
        ),
        array(
            'id'         => 'header_cta_text',
            'type'       => 'text',
            'title'      => 'CTA Button Text | نص الزر',
            'default'    => 'Book Now',
            'dependency' => array('header_show_cta', '==', 'true'),
        ),
    ),
));

// Footer Settings
CSF::createSection($prefix, array(
    'title'  => 'Footer Settings | إعدادات الفوتر',
    'icon'   => 'fa fa-window-minimize',
    'fields' => array(
        array(
            'id'      => 'footer_style',
            'type'    => 'image_select',
            'title'   => 'Footer Style | نمط الفوتر',
            'options' => array(
                'style_1' => YTRIP_URL . 'assets/images/footer-styles/style-1.png',
                'style_2' => YTRIP_URL . 'assets/images/footer-styles/style-2.png',
            ),
            'default' => 'style_1',
        ),
        array(
            'id'      => 'footer_bg_color',
            'type'    => 'color',
            'title'   => 'Footer Background | خلفية الفوتر',
            'default' => '#1a1a2e',
        ),
        array(
            'id'      => 'footer_columns',
            'type'    => 'slider',
            'title'   => 'Footer Columns | أعمدة الفوتر',
            'min'     => 2,
            'max'     => 4,
            'default' => 4,
        ),
        array(
            'id'      => 'footer_copyright',
            'type'    => 'text',
            'title'   => 'Copyright Text | نص حقوق النشر',
            'default' => '© {year} YTrip. All rights reserved.',
        ),
        array(
            'id'      => 'footer_social_links',
            'type'    => 'group',
            'title'   => 'Social Links | روابط التواصل',
            'fields'  => array(
                array('id' => 'icon', 'type' => 'icon', 'title' => 'Icon'),
                array('id' => 'url', 'type' => 'text', 'title' => 'URL'),
            ),
        ),
    ),
));

// Booking Settings
CSF::createSection($prefix, array(
    'title'  => 'Booking Settings | إعدادات الحجز',
    'icon'   => 'fa fa-calendar-check',
    'fields' => array(
        array(
            'id'      => 'booking_form_style',
            'type'    => 'select',
            'title'   => 'Booking Form Style | نمط نموذج الحجز',
            'options' => array(
                'inline'  => 'Inline | داخلي',
                'modal'   => 'Modal Popup | نافذة منبثقة',
                'sidebar' => 'Sticky Sidebar | شريط ثابت',
            ),
            'default' => 'sidebar',
        ),
        array(
            'id'      => 'booking_show_calendar',
            'type'    => 'switcher',
            'title'   => 'Show Date Picker | إظهار التقويم',
            'default' => true,
        ),
        array(
            'id'      => 'booking_show_guests',
            'type'    => 'switcher',
            'title'   => 'Show Guest Selector | محدد الضيوف',
            'default' => true,
        ),
        array(
            'id'      => 'booking_min_guests',
            'type'    => 'number',
            'title'   => 'Minimum Guests | أقل عدد ضيوف',
            'default' => 1,
        ),
        array(
            'id'      => 'booking_max_guests',
            'type'    => 'number',
            'title'   => 'Maximum Guests | أكبر عدد ضيوف',
            'default' => 20,
        ),
        array(
            'id'      => 'booking_success_message',
            'type'    => 'textarea',
            'title'   => 'Success Message | رسالة النجاح',
            'default' => 'Thank you for your booking! We will contact you shortly.',
        ),
    ),
));

// =============================================================================
// DESIGN TOKENS - Component Level Settings
// =============================================================================

// Design Tokens Parent Section
CSF::createSection($prefix, array(
    'id'    => 'design_tokens',
    'title' => esc_html__( 'Design Tokens', 'ytrip' ),
    'icon'  => 'fa fa-cubes',
));

// Cards Tokens
CSF::createSection($prefix, array(
    'parent' => 'design_tokens',
    'title'  => esc_html__( 'Cards', 'ytrip' ),
    'icon'   => 'fa fa-id-card',
    'fields' => array(
        // Tab: Tour Cards
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Tour Card Tokens', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_tour_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Color', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_bg_hover',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Hover', 'ytrip' ),
            'default' => '#fafafa',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_border_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Border Color', 'ytrip' ),
            'default' => '#e5e7eb',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_border_width',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Width', 'ytrip' ),
            'default' => '1px',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '12px',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Padding', 'ytrip' ),
            'default' => '16px',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_shadow',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow', 'ytrip' ),
            'default' => '0 4px 6px -1px rgba(0,0,0,0.1)',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_shadow_hover',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow Hover', 'ytrip' ),
            'default' => '0 10px 15px -3px rgba(0,0,0,0.15)',
        ),
        array(
            'id'      => 'design_tokens_cards_tour_transition',
            'type'    => 'text',
            'title'   => esc_html__( 'Transition', 'ytrip' ),
            'default' => '0.3s ease',
        ),

        // Tab: Destination Cards
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Destination Card Tokens', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_destination_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Color', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_cards_destination_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '16px',
        ),
        array(
            'id'      => 'design_tokens_cards_destination_shadow',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow', 'ytrip' ),
            'default' => '0 4px 6px rgba(0,0,0,0.1)',
        ),
        array(
            'id'      => 'design_tokens_cards_destination_shadow_hover',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow Hover', 'ytrip' ),
            'default' => '0 12px 20px rgba(0,0,0,0.15)',
        ),
    ),
));

// Buttons Tokens
CSF::createSection($prefix, array(
    'parent' => 'design_tokens',
    'title'  => esc_html__( 'Buttons', 'ytrip' ),
    'icon'   => 'fa fa-hand-pointer',
    'fields' => array(
        // Primary Button
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Primary Button', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background', 'ytrip' ),
            'default' => '#2563eb',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_bg_hover',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Hover', 'ytrip' ),
            'default' => '#1d4ed8',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_text_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Text Color', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '8px',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Padding', 'ytrip' ),
            'default' => '12px 24px',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Font Size', 'ytrip' ),
            'default' => '16px',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_font_weight',
            'type'    => 'select',
            'title'   => esc_html__( 'Font Weight', 'ytrip' ),
            'options' => array(
                '400' => '400 - Normal',
                '500' => '500 - Medium',
                '600' => '600 - Semibold',
                '700' => '700 - Bold',
            ),
            'default' => '600',
        ),
        array(
            'id'      => 'design_tokens_buttons_primary_shadow',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow', 'ytrip' ),
            'default' => '0 4px 6px rgba(37,99,235,0.25)',
        ),

        // Secondary Button
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Secondary Button', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_buttons_secondary_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background', 'ytrip' ),
            'default' => '#f3f4f6',
        ),
        array(
            'id'      => 'design_tokens_buttons_secondary_bg_hover',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Hover', 'ytrip' ),
            'default' => '#e5e7eb',
        ),
        array(
            'id'      => 'design_tokens_buttons_secondary_text_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Text Color', 'ytrip' ),
            'default' => '#374151',
        ),
        array(
            'id'      => 'design_tokens_buttons_secondary_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '8px',
        ),

        // Ghost Button
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Ghost/Outline Button', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_buttons_ghost_border_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Border Color', 'ytrip' ),
            'default' => '#2563eb',
        ),
        array(
            'id'      => 'design_tokens_buttons_ghost_text_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Text Color', 'ytrip' ),
            'default' => '#2563eb',
        ),
        array(
            'id'      => 'design_tokens_buttons_ghost_bg_hover',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Hover', 'ytrip' ),
            'default' => 'rgba(37,99,235,0.1)',
        ),
        array(
            'id'      => 'design_tokens_buttons_ghost_border_width',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Width', 'ytrip' ),
            'default' => '2px',
        ),

        // Button Sizes
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Button Size Variants', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_buttons_sm_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Small Padding', 'ytrip' ),
            'default' => '8px 16px',
        ),
        array(
            'id'      => 'design_tokens_buttons_sm_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Small Font Size', 'ytrip' ),
            'default' => '14px',
        ),
        array(
            'id'      => 'design_tokens_buttons_lg_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Large Padding', 'ytrip' ),
            'default' => '16px 32px',
        ),
        array(
            'id'      => 'design_tokens_buttons_lg_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Large Font Size', 'ytrip' ),
            'default' => '18px',
        ),
    ),
));

// Forms Tokens
CSF::createSection($prefix, array(
    'parent' => 'design_tokens',
    'title'  => esc_html__( 'Forms & Inputs', 'ytrip' ),
    'icon'   => 'fa fa-wpforms',
    'fields' => array(
        // Input Fields
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Input Fields', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_forms_input_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_forms_input_text_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Text Color', 'ytrip' ),
            'default' => '#1f2937',
        ),
        array(
            'id'      => 'design_tokens_forms_input_placeholder',
            'type'    => 'color',
            'title'   => esc_html__( 'Placeholder Color', 'ytrip' ),
            'default' => '#9ca3af',
        ),
        array(
            'id'      => 'design_tokens_forms_input_border_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Border Color', 'ytrip' ),
            'default' => '#d1d5db',
        ),
        array(
            'id'      => 'design_tokens_forms_input_border_focus',
            'type'    => 'color',
            'title'   => esc_html__( 'Border Color Focus', 'ytrip' ),
            'default' => '#2563eb',
        ),
        array(
            'id'      => 'design_tokens_forms_input_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '8px',
        ),
        array(
            'id'      => 'design_tokens_forms_input_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Padding', 'ytrip' ),
            'default' => '12px 16px',
        ),
        array(
            'id'      => 'design_tokens_forms_input_shadow_focus',
            'type'    => 'text',
            'title'   => esc_html__( 'Focus Shadow', 'ytrip' ),
            'default' => '0 0 0 3px rgba(37,99,235,0.15)',
        ),

        // Labels
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Labels', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_forms_label_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Label Color', 'ytrip' ),
            'default' => '#374151',
        ),
        array(
            'id'      => 'design_tokens_forms_label_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Label Font Size', 'ytrip' ),
            'default' => '14px',
        ),
        array(
            'id'      => 'design_tokens_forms_label_font_weight',
            'type'    => 'select',
            'title'   => esc_html__( 'Label Font Weight', 'ytrip' ),
            'options' => array(
                '400' => '400 - Normal',
                '500' => '500 - Medium',
                '600' => '600 - Semibold',
            ),
            'default' => '500',
        ),

        // Error States
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Error States', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_forms_error_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Error Text Color', 'ytrip' ),
            'default' => '#dc2626',
        ),
        array(
            'id'      => 'design_tokens_forms_error_border_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Error Border Color', 'ytrip' ),
            'default' => '#dc2626',
        ),
        array(
            'id'      => 'design_tokens_forms_error_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Error Background', 'ytrip' ),
            'default' => '#fef2f2',
        ),
    ),
));

// Modals Tokens
CSF::createSection($prefix, array(
    'parent' => 'design_tokens',
    'title'  => esc_html__( 'Modals & Popups', 'ytrip' ),
    'icon'   => 'fa fa-window-restore',
    'fields' => array(
        // Overlay
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Overlay', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_modals_overlay_bg',
            'type'    => 'text',
            'title'   => esc_html__( 'Overlay Background', 'ytrip' ),
            'default' => 'rgba(0,0,0,0.5)',
            'desc'    => esc_html__( 'Use rgba for transparency', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_modals_overlay_backdrop_blur',
            'type'    => 'text',
            'title'   => esc_html__( 'Backdrop Blur', 'ytrip' ),
            'default' => '4px',
        ),

        // Container
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Modal Container', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_modals_container_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_modals_container_border_radius',
            'type'    => 'text',
            'title'   => esc_html__( 'Border Radius', 'ytrip' ),
            'default' => '16px',
        ),
        array(
            'id'      => 'design_tokens_modals_container_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Padding', 'ytrip' ),
            'default' => '24px',
        ),
        array(
            'id'      => 'design_tokens_modals_container_shadow',
            'type'    => 'text',
            'title'   => esc_html__( 'Shadow', 'ytrip' ),
            'default' => '0 25px 50px -12px rgba(0,0,0,0.25)',
        ),
        array(
            'id'      => 'design_tokens_modals_container_max_width',
            'type'    => 'text',
            'title'   => esc_html__( 'Max Width', 'ytrip' ),
            'default' => '560px',
        ),
        array(
            'id'      => 'design_tokens_modals_container_animation',
            'type'    => 'select',
            'title'   => esc_html__( 'Animation', 'ytrip' ),
            'options' => array(
                'scale-fade' => esc_html__( 'Scale & Fade', 'ytrip' ),
                'slide-up'   => esc_html__( 'Slide Up', 'ytrip' ),
                'fade'       => esc_html__( 'Fade Only', 'ytrip' ),
                'none'       => esc_html__( 'None', 'ytrip' ),
            ),
            'default' => 'scale-fade',
        ),

        // Header
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Modal Header', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_modals_header_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Title Font Size', 'ytrip' ),
            'default' => '20px',
        ),
        array(
            'id'      => 'design_tokens_modals_header_font_weight',
            'type'    => 'select',
            'title'   => esc_html__( 'Title Font Weight', 'ytrip' ),
            'options' => array(
                '500' => '500 - Medium',
                '600' => '600 - Semibold',
                '700' => '700 - Bold',
            ),
            'default' => '600',
        ),

        // Close Button
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Close Button', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_modals_close_button_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Size', 'ytrip' ),
            'default' => '32px',
        ),
        array(
            'id'      => 'design_tokens_modals_close_button_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Background', 'ytrip' ),
            'default' => '#f3f4f6',
        ),
        array(
            'id'      => 'design_tokens_modals_close_button_bg_hover',
            'type'    => 'color',
            'title'   => esc_html__( 'Background Hover', 'ytrip' ),
            'default' => '#e5e7eb',
        ),
    ),
));

// =============================================================================
// ADVANCED CARD CUSTOMIZATION
// =============================================================================

CSF::createSection($prefix, array(
    'title'  => esc_html__( 'Advanced Cards', 'ytrip' ),
    'icon'   => 'fa fa-layer-group',
    'fields' => array(
        // Badge Settings
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Badge Configuration', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_badge_position',
            'type'    => 'select',
            'title'   => esc_html__( 'Badge Position', 'ytrip' ),
            'options' => array(
                'top-left'       => esc_html__( 'Top Left', 'ytrip' ),
                'top-right'      => esc_html__( 'Top Right', 'ytrip' ),
                'bottom-left'    => esc_html__( 'Bottom Left', 'ytrip' ),
                'bottom-right'   => esc_html__( 'Bottom Right', 'ytrip' ),
                'overlay-center' => esc_html__( 'Center Overlay', 'ytrip' ),
            ),
            'default' => 'top-left',
        ),
        array(
            'id'      => 'design_tokens_cards_badge_style',
            'type'    => 'select',
            'title'   => esc_html__( 'Badge Style', 'ytrip' ),
            'options' => array(
                'pill'   => esc_html__( 'Pill', 'ytrip' ),
                'square' => esc_html__( 'Square', 'ytrip' ),
                'ribbon' => esc_html__( 'Ribbon', 'ytrip' ),
                'bubble' => esc_html__( 'Bubble', 'ytrip' ),
            ),
            'default' => 'pill',
        ),
        array(
            'id'      => 'design_tokens_cards_badge_bg',
            'type'    => 'color',
            'title'   => esc_html__( 'Badge Background', 'ytrip' ),
            'default' => '#dc2626',
        ),
        array(
            'id'      => 'design_tokens_cards_badge_text_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Badge Text Color', 'ytrip' ),
            'default' => '#ffffff',
        ),
        array(
            'id'      => 'design_tokens_cards_badge_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Badge Font Size', 'ytrip' ),
            'default' => '12px',
        ),

        // Price Display
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Price Display', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_price_format',
            'type'    => 'select',
            'title'   => esc_html__( 'Price Format', 'ytrip' ),
            'options' => array(
                'price_only'  => esc_html__( '$99', 'ytrip' ),
                'from_price'  => esc_html__( 'From $99', 'ytrip' ),
                'per_person'  => esc_html__( '$99/person', 'ytrip' ),
                'starting_at' => esc_html__( 'Starting at $99', 'ytrip' ),
            ),
            'default' => 'from_price',
        ),
        array(
            'id'      => 'design_tokens_cards_price_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Price Color', 'ytrip' ),
            'default' => '#059669',
        ),
        array(
            'id'      => 'design_tokens_cards_price_font_size',
            'type'    => 'text',
            'title'   => esc_html__( 'Price Font Size', 'ytrip' ),
            'default' => '20px',
        ),
        array(
            'id'      => 'design_tokens_cards_price_font_weight',
            'type'    => 'select',
            'title'   => esc_html__( 'Price Font Weight', 'ytrip' ),
            'options' => array(
                '500' => '500 - Medium',
                '600' => '600 - Semibold',
                '700' => '700 - Bold',
            ),
            'default' => '700',
        ),

        // Meta Visibility
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Meta Information Visibility', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_meta_show_rating',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Rating', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'design_tokens_cards_meta_show_duration',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Duration', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'design_tokens_cards_meta_show_location',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Location', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'design_tokens_cards_meta_show_reviews',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Reviews Count', 'ytrip' ),
            'default' => true,
        ),
        array(
            'id'      => 'design_tokens_cards_meta_show_capacity',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Show Capacity', 'ytrip' ),
            'default' => false,
        ),
        array(
            'id'      => 'design_tokens_cards_meta_color',
            'type'    => 'color',
            'title'   => esc_html__( 'Meta Text Color', 'ytrip' ),
            'default' => '#6b7280',
        ),

        // Animation Controls
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Animation Controls', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_animation_timing',
            'type'    => 'select',
            'title'   => esc_html__( 'Timing Function', 'ytrip' ),
            'options' => array(
                'ease'        => esc_html__( 'Ease', 'ytrip' ),
                'ease-in'     => esc_html__( 'Ease In', 'ytrip' ),
                'ease-out'    => esc_html__( 'Ease Out', 'ytrip' ),
                'ease-in-out' => esc_html__( 'Ease In Out', 'ytrip' ),
                'linear'      => esc_html__( 'Linear', 'ytrip' ),
            ),
            'default' => 'ease',
        ),
        array(
            'id'      => 'design_tokens_cards_animation_duration',
            'type'    => 'slider',
            'title'   => esc_html__( 'Animation Duration', 'ytrip' ),
            'min'     => 150,
            'max'     => 500,
            'step'    => 50,
            'unit'    => 'ms',
            'default' => 300,
        ),
        array(
            'id'      => 'design_tokens_cards_animation_hover_scale',
            'type'    => 'slider',
            'title'   => esc_html__( 'Hover Scale', 'ytrip' ),
            'min'     => 1,
            'max'     => 1.1,
            'step'    => 0.01,
            'default' => 1.02,
            'desc'    => esc_html__( '1.0 = no scale, 1.05 = 5% larger', 'ytrip' ),
        ),
        array(
            'id'      => 'design_tokens_cards_animation_hover_lift',
            'type'    => 'slider',
            'title'   => esc_html__( 'Hover Lift', 'ytrip' ),
            'min'     => -12,
            'max'     => 0,
            'step'    => 1,
            'unit'    => 'px',
            'default' => -4,
            'desc'    => esc_html__( 'Negative values lift the card up on hover', 'ytrip' ),
        ),
    ),
));

// =============================================================================
// DESIGN PRESETS
// =============================================================================

CSF::createSection($prefix, array(
    'title'  => esc_html__( 'Design Presets', 'ytrip' ),
    'icon'   => 'fa fa-palette',
    'fields' => array(
        array(
            'type'    => 'notice',
            'style'   => 'info',
            'content' => esc_html__( 'Apply a ready-made design preset with one click. You can also export your current settings or import a custom preset.', 'ytrip' ),
        ),
        array(
            'id'      => 'design_preset_select',
            'type'    => 'image_select',
            'title'   => esc_html__( 'Select Preset', 'ytrip' ),
            'options' => array(
                'modern_minimal'  => YTRIP_URL . 'assets/images/presets/modern-minimal.jpg',
                'bold_travel'     => YTRIP_URL . 'assets/images/presets/bold-travel.jpg',
                'luxury_gold'     => YTRIP_URL . 'assets/images/presets/luxury-gold.jpg',
                'clean_corporate' => YTRIP_URL . 'assets/images/presets/clean-corporate.jpg',
            ),
            'default' => '',
        ),
        array(
            'type'    => 'callback',
            'function' => 'ytrip_preset_buttons_callback',
        ),
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Import / Export', 'ytrip' ),
        ),
        array(
            'id'       => 'preset_export_name',
            'type'     => 'text',
            'title'    => esc_html__( 'Export Preset Name', 'ytrip' ),
            'default'  => 'My Custom Preset',
        ),
        array(
            'type'    => 'callback',
            'function' => 'ytrip_preset_export_import_callback',
        ),
    ),
));

// =============================================================================
// RESPONSIVE BREAKPOINTS
// =============================================================================

CSF::createSection($prefix, array(
    'title'  => esc_html__( 'Responsive Settings', 'ytrip' ),
    'icon'   => 'fa fa-mobile-alt',
    'fields' => array(
        array(
            'type'    => 'notice',
            'style'   => 'info',
            'content' => esc_html__( 'Configure different settings for desktop, tablet, and mobile devices.', 'ytrip' ),
        ),

        // Breakpoint Definitions
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Breakpoint Definitions', 'ytrip' ),
        ),
        array(
            'id'      => 'breakpoint_tablet',
            'type'    => 'slider',
            'title'   => esc_html__( 'Tablet Breakpoint', 'ytrip' ),
            'min'     => 768,
            'max'     => 1200,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 1024,
            'desc'    => esc_html__( 'Screen width below this value will use tablet styles', 'ytrip' ),
        ),
        array(
            'id'      => 'breakpoint_mobile',
            'type'    => 'slider',
            'title'   => esc_html__( 'Mobile Breakpoint', 'ytrip' ),
            'min'     => 480,
            'max'     => 768,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 767,
            'desc'    => esc_html__( 'Screen width below this value will use mobile styles', 'ytrip' ),
        ),

        // Desktop Settings (Default)
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Desktop Settings', 'ytrip' ),
        ),
        array(
            'id'      => 'responsive_desktop_columns',
            'type'    => 'slider',
            'title'   => esc_html__( 'Grid Columns', 'ytrip' ),
            'min'     => 2,
            'max'     => 5,
            'step'    => 1,
            'default' => 3,
        ),
        array(
            'id'      => 'responsive_desktop_card_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Card Padding', 'ytrip' ),
            'default' => '16px',
        ),
        array(
            'id'      => 'responsive_desktop_section_spacing',
            'type'    => 'slider',
            'title'   => esc_html__( 'Section Spacing', 'ytrip' ),
            'min'     => 40,
            'max'     => 120,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 80,
        ),

        // Tablet Settings
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Tablet Settings', 'ytrip' ),
        ),
        array(
            'id'      => 'responsive_tablet_columns',
            'type'    => 'slider',
            'title'   => esc_html__( 'Grid Columns', 'ytrip' ),
            'min'     => 1,
            'max'     => 4,
            'step'    => 1,
            'default' => 2,
        ),
        array(
            'id'      => 'responsive_tablet_card_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Card Padding', 'ytrip' ),
            'default' => '14px',
        ),
        array(
            'id'      => 'responsive_tablet_section_spacing',
            'type'    => 'slider',
            'title'   => esc_html__( 'Section Spacing', 'ytrip' ),
            'min'     => 32,
            'max'     => 96,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 56,
        ),

        // Mobile Settings
        array(
            'type'  => 'subheading',
            'content' => esc_html__( 'Mobile Settings', 'ytrip' ),
        ),
        array(
            'id'      => 'responsive_mobile_columns',
            'type'    => 'slider',
            'title'   => esc_html__( 'Grid Columns', 'ytrip' ),
            'min'     => 1,
            'max'     => 2,
            'step'    => 1,
            'default' => 1,
        ),
        array(
            'id'      => 'responsive_mobile_card_padding',
            'type'    => 'text',
            'title'   => esc_html__( 'Card Padding', 'ytrip' ),
            'default' => '12px',
        ),
        array(
            'id'      => 'responsive_mobile_section_spacing',
            'type'    => 'slider',
            'title'   => esc_html__( 'Section Spacing', 'ytrip' ),
            'min'     => 24,
            'max'     => 64,
            'step'    => 8,
            'unit'    => 'px',
            'default' => 40,
        ),
        array(
            'id'      => 'responsive_mobile_font_scale',
            'type'    => 'slider',
            'title'   => esc_html__( 'Font Size Scale', 'ytrip' ),
            'min'     => 0.8,
            'max'     => 1,
            'step'    => 0.05,
            'default' => 0.9,
            'desc'    => esc_html__( 'Scale factor for all font sizes on mobile', 'ytrip' ),
        ),
    ),
));

// =============================================================================
// LIVE PREVIEW (Toggle)
// =============================================================================

CSF::createSection($prefix, array(
    'title'  => esc_html__( 'Live Preview', 'ytrip' ),
    'icon'   => 'fa fa-eye',
    'fields' => array(
        array(
            'id'      => 'enable_live_preview',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Live Preview', 'ytrip' ),
            'default' => true,
            'desc'    => esc_html__( 'Show a real-time preview panel while editing settings', 'ytrip' ),
        ),
        array(
            'id'         => 'live_preview_position',
            'type'       => 'button_set',
            'title'      => esc_html__( 'Preview Position', 'ytrip' ),
            'options'    => array(
                'right'  => esc_html__( 'Right Sidebar', 'ytrip' ),
                'bottom' => esc_html__( 'Bottom Panel', 'ytrip' ),
            ),
            'default'    => 'right',
            'dependency' => array( 'enable_live_preview', '==', 'true' ),
        ),
        array(
            'id'         => 'live_preview_width',
            'type'       => 'slider',
            'title'      => esc_html__( 'Preview Width', 'ytrip' ),
            'min'        => 300,
            'max'        => 500,
            'step'       => 20,
            'unit'       => 'px',
            'default'    => 380,
            'dependency' => array( 'enable_live_preview|live_preview_position', '==|==', 'true|right' ),
        ),
        array(
            'type'    => 'callback',
            'function' => 'ytrip_live_preview_panel_callback',
        ),
    ),
));

// =============================================================================
// CALLBACK FUNCTIONS FOR CUSTOM UI
// =============================================================================

/**
 * Preset action buttons callback
 */
function ytrip_preset_buttons_callback() {
    ?>
    <div class="csf-field" style="padding: 15px 0;">
        <button type="button" class="button button-primary" id="ytrip-apply-preset-btn">
            <i class="fa fa-check"></i> <?php esc_html_e( 'Apply Selected Preset', 'ytrip' ); ?>
        </button>
        <span id="ytrip-preset-msg" style="margin-left: 15px; font-weight: 600; display: none;"></span>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#ytrip-apply-preset-btn').on('click', function(e) {
            e.preventDefault();
            var presetId = $('input[name="ytrip_settings[design_preset_select]"]:checked').val();
            if (!presetId) {
                $('#ytrip-preset-msg').text('Please select a preset first').css('color', '#dc2626').show();
                return;
            }
            var btn = $(this);
            btn.addClass('disabled').html('<i class="fa fa-spinner fa-spin"></i> Applying...');
            
            $.post(ajaxurl, {
                action: 'ytrip_apply_preset',
                preset_id: presetId,
                nonce: '<?php echo wp_create_nonce( 'ytrip_admin_nonce' ); ?>'
            }, function(res) {
                btn.removeClass('disabled').html('<i class="fa fa-check"></i> Apply Selected Preset');
                if (res.success) {
                    $('#ytrip-preset-msg').text('Preset applied! Reloading...').css('color', '#059669').show();
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    $('#ytrip-preset-msg').text(res.data || 'Error applying preset').css('color', '#dc2626').show();
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * Preset export/import callback
 */
function ytrip_preset_export_import_callback() {
    ?>
    <div class="csf-field" style="padding: 15px 0;">
        <button type="button" class="button" id="ytrip-export-preset-btn">
            <i class="fa fa-download"></i> <?php esc_html_e( 'Export Current Settings', 'ytrip' ); ?>
        </button>
        <button type="button" class="button" id="ytrip-import-preset-btn" style="margin-left: 10px;">
            <i class="fa fa-upload"></i> <?php esc_html_e( 'Import Preset', 'ytrip' ); ?>
        </button>
        <input type="file" id="ytrip-import-file" accept=".json" style="display: none;">
        <span id="ytrip-import-export-msg" style="margin-left: 15px; font-weight: 600; display: none;"></span>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // Export
        $('#ytrip-export-preset-btn').on('click', function(e) {
            e.preventDefault();
            var presetName = $('input[name="ytrip_settings[preset_export_name]"]').val() || 'custom';
            
            $.post(ajaxurl, {
                action: 'ytrip_export_preset',
                name: presetName,
                nonce: '<?php echo wp_create_nonce( 'ytrip_admin_nonce' ); ?>'
            }, function(res) {
                if (res.success) {
                    var blob = new Blob([res.data.json], {type: 'application/json'});
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = res.data.filename;
                    a.click();
                    URL.revokeObjectURL(url);
                    $('#ytrip-import-export-msg').text('Exported!').css('color', '#059669').show().delay(3000).fadeOut();
                }
            });
        });
        
        // Import trigger
        $('#ytrip-import-preset-btn').on('click', function() {
            $('#ytrip-import-file').click();
        });
        
        // Import handler
        $('#ytrip-import-file').on('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;
            
            var reader = new FileReader();
            reader.onload = function(e) {
                $.post(ajaxurl, {
                    action: 'ytrip_import_preset',
                    json: e.target.result,
                    nonce: '<?php echo wp_create_nonce( 'ytrip_admin_nonce' ); ?>'
                }, function(res) {
                    if (res.success) {
                        $('#ytrip-import-export-msg').text('Imported! Reloading...').css('color', '#059669').show();
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        $('#ytrip-import-export-msg').text(res.data || 'Import failed').css('color', '#dc2626').show();
                    }
                });
            };
            reader.readAsText(file);
        });
    });
    </script>
    <?php
}

/**
 * Live preview panel callback
 */
function ytrip_live_preview_panel_callback() {
    $options = get_option( 'ytrip_settings', array() );
    $enabled = ! empty( $options['enable_live_preview'] );
    
    if ( ! $enabled ) {
        echo '<div class="csf-field"><p>' . esc_html__( 'Enable Live Preview above to see the preview panel.', 'ytrip' ) . '</p></div>';
        return;
    }
    ?>
    <div class="csf-field">
        <div id="ytrip-live-preview-container" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-top: 15px;">
            <h4 style="margin: 0 0 15px 0; font-size: 14px; color: #374151;">
                <i class="fa fa-eye"></i> <?php esc_html_e( 'Live Preview', 'ytrip' ); ?>
            </h4>
            <div id="ytrip-preview-card" style="background: var(--ytrip-cards-tour-bg, #fff); border-radius: var(--ytrip-cards-tour-border-radius, 12px); box-shadow: var(--ytrip-cards-tour-shadow, 0 4px 6px rgba(0,0,0,0.1)); overflow: hidden; max-width: 320px;">
                <div style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative;">
                    <span style="position: absolute; top: 12px; left: 12px; background: var(--ytrip-cards-badge-bg, #dc2626); color: var(--ytrip-cards-badge-text-color, #fff); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Featured</span>
                </div>
                <div style="padding: var(--ytrip-cards-tour-padding, 16px);">
                    <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Sample Tour Title</h3>
                    <p style="margin: 0 0 12px 0; color: var(--ytrip-cards-meta-color, #6b7280); font-size: 14px;">
                        <i class="fa fa-map-marker-alt"></i> Paris, France
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--ytrip-cards-price-color, #059669); font-size: var(--ytrip-cards-price-font-size, 20px); font-weight: 700;">From $299</span>
                        <button style="background: var(--ytrip-buttons-primary-bg, #2563eb); color: var(--ytrip-buttons-primary-text-color, #fff); border: none; padding: 8px 16px; border-radius: var(--ytrip-buttons-primary-border-radius, 8px); cursor: pointer; font-weight: 600;">Book Now</button>
                    </div>
                </div>
            </div>
            <p style="margin: 15px 0 0 0; font-size: 12px; color: #6b7280;">
                <?php esc_html_e( 'This preview updates in real-time as you change settings.', 'ytrip' ); ?>
            </p>
        </div>
    </div>
    <?php
}

// Taxonomy Options (Image & Background)
CSF::createTaxonomyOptions('ytrip_term_options', array(
    'taxonomy' => array( 'ytrip_destination', 'ytrip_category' ),
    'fields'   => array(
        array(
            'id'    => 'term_image',
            'type'  => 'media',
            'title' => 'Feature Image | صورة التصنيف',
            'desc'  => 'Image used for homepage cards and lists.',
        ),
        array(
            'id'    => 'term_background',
            'type'  => 'media',
            'title' => 'Page Header Background | خلفية الصفحة',
            'desc'  => 'Wide background image for the archive page header.',
        ),
    ),
));
