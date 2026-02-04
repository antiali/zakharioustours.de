<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CSF' ) ) {
    return;
}

CSF::createOptions('ytrip_homepage', array(
    'menu_title'       => 'Homepage Builder | بناء الصفحة الرئيسية',
    'menu_slug'        => 'ytrip-homepage',
    'menu_icon'        => 'dashicons-admin-home',
    'menu_position'    => 3,
    'framework_title'  => 'YTrip Homepage Builder',
    'theme'            => 'dark',
));

// Homepage Sections Manager
CSF::createSection('ytrip_homepage', array(
    'title'  => 'Homepage Sections | أقسام الصفحة',
    'icon'   => 'fa fa-th-large',
    'fields' => array(
        array(
            'id'          => 'homepage_sections',
            'type'        => 'sorter',
            'title'       => 'Drag to Reorder Sections | رتب الأقسام',
            'default'     => array(
                'enabled'  => array(
                    'hero_slider'    => esc_html__( 'Hero Slider', 'ytrip' ),
                    'search_form'    => esc_html__( 'Search & Filter', 'ytrip' ),
                    'featured_tours' => esc_html__( 'Featured Tours', 'ytrip' ),
                    'destinations'   => esc_html__( 'Popular Destinations', 'ytrip' ),
                    'categories'     => esc_html__( 'Tour Categories', 'ytrip' ),
                    'testimonials'   => esc_html__( 'Customer Reviews', 'ytrip' ),
                    'stats'          => esc_html__( 'Statistics Counter', 'ytrip' ),
                    'blog'           => esc_html__( 'Latest Blog Posts', 'ytrip' ),
                ),
                'disabled' => array(
                    'video_banner'   => esc_html__( 'Video Banner', 'ytrip' ),
                    'promo_banner'   => esc_html__( 'Promotional Banner', 'ytrip' ),
                    'partners'       => esc_html__( 'Partners/Sponsors', 'ytrip' ),
                    'instagram_feed' => esc_html__( 'Instagram Feed', 'ytrip' ),
                ),
            ),
        ),
    ),
));

// 0. GENERAL SETTINGS
CSF::createSection('ytrip_homepage', array(
    'title'  => 'General Settings | إعدادات عامة',
    'icon'   => 'fa fa-cogs',
    'fields' => array(
        array(
            'id'      => 'homepage_layout',
            'type'    => 'image_select',
            'title'   => 'Homepage Layout | تصميم الصفحة الرئيسية',
            'desc'    => 'اختر التصميم العام للصفحة الرئيسية. (Choose the overall homepage layout structure).',
            'options' => array(
                'modern'  => YTRIP_URL . 'assets/images/home-layouts/modern.png',
                'classic' => YTRIP_URL . 'assets/images/home-layouts/classic.png',
                'search'  => YTRIP_URL . 'assets/images/home-layouts/search-focused.png',
            ),
            'default' => 'modern',
        ),
        array(
            'id'      => 'homepage_width',
            'type'    => 'select',
            'title'   => 'Content Width | عرض المحتوى',
            'options' => array(
                'boxed' => 'Boxed | صندوق',
                'wide'  => 'Wide | عريض',
                'full'  => 'Full Width | عرض كامل',
            ),
            'default' => 'wide',
        ),
    ),
));

