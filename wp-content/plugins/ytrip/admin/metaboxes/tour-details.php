<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CSF' ) ) {
    return;
}

$prefix = 'ytrip_tour_details';

CSF::createMetabox($prefix, array(
    'title'     => 'Tour Details | تفاصيل الرحلة',
    'post_type' => 'ytrip_tour',
    'context'   => 'normal',
    'priority'  => 'high',
));

// Tab 1: Basic Info
CSF::createSection($prefix, array(
    'title'  => 'Basic Information',
    'icon'   => 'fa fa-info-circle',
    'fields' => array(
        array(
            'id'      => 'tour_duration',
            'type'    => 'fieldset',
            'title'   => 'Duration | المدة',
            'desc'    => 'حدد مدة الرحلة بالأيام والليالي. (Set the tour duration in days and nights).',
            'fields'  => array(
                array(
                    'id'      => 'days',
                    'type'    => 'number',
                    'title'   => 'Days | أيام',
                    'default' => 1,
                    'desc'    => 'عدد الأيام.',
                ),
                array(
                    'id'      => 'nights',
                    'type'    => 'number',
                    'title'   => 'Nights | ليالي',
                    'default' => 0,
                    'desc'    => 'عدد الليالي.',
                ),
            ),
        ),
        array(
            'id'      => 'group_size',
            'type'    => 'fieldset',
            'title'   => 'Group Size | حجم المجموعة',
            'desc'    => 'عدد الأشخاص المسموح به في الرحلة. (Allowed number of people).',
            'fields'  => array(
                array(
                    'id'      => 'min',
                    'type'    => 'number',
                    'title'   => 'Minimum | الحد الأدنى',
                    'default' => 1,
                ),
                array(
                    'id'      => 'max',
                    'type'    => 'number',
                    'title'   => 'Maximum | الحد الأقصى',
                    'default' => 50,
                ),
            ),
        ),
        array(
            'id'      => 'difficulty',
            'type'    => 'button_set',
            'title'   => 'Difficulty | الصعوبة',
            'desc'    => 'مستوى الجهد البدني المطلوب للرحلة. (Physical effort level required).',
            'options' => array(
                'easy'      => 'Easy | سهل',
                'moderate'  => 'Moderate | متوسط',
                'difficult' => 'Difficult | صعب',
                'expert'    => 'Expert | خبير',
            ),
            'default' => 'moderate',
        ),
        array(
            'id'      => 'age_restriction',
            'type'    => 'fieldset',
            'title'   => 'Age Restriction | العمر',
            'desc'    => 'الفئة العمرية المسموح لها بالمشاركة. (Allowed age range).',
            'fields'  => array(
                array(
                    'id'      => 'min_age',
                    'type'    => 'number',
                    'title'   => 'Minimum Age | الحد الأدنى',
                    'default' => 0,
                ),
                array(
                    'id'      => 'max_age',
                    'type'    => 'number',
                    'title'   => 'Maximum Age | الحد الأقصى',
                    'default' => 99,
                ),
            ),
        ),
    ),
));

// Tab 2: Itinerary (Daily Program)
CSF::createSection($prefix, array(
    'title'  => 'Itinerary | البرنامج',
    'icon'   => 'fa fa-calendar',
    'fields' => array(
        array(
            'id'     => 'itinerary',
            'type'   => 'group',
            'title'  => 'Daily Program | البرنامج اليومي',
            'subtitle' => 'أضف تفاصيل البرنامج اليومي للرحلة هنا. يمكنك إضافة عدة أيام. (Add daily program details here).',
            'fields' => array(
                array(
                    'id'    => 'day_number',
                    'type'  => 'number',
                    'title' => 'Day | اليوم',
                    'default' => 1,
                ),
                array(
                    'id'    => 'day_title',
                    'type'  => 'text',
                    'title' => 'Title | العنوان',
                    'desc'  => 'مثال: الوصول والاستقبال (Arrival)',
                ),
                array(
                    'id'    => 'day_description',
                    'type'  => 'wysiwyg',
                    'title' => 'Description | الوصف',
                ),
                array(
                    'id'    => 'day_image',
                    'type'  => 'media',
                    'title' => 'Image | الصورة',
                ),
                array(
                    'id'    => 'activities',
                    'type'  => 'repeater',
                    'title' => 'Activities | الأنشطة',
                    'fields' => array(
                        array(
                            'id'    => 'time',
                            'type'  => 'text',
                            'title' => 'Time | الوقت',
                        ),
                        array(
                            'id'    => 'activity',
                            'type'  => 'text',
                            'title' => 'Activity | النشاط',
                        ),
                    ),
                ),
            ),
        ),
    ),
));

// Tab 3: Included/Excluded
CSF::createSection($prefix, array(
    'title'  => 'Included/Excluded',
    'icon'   => 'fa fa-check',
    'fields' => array(
        array(
            'id'     => 'included',
            'type'   => 'repeater',
            'title'  => 'What\'s Included | ما يشمله السعر',
            'fields' => array(
                array(
                    'id'    => 'item',
                    'type'  => 'text',
                    'title' => 'Item | العنصر',
                ),
                array(
                    'id'    => 'icon',
                    'type'  => 'icon',
                    'title' => 'Icon | أيقونة',
                ),
            ),
        ),
        array(
            'id'     => 'excluded',
            'type'   => 'repeater',
            'title'  => 'What\'s Excluded | غير مشمول',
            'fields' => array(
                array(
                    'id'    => 'item',
                    'type'  => 'text',
                    'title' => 'Item | العنصر',
                ),
            ),
        ),
    ),
));

// Tab 4: Gallery & Media
CSF::createSection($prefix, array(
    'title'  => 'Gallery | المعرض',
    'icon'   => 'fa fa-images',
    'fields' => array(
        array(
            'id'    => 'tour_gallery',
            'type'  => 'gallery',
            'title' => 'Tour Images | صور الرحلة',
            'desc'  => 'اختر صوراً متعددة لإنشاء معرض صور للرحلة. (Select multiple images for tour gallery).',
        ),
        array(
            'id'    => 'tour_video',
            'type'  => 'text',
            'title' => 'YouTube Video URL | رابط فيديو',
            'desc'  => 'رابط فيديو يوتيوب تعريفي للرحلة (اختياري). (YouTube video URL - Optional).',
        ),
    ),
));

// Tab 5: Location
CSF::createSection($prefix, array(
    'title'  => 'Location | الموقع',
    'icon'   => 'fa fa-map-marker',
    'fields' => array(
        array(
            'id'    => 'meeting_point',
            'type'  => 'text',
            'title' => 'Meeting Point | نقطة التجمع',
            'desc'  => 'العنوان أو المكان الذي ستبدأ منه الرحلة.',
        ),
        array(
            'id'    => 'map_location',
            'type'  => 'map',
            'title' => 'Map | الخريطة',
            'desc'  => 'حدد موقع التجمع على الخريطة.',
        ),
        array(
            'id'     => 'destinations',
            'type'   => 'repeater',
            'title'  => 'Tour Destinations | وجهات الرحلة',
            'desc'   => 'قائمة بالأماكن التي ستتم زيارتها أثناء الرحلة.',
            'fields' => array(
                array(
                    'id'    => 'destination_name',
                    'type'  => 'text',
                    'title' => 'Destination | الوجهة',
                ),
                array(
                    'id'    => 'lat_lng',
                    'type'  => 'map',
                    'title' => 'Location | الموقع',
                ),
            ),
        ),
    ),
));

// Tab 6: FAQ
CSF::createSection($prefix, array(
    'title'  => 'FAQ',
    'icon'   => 'fa fa-question-circle',
    'fields' => array(
        array(
            'id'     => 'faq',
            'type'   => 'group',
            'title'  => 'Frequently Asked Questions',
            'subtitle' => 'أسئلة شائعة قد يطرحها العملاء حول هذه الرحلة.',
            'fields' => array(
                array(
                    'id'    => 'question',
                    'type'  => 'text',
                    'title' => 'Question | السؤال',
                ),
                array(
                    'id'    => 'answer',
                    'type'  => 'textarea',
                    'title' => 'Answer | الإجابة',
                ),
            ),
        ),
    ),
));

// Tab 7: Highlights
CSF::createSection($prefix, array(
    'title'  => 'Highlights | النقاط المميزة',
    'icon'   => 'fa fa-star',
    'fields' => array(
        array(
            'id'     => 'highlights',
            'type'   => 'repeater',
            'title'  => 'Tour Highlights',
            'subtitle' => 'أهم المزايا والنقاط التي تجعل هذه الرحلة مميزة.',
            'fields' => array(
                array(
                    'id'    => 'highlight',
                    'type'  => 'text',
                    'title' => 'Highlight | نقطة مميزة',
                ),
            ),
        ),
    ),
));