// 1. HERO SLIDER SECTION
CSF::createSection('ytrip_homepage', array(
    'title'  => 'Hero Slider | السلايدر الرئيسي',
    'icon'   => 'fa fa-images',
    'fields' => array(
        array(
            'id'      => 'hero_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Hero Slider | تفعيل السلايدر',
            'default' => true,
            'desc'    => 'تفعيل أو تعطيل قسم السلايدر الرئيسي في أعلى الصفحة. (Enable or disable the main hero slider).',
        ),
        array(
            'id'     => 'hero_slides',
            'type'   => 'group',
            'title'  => 'Slides | الشرائح',
            'subtitle' => 'أضف صوراً ونصوصاً للعرض في السلايدر. (Add images and text for the slider).',
            'fields' => array(
                array(
                    'id'    => 'slide_image',
                    'type'  => 'media',
                    'title' => 'Background Image | صورة الخلفية',
                    'desc'  => 'يفضل استخدام صور عالية الجودة بحجم 1920x800. (High quality images recommended).',
                ),
                array(
                    'id'    => 'slide_title',
                    'type'  => 'text',
                    'title' => 'Title | العنوان',
                    'desc'  => 'العنوان الرئيسي للشريحة.',
                ),
                array(
                    'id'    => 'slide_subtitle',
                    'type'  => 'textarea',
                    'title' => 'Subtitle | العنوان الفرعي',
                    'desc'  => 'نص وصفي قصير يظهر تحت العنوان.',
                ),
                array(
                    'id'      => 'button_1',
                    'type'    => 'fieldset',
                    'title'   => 'Button 1 | الزر الأول',
                    'fields'  => array(
                        array(
                            'id'    => 'text',
                            'type'  => 'text',
                            'title' => esc_html__('Button Text', 'ytrip'),
                        ),
                        array(
                            'id'    => 'link',
                            'type'  => 'link',
                            'title' => esc_html__('Button Link', 'ytrip'),
                        ),
                        array(
                            'id'      => 'style',
                            'type'    => 'button_set',
                            'title'   => esc_html__('Style', 'ytrip'),
                            'options' => array(
                                'primary'   => esc_html__('Primary', 'ytrip'),
                                'secondary' => esc_html__('Secondary', 'ytrip'),
                                'outline'   => esc_html__('Outline', 'ytrip'),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
));

// 2. SEARCH & FILTER SECTION
CSF::createSection('ytrip_homepage', array(
    'title'  => 'Search Form | نموذج البحث',
    'icon'   => 'fa fa-search',
    'fields' => array(
        array(
            'id'      => 'search_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Search | تفعيل البحث',
            'default' => true,
            'desc'    => 'إظهار نموذج البحث عن الرحلات. (Show the tour search form).',
        ),
        array(
            'id'      => 'search_style',
            'type'    => 'image_select',
            'title'   => 'Search Form Style | نمط البحث',
            'desc'    => 'اختر شكل وتصميم نموذج البحث. (Select the design compatibility of the search form).',
            'options' => array(
                'style_1' => YTRIP_URL . 'assets/images/search-styles/style-1.jpg',
                'style_2' => YTRIP_URL . 'assets/images/search-styles/style-2.jpg',
                'style_3' => YTRIP_URL . 'assets/images/search-styles/style-3.jpg',
            ),
            'default' => 'style_1',
        ),
    ),
));

// 3. FEATURED TOURS SECTION
CSF::createSection('ytrip_homepage', array(
    'title'  => 'Featured Tours | الرحلات المميزة',
    'icon'   => 'fa fa-star',
    'fields' => array(
        array(
            'id'      => 'featured_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Featured Tours',
            'default' => true,
            'desc'    => 'إظهار قسم الرحلات المميزة. (Show featured tours section).',
        ),
        array(
            'id'      => 'featured_section_title',
            'type'    => 'text',
            'title'   => 'Section Title | عنوان القسم',
            'default' => 'Featured Tours',
        ),
        array(
            'id'      => 'featured_selection',
            'type'    => 'button_set',
            'title'   => 'Tours Selection | اختيار الرحلات',
            'desc'    => 'كيفية اختيار الرحلات التي تظهر هنا. (How to select tours to display).',
            'options' => array(
                'auto'   => 'Automatic (Latest)',
                'manual' => 'Manual Selection',
            ),
            'default' => 'auto',
        ),
        array(
            'id'         => 'featured_tours',
            'type'       => 'select',
            'title'      => 'Select Tours | اختر الرحلات',
            'multiple'   => true,
            'chosen'     => true,
            'ajax'       => true,
            'options'    => 'posts',
            'query_args' => array(
                'post_type' => 'ytrip_tour',
            ),
            'dependency' => array('featured_selection', '==', 'manual'),
            'desc'       => 'ابحث واختر الرحلات يدوياً. (Search and select tours manually).',
        ),
        array(
            'id'      => 'featured_count',
            'type'    => 'number',
            'title'   => 'Number of Tours | عدد الرحلات',
            'default' => 6,
            'dependency' => array('featured_selection', '==', 'auto'),
            'desc'    => 'عدد الرحلات التي يتم عرضها تلقائياً. (Number of tours to display automatically).',
        ),
    ),
));

// 4. DESTINATIONS SECTION
CSF::createSection('ytrip_homepage', array(
    'title'  => 'Destinations | الوجهات',
    'icon'   => 'fa fa-map',
    'fields' => array(
        array(
            'id'      => 'destinations_enable',
            'type'    => 'switcher',
            'title'   => 'Enable Destinations',
            'default' => true,
            'desc'    => 'إظهار قسم الوجهات السياحية. (Show destinations section).',
        ),
        array(
            'id'      => 'destinations_title',
            'type'    => 'text',
            'title'   => 'Section Title',
            'default' => 'Popular Destinations',
        ),
    ),
));

// ... (Other sections)