// Tab 8 (Before Related): Booking & Pricing
CSF::createSection($prefix, array(
    'title'  => 'Booking & Pricing | الحجز والأسعار',
    'icon'   => 'fa fa-tag',
    'fields' => array(
        array(
            'id'      => 'booking_method',
            'type'    => 'button_set',
            'title'   => 'Booking Method | طريقة الحجز',
            'options' => array(
                'woocommerce' => 'Booking (WooCommerce)',
                'inquiry'     => 'Inquiry Form',
            ),
            'default' => 'woocommerce',
        ),
        array(
            'id'         => 'inquiry_email',
            'type'       => 'text',
            'title'      => 'Notification Email | بريد الإشعارات',
            'desc'       => 'Email to receive inquiry notifications. Defaults to admin email if empty.',
            'dependency' => array('booking_method', '==', 'inquiry'),
        ),
        array(
            'id'         => 'price_settings',
            'type'       => 'fieldset',
            'title'      => 'Pricing Settings',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'      => 'tour_price',
                    'type'    => 'text',
                    'title'   => 'Regular Price | السعر الأساسي',
                    'desc'    => 'Enter the price per person.',
                    'default' => '',
                ),
                array(
                    'id'      => 'tour_sale_price',
                    'type'    => 'text',
                    'title'   => 'Sale Price | سعر الخصم',
                    'desc'    => 'Leave empty if not on sale.',
                    'default' => '',
                ),
            ),
        ),
        // === DYNAMIC PRICING: Person Types ===
        array(
            'id'         => 'person_types',
            'type'       => 'group',
            'title'      => 'Person Types | أنواع الأشخاص',
            'subtitle'   => 'Define different pricing for adults, children, etc.',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'      => 'type_key',
                    'type'    => 'text',
                    'title'   => 'Type Key | المفتاح',
                    'desc'    => 'Unique key (e.g., adult, child, infant)',
                    'default' => '',
                ),
                array(
                    'id'      => 'type_label',
                    'type'    => 'text',
                    'title'   => 'Label | التسمية',
                    'desc'    => 'Display name (e.g., Adult, Child)',
                ),
                array(
                    'id'      => 'modifier_type',
                    'type'    => 'select',
                    'title'   => 'Modifier Type',
                    'options' => array(
                        'percentage' => 'Percentage (%)',
                        'fixed'      => 'Fixed Amount',
                    ),
                    'default' => 'percentage',
                ),
                array(
                    'id'      => 'modifier_value',
                    'type'    => 'number',
                    'title'   => 'Modifier Value | القيمة',
                    'desc'    => 'For %, use negative for discount (-50 = 50% off). For fixed, use amount.',
                    'default' => 0,
                ),
                array(
                    'id'      => 'min_age',
                    'type'    => 'number',
                    'title'   => 'Min Age | العمر الأدنى',
                    'default' => 0,
                ),
                array(
                    'id'      => 'max_age',
                    'type'    => 'number',
                    'title'   => 'Max Age | العمر الأقصى',
                    'default' => 99,
                ),
            ),
        ),
        // === DYNAMIC PRICING: Seasonal Pricing ===
        array(
            'id'         => 'seasonal_pricing',
            'type'       => 'group',
            'title'      => 'Seasonal Pricing | التسعير الموسمي',
            'subtitle'   => 'Add price adjustments for specific date ranges (e.g., High Season +20%).',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'    => 'season_name',
                    'type'  => 'text',
                    'title' => 'Season Name | اسم الموسم',
                    'desc'  => 'E.g., High Season, Holiday Peak',
                ),
                array(
                    'id'    => 'start_date',
                    'type'  => 'date',
                    'title' => 'Start Date | تاريخ البدء',
                ),
                array(
                    'id'    => 'end_date',
                    'type'  => 'date',
                    'title' => 'End Date | تاريخ الانتهاء',
                ),
                array(
                    'id'      => 'modifier_type',
                    'type'    => 'select',
                    'title'   => 'Modifier Type',
                    'options' => array(
                        'percentage' => 'Percentage (%)',
                        'fixed'      => 'Fixed Amount',
                    ),
                    'default' => 'percentage',
                ),
                array(
                    'id'      => 'modifier_value',
                    'type'    => 'number',
                    'title'   => 'Modifier Value | القيمة',
                    'desc'    => 'Positive = increase, Negative = decrease. E.g., 20 = +20%.',
                    'default' => 0,
                ),
            ),
        ),
        // === DYNAMIC PRICING: Group Discounts ===
        array(
            'id'         => 'group_discounts',
            'type'       => 'group',
            'title'      => 'Group Discounts | خصومات المجموعات',
            'subtitle'   => 'Offer discounts for larger groups.',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'      => 'min_persons',
                    'type'    => 'number',
                    'title'   => 'Minimum Persons | الحد الأدنى',
                    'desc'    => 'Discount applies if total persons >= this number.',
                    'default' => 5,
                ),
                array(
                    'id'      => 'discount_type',
                    'type'    => 'select',
                    'title'   => 'Discount Type',
                    'options' => array(
                        'percentage' => 'Percentage (%)',
                        'fixed'      => 'Fixed Amount',
                    ),
                    'default' => 'percentage',
                ),
                array(
                    'id'      => 'discount_value',
                    'type'    => 'number',
                    'title'   => 'Discount Value | قيمة الخصم',
                    'desc'    => 'E.g., 10 = 10% off total.',
                    'default' => 10,
                ),
            ),
        ),
        // === Blocked Dates ===
        array(
            'id'         => 'blocked_dates',
            'type'       => 'repeater',
            'title'      => 'Blocked Dates | تواريخ محجوبة',
            'subtitle'   => 'Dates when this tour is not available.',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'    => 'date',
                    'type'  => 'date',
                    'title' => 'Date',
                ),
            ),
        ),
        array(
            'id'         => 'booking_settings',
            'type'       => 'fieldset',
            'title'      => 'Inventory & Booking',
            'dependency' => array('booking_method', '==', 'woocommerce'),
            'fields'     => array(
                array(
                    'id'      => 'tour_stock',
                    'type'    => 'number',
                    'title'   => 'Max Seats Available',
                    'desc'    => 'Total number of seats for this tour.',
                    'default' => 20,
                ),
            ),
        ),
        array(
            'type'       => 'notice',
            'style'      => 'info',
            'content'    => '<strong>Note:</strong> Saving this tour will automatically update the linked booking product in the system.',
            'dependency' => array('booking_method', '==', 'woocommerce'),
        ),
    ),
));

// Tab 9: Related Tours
CSF::createSection($prefix, array(
    'title'  => 'Related Tours | رحلات ذات صلة',
    'icon'   => 'fa fa-link',
    'fields' => array(
        array(
            'id'      => 'related_mode',
            'type'    => 'button_set',
            'title'   => 'Mode | طريقة العرض',
            'desc'    => 'اختر كيفية عرض الرحلات ذات الصلة. (Choose how to display related tours).',
            'options' => array(
                'auto'   => 'Auto (Based on Taxonomy)',
                'manual' => 'Manual Selection',
            ),
            'default' => 'auto',
        ),
        array(
            'id'         => 'related_taxonomy',
            'type'       => 'select',
            'title'      => 'Match By | التطابق حسب',
            'desc'       => 'اختر التصنيف المستخدم لجلب الرحلات المشابهة. (Select taxonomy for auto matching).',
            'options'    => array(
                'ytrip_destination' => 'Destination | الوجهة',
                'ytrip_category'    => 'Category | التصنيف',
            ),
            'default'    => 'ytrip_destination',
            'dependency' => array('related_mode', '==', 'auto'),
        ),
        array(
            'id'         => 'related_count',
            'type'       => 'number',
            'title'      => 'Count | العدد',
            'desc'       => 'عدد الرحلات التي سيتم عرضها. (Number of tours to show).',
            'default'    => 3,
            'dependency' => array('related_mode', '==', 'auto'),
        ),
        array(
            'id'          => 'related_tours',
            'type'        => 'select',
            'title'       => 'Select Tours | اختر الرحلات',
            'desc'        => 'اختر الرحلات يدوياً. (Manually select related tours).',
            'multiple'    => true,
            'chosen'      => true,
            'ajax'        => true,
            'options'     => 'posts',
            'query_args'  => array(
                'post_type' => 'ytrip_tour',
            ),
            'dependency'  => array('related_mode', '==', 'manual'),
        ),
        array(
            'id'      => 'related_title',
            'type'    => 'text',
            'title'   => 'Section Title | عنوان القسم',
            'default' => 'Related Tours',
            'desc'    => 'العنوان الذي يظهر فوق قسم الرحلات ذات الصلة.',
        ),
    ),
));
