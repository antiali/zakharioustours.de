<?php
/**
 * UTM Settings Class - Codestar Framework Integration
 *
 * Complete admin panel with all options
 */

if (!defined('ABSPATH')) {
    exit;
}

// Set Codestar Framework options
if (!defined('CSF_ACTIVE')) {
    define('CSF_ACTIVE', true);
}

class UTM_Settings {
    
    private static $instance = null;
    private $prefix = 'utm_options';
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'init_codestar_framework'));
    }
    
    public function init_codestar_framework() {
        if (!class_exists('CSF')) {
            return;
        }
        
        $this->create_options();
        $this->create_metaboxes();
        $this->create_taxonomy_options();
        $this->create_profile_options();
        $this->create_shortcode_options();
        $this->create_customizer_options();
    }
    
    /**
     * Create Admin Options Panel
     */
    private function create_options() {
        
        CSF::createOptions($this->prefix, array(
            'menu_title' => __('Tours Settings', 'ultimate-tours-manager'),
            'menu_slug' => 'utm-settings',
            'menu_type' => 'submenu',
            'menu_parent' => 'edit.php?post_type=tour',
            'menu_position' => 99,
            'menu_icon' => 'dashicons-admin-generic',
            'framework_title' => __('Ultimate Tours Manager <small>v2.0</small>', 'ultimate-tours-manager'),
            'framework_class' => 'utm-admin-options',
            'show_bar_menu' => true,
            'show_sub_menu' => true,
            'show_in_network' => false,
            'show_search' => true,
            'show_reset_all' => true,
            'show_reset_section' => true,
            'show_footer' => true,
            'show_all_options' => true,
            'sticky_header' => true,
            'save_defaults' => true,
            'ajax_save' => true,
            'admin_bar_menu_icon' => 'dashicons-admin-site-alt3',
            'admin_bar_menu_priority' => 80,
            'footer_text' => __('Thank you for using Ultimate Tours Manager', 'ultimate-tours-manager'),
            'footer_after' => '<a href="https://calldigitalnow.com" target="_blank">CallDigital</a>',
            'database' => 'option',
            'transient_time' => 0,
            'contextual_help' => array(),
            'contextual_help_sidebar' => '',
            'enqueue_webfont' => true,
            'async_webfont' => false,
            'output_css' => true,
        ));
        
        // =================================================================
        // GENERAL SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'general_settings',
            'title' => __('General Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-cog',
            'fields' => array(
                
                array(
                    'id' => 'enable_tours',
                    'type' => 'switcher',
                    'title' => __('Enable Tours', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable or disable tours functionality', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'tour_slug',
                    'type' => 'text',
                    'title' => __('Tour Slug', 'ultimate-tours-manager'),
                    'subtitle' => __('Custom slug for tour post type', 'ultimate-tours-manager'),
                    'default' => 'tour',
                    'validate' => 'csf_validate_alpha_dash',
                ),
                
                array(
                    'id' => 'destination_slug',
                    'type' => 'text',
                    'title' => __('Destination Slug', 'ultimate-tours-manager'),
                    'subtitle' => __('Custom slug for destination taxonomy', 'ultimate-tours-manager'),
                    'default' => 'destination',
                    'validate' => 'csf_validate_alpha_dash',
                ),
                
                array(
                    'id' => 'items_per_page',
                    'type' => 'spinner',
                    'title' => __('Items Per Page', 'ultimate-tours-manager'),
                    'subtitle' => __('Number of tours to display per page', 'ultimate-tours-manager'),
                    'default' => 12,
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ),
                
                array(
                    'id' => 'date_format',
                    'type' => 'select',
                    'title' => __('Date Format', 'ultimate-tours-manager'),
                    'subtitle' => __('Select date format for tours', 'ultimate-tours-manager'),
                    'options' => array(
                        'Y-m-d' => 'YYYY-MM-DD',
                        'd/m/Y' => 'DD/MM/YYYY',
                        'm/d/Y' => 'MM/DD/YYYY',
                        'd-m-Y' => 'DD-MM-YYYY',
                        'F j, Y' => 'Full Date (January 1, 2024)',
                    ),
                    'default' => 'Y-m-d',
                ),
                
                array(
                    'id' => 'time_format',
                    'type' => 'select',
                    'title' => __('Time Format', 'ultimate-tours-manager'),
                    'subtitle' => __('Select time format', 'ultimate-tours-manager'),
                    'options' => array(
                        'H:i' => '24 Hour (14:30)',
                        'h:i A' => '12 Hour (02:30 PM)',
                    ),
                    'default' => 'H:i',
                ),
                
                array(
                    'id' => 'enable_rtl',
                    'type' => 'switcher',
                    'title' => __('Enable RTL', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable Right-to-Left support', 'ultimate-tours-manager'),
                    'default' => false,
                ),
                
            ),
        ));
        
        // =================================================================
        // CURRENCY & PRICING
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'currency_settings',
            'title' => __('Currency & Pricing', 'ultimate-tours-manager'),
            'icon' => 'fas fa-dollar-sign',
            'fields' => array(
                
                array(
                    'id' => 'currency',
                    'type' => 'select',
                    'title' => __('Currency', 'ultimate-tours-manager'),
                    'subtitle' => __('Select default currency', 'ultimate-tours-manager'),
                    'options' => array(
                        'USD' => 'US Dollar ($)',
                        'EUR' => 'Euro (€)',
                        'GBP' => 'British Pound (£)',
                        'AED' => 'UAE Dirham (د.إ)',
                        'SAR' => 'Saudi Riyal (﷼)',
                        'EGP' => 'Egyptian Pound (E£)',
                        'JPY' => 'Japanese Yen (¥)',
                        'AUD' => 'Australian Dollar (A$)',
                        'CAD' => 'Canadian Dollar (C$)',
                        'CHF' => 'Swiss Franc (CHF)',
                        'CNY' => 'Chinese Yuan (¥)',
                        'INR' => 'Indian Rupee (₹)',
                        'RUB' => 'Russian Ruble (₽)',
                        'TRY' => 'Turkish Lira (₺)',
                    ),
                    'default' => 'USD',
                ),
                
                array(
                    'id' => 'currency_position',
                    'type' => 'button_set',
                    'title' => __('Currency Position', 'ultimate-tours-manager'),
                    'subtitle' => __('Position of currency symbol', 'ultimate-tours-manager'),
                    'options' => array(
                        'left' => __('Left ($99)', 'ultimate-tours-manager'),
                        'right' => __('Right (99$)', 'ultimate-tours-manager'),
                        'left_space' => __('Left with Space ($ 99)', 'ultimate-tours-manager'),
                        'right_space' => __('Right with Space (99 $)', 'ultimate-tours-manager'),
                    ),
                    'default' => 'left',
                ),
                
                array(
                    'id' => 'thousand_separator',
                    'type' => 'text',
                    'title' => __('Thousand Separator', 'ultimate-tours-manager'),
                    'default' => ',',
                ),
                
                array(
                    'id' => 'decimal_separator',
                    'type' => 'text',
                    'title' => __('Decimal Separator', 'ultimate-tours-manager'),
                    'default' => '.',
                ),
                
                array(
                    'id' => 'decimal_places',
                    'type' => 'spinner',
                    'title' => __('Decimal Places', 'ultimate-tours-manager'),
                    'default' => 2,
                    'min' => 0,
                    'max' => 4,
                ),
                
                array(
                    'id' => 'enable_tax',
                    'type' => 'switcher',
                    'title' => __('Enable Tax', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable tax calculation', 'ultimate-tours-manager'),
                    'default' => false,
                ),
                
                array(
                    'id' => 'tax_rate',
                    'type' => 'spinner',
                    'title' => __('Tax Rate (%)', 'ultimate-tours-manager'),
                    'subtitle' => __('Default tax rate percentage', 'ultimate-tours-manager'),
                    'default' => 0,
                    'min' => 0,
                    'max' => 100,
                    'step' => 0.5,
                    'dependency' => array('enable_tax', '==', 'true'),
                ),
                
                array(
                    'id' => 'price_display',
                    'type' => 'button_set',
                    'title' => __('Price Display', 'ultimate-tours-manager'),
                    'options' => array(
                        'per_person' => __('Per Person', 'ultimate-tours-manager'),
                        'per_group' => __('Per Group', 'ultimate-tours-manager'),
                        'starting_from' => __('Starting From', 'ultimate-tours-manager'),
                    ),
                    'default' => 'per_person',
                ),
                
                array(
                    'id' => 'enable_discounts',
                    'type' => 'switcher',
                    'title' => __('Enable Discounts', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable discount functionality', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'discount_types',
                    'type' => 'checkbox',
                    'title' => __('Discount Types', 'ultimate-tours-manager'),
                    'options' => array(
                        'early_bird' => __('Early Bird', 'ultimate-tours-manager'),
                        'last_minute' => __('Last Minute', 'ultimate-tours-manager'),
                        'group' => __('Group Discount', 'ultimate-tours-manager'),
                        'seasonal' => __('Seasonal', 'ultimate-tours-manager'),
                        'coupon' => __('Coupon Code', 'ultimate-tours-manager'),
                    ),
                    'default' => array('early_bird', 'group', 'coupon'),
                    'dependency' => array('enable_discounts', '==', 'true'),
                ),
                
            ),
        ));
        
        // =================================================================
        // BOOKING SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'booking_settings',
            'title' => __('Booking Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-calendar-check',
            'fields' => array(
                
                array(
                    'id' => 'enable_booking',
                    'type' => 'switcher',
                    'title' => __('Enable Online Booking', 'ultimate-tours-manager'),
                    'subtitle' => __('Allow customers to book tours online', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'booking_type',
                    'type' => 'button_set',
                    'title' => __('Booking Type', 'ultimate-tours-manager'),
                    'options' => array(
                        'instant' => __('Instant Booking', 'ultimate-tours-manager'),
                        'request' => __('Request Only', 'ultimate-tours-manager'),
                        'enquiry' => __('Enquiry Form', 'ultimate-tours-manager'),
                    ),
                    'default' => 'instant',
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'require_login',
                    'type' => 'switcher',
                    'title' => __('Require Login', 'ultimate-tours-manager'),
                    'subtitle' => __('Require users to login before booking', 'ultimate-tours-manager'),
                    'default' => false,
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'auto_confirm',
                    'type' => 'switcher',
                    'title' => __('Auto Confirm Booking', 'ultimate-tours-manager'),
                    'subtitle' => __('Automatically confirm bookings after payment', 'ultimate-tours-manager'),
                    'default' => true,
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'booking_form_fields',
                    'type' => 'group',
                    'title' => __('Booking Form Fields', 'ultimate-tours-manager'),
                    'subtitle' => __('Configure booking form fields', 'ultimate-tours-manager'),
                    'button_title' => __('Add Field', 'ultimate-tours-manager'),
                    'accordion_title_prefix' => __('Field:', 'ultimate-tours-manager'),
                    'accordion_title_number' => true,
                    'fields' => array(
                        array(
                            'id' => 'field_label',
                            'type' => 'text',
                            'title' => __('Field Label', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'field_type',
                            'type' => 'select',
                            'title' => __('Field Type', 'ultimate-tours-manager'),
                            'options' => array(
                                'text' => 'Text',
                                'email' => 'Email',
                                'tel' => 'Phone',
                                'number' => 'Number',
                                'textarea' => 'Textarea',
                                'select' => 'Select',
                                'checkbox' => 'Checkbox',
                                'radio' => 'Radio',
                                'date' => 'Date',
                                'time' => 'Time',
                            ),
                        ),
                        array(
                            'id' => 'field_required',
                            'type' => 'switcher',
                            'title' => __('Required', 'ultimate-tours-manager'),
                            'default' => false,
                        ),
                        array(
                            'id' => 'field_placeholder',
                            'type' => 'text',
                            'title' => __('Placeholder', 'ultimate-tours-manager'),
                        ),
                    ),
                    'default' => array(
                        array('field_label' => 'First Name', 'field_type' => 'text', 'field_required' => true),
                        array('field_label' => 'Last Name', 'field_type' => 'text', 'field_required' => true),
                        array('field_label' => 'Email', 'field_type' => 'email', 'field_required' => true),
                        array('field_label' => 'Phone', 'field_type' => 'tel', 'field_required' => true),
                    ),
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'min_advance_booking',
                    'type' => 'spinner',
                    'title' => __('Minimum Advance Booking (Hours)', 'ultimate-tours-manager'),
                    'subtitle' => __('Minimum hours before tour start to accept booking', 'ultimate-tours-manager'),
                    'default' => 24,
                    'min' => 0,
                    'max' => 720,
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'max_advance_booking',
                    'type' => 'spinner',
                    'title' => __('Maximum Advance Booking (Days)', 'ultimate-tours-manager'),
                    'subtitle' => __('Maximum days in advance to accept booking', 'ultimate-tours-manager'),
                    'default' => 365,
                    'min' => 1,
                    'max' => 730,
                    'dependency' => array('enable_booking', '==', 'true'),
                ),
                
                array(
                    'id' => 'cancellation_policy',
                    'type' => 'wp_editor',
                    'title' => __('Cancellation Policy', 'ultimate-tours-manager'),
                    'subtitle' => __('Default cancellation policy text', 'ultimate-tours-manager'),
                    'default' => __('Full refund if cancelled 7 days before the tour. 50% refund if cancelled 3 days before. No refund for cancellations less than 3 days before.', 'ultimate-tours-manager'),
                ),
                
                array(
                    'id' => 'terms_conditions',
                    'type' => 'wp_editor',
                    'title' => __('Booking Terms & Conditions', 'ultimate-tours-manager'),
                    'subtitle' => __('Terms and conditions for bookings', 'ultimate-tours-manager'),
                ),
                
            ),
        ));
        
        // =================================================================
        // PAYMENT SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'payment_settings',
            'title' => __('Payment Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-credit-card',
            'fields' => array(
                
                array(
                    'id' => 'payment_gateways',
                    'type' => 'checkbox',
                    'title' => __('Payment Gateways', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable payment gateways', 'ultimate-tours-manager'),
                    'options' => array(
                        'woocommerce' => __('WooCommerce', 'ultimate-tours-manager'),
                        'paypal' => __('PayPal', 'ultimate-tours-manager'),
                        'stripe' => __('Stripe', 'ultimate-tours-manager'),
                        'bank_transfer' => __('Bank Transfer', 'ultimate-tours-manager'),
                        'cash' => __('Cash on Arrival', 'ultimate-tours-manager'),
                    ),
                    'default' => array('woocommerce'),
                ),
                
                array(
                    'id' => 'paypal_settings',
                    'type' => 'fieldset',
                    'title' => __('PayPal Settings', 'ultimate-tours-manager'),
                    'dependency' => array('payment_gateways', 'any', 'paypal'),
                    'fields' => array(
                        array(
                            'id' => 'paypal_mode',
                            'type' => 'button_set',
                            'title' => __('Mode', 'ultimate-tours-manager'),
                            'options' => array(
                                'sandbox' => 'Sandbox',
                                'live' => 'Live',
                            ),
                            'default' => 'sandbox',
                        ),
                        array(
                            'id' => 'paypal_client_id',
                            'type' => 'text',
                            'title' => __('Client ID', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'paypal_secret',
                            'type' => 'text',
                            'title' => __('Secret Key', 'ultimate-tours-manager'),
                            'attributes' => array('type' => 'password'),
                        ),
                    ),
                ),
                
                array(
                    'id' => 'stripe_settings',
                    'type' => 'fieldset',
                    'title' => __('Stripe Settings', 'ultimate-tours-manager'),
                    'dependency' => array('payment_gateways', 'any', 'stripe'),
                    'fields' => array(
                        array(
                            'id' => 'stripe_mode',
                            'type' => 'button_set',
                            'title' => __('Mode', 'ultimate-tours-manager'),
                            'options' => array(
                                'test' => 'Test',
                                'live' => 'Live',
                            ),
                            'default' => 'test',
                        ),
                        array(
                            'id' => 'stripe_publishable_key',
                            'type' => 'text',
                            'title' => __('Publishable Key', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'stripe_secret_key',
                            'type' => 'text',
                            'title' => __('Secret Key', 'ultimate-tours-manager'),
                            'attributes' => array('type' => 'password'),
                        ),
                    ),
                ),
                
                array(
                    'id' => 'bank_details',
                    'type' => 'fieldset',
                    'title' => __('Bank Transfer Details', 'ultimate-tours-manager'),
                    'dependency' => array('payment_gateways', 'any', 'bank_transfer'),
                    'fields' => array(
                        array(
                            'id' => 'bank_name',
                            'type' => 'text',
                            'title' => __('Bank Name', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'account_name',
                            'type' => 'text',
                            'title' => __('Account Name', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'account_number',
                            'type' => 'text',
                            'title' => __('Account Number', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'iban',
                            'type' => 'text',
                            'title' => __('IBAN', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'swift_code',
                            'type' => 'text',
                            'title' => __('SWIFT/BIC Code', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
                
                array(
                    'id' => 'deposit_settings',
                    'type' => 'fieldset',
                    'title' => __('Deposit Settings', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'enable_deposit',
                            'type' => 'switcher',
                            'title' => __('Enable Deposit', 'ultimate-tours-manager'),
                            'default' => false,
                        ),
                        array(
                            'id' => 'deposit_type',
                            'type' => 'button_set',
                            'title' => __('Deposit Type', 'ultimate-tours-manager'),
                            'options' => array(
                                'percentage' => __('Percentage', 'ultimate-tours-manager'),
                                'fixed' => __('Fixed Amount', 'ultimate-tours-manager'),
                            ),
                            'default' => 'percentage',
                            'dependency' => array('enable_deposit', '==', 'true'),
                        ),
                        array(
                            'id' => 'deposit_amount',
                            'type' => 'spinner',
                            'title' => __('Deposit Amount', 'ultimate-tours-manager'),
                            'default' => 30,
                            'min' => 1,
                            'max' => 100,
                            'dependency' => array('enable_deposit', '==', 'true'),
                        ),
                    ),
                ),
                
            ),
        ));
        
        // =================================================================
        // EMAIL NOTIFICATIONS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'email_settings',
            'title' => __('Email Notifications', 'ultimate-tours-manager'),
            'icon' => 'fas fa-envelope',
            'fields' => array(
                
                array(
                    'id' => 'enable_emails',
                    'type' => 'switcher',
                    'title' => __('Enable Email Notifications', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'admin_email',
                    'type' => 'text',
                    'title' => __('Admin Email', 'ultimate-tours-manager'),
                    'subtitle' => __('Email address for admin notifications', 'ultimate-tours-manager'),
                    'default' => get_option('admin_email'),
                    'validate' => 'csf_validate_email',
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'from_name',
                    'type' => 'text',
                    'title' => __('From Name', 'ultimate-tours-manager'),
                    'default' => get_bloginfo('name'),
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'from_email',
                    'type' => 'text',
                    'title' => __('From Email', 'ultimate-tours-manager'),
                    'default' => get_option('admin_email'),
                    'validate' => 'csf_validate_email',
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'email_template',
                    'type' => 'button_set',
                    'title' => __('Email Template', 'ultimate-tours-manager'),
                    'options' => array(
                        'default' => __('Default', 'ultimate-tours-manager'),
                        'modern' => __('Modern', 'ultimate-tours-manager'),
                        'minimal' => __('Minimal', 'ultimate-tours-manager'),
                    ),
                    'default' => 'modern',
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'email_logo',
                    'type' => 'media',
                    'title' => __('Email Logo', 'ultimate-tours-manager'),
                    'library' => 'image',
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'email_header_color',
                    'type' => 'color',
                    'title' => __('Email Header Color', 'ultimate-tours-manager'),
                    'default' => '#0073aa',
                    'dependency' => array('enable_emails', '==', 'true'),
                ),
                
                array(
                    'id' => 'booking_confirmed_email',
                    'type' => 'fieldset',
                    'title' => __('Booking Confirmed Email', 'ultimate-tours-manager'),
                    'dependency' => array('enable_emails', '==', 'true'),
                    'fields' => array(
                        array(
                            'id' => 'enabled',
                            'type' => 'switcher',
                            'title' => __('Enable', 'ultimate-tours-manager'),
                            'default' => true,
                        ),
                        array(
                            'id' => 'subject',
                            'type' => 'text',
                            'title' => __('Subject', 'ultimate-tours-manager'),
                            'default' => __('Your booking #{booking_id} has been confirmed', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'content',
                            'type' => 'wp_editor',
                            'title' => __('Content', 'ultimate-tours-manager'),
                            'default' => __('Dear {customer_name},

Your booking for {tour_name} has been confirmed.

Booking Details:
- Booking Number: #{booking_id}
- Tour: {tour_name}
- Date: {booking_date}
- Guests: {guests}
- Total: {total_price}

Thank you for choosing us!', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
                
                array(
                    'id' => 'booking_reminder_email',
                    'type' => 'fieldset',
                    'title' => __('Booking Reminder Email', 'ultimate-tours-manager'),
                    'dependency' => array('enable_emails', '==', 'true'),
                    'fields' => array(
                        array(
                            'id' => 'enabled',
                            'type' => 'switcher',
                            'title' => __('Enable', 'ultimate-tours-manager'),
                            'default' => true,
                        ),
                        array(
                            'id' => 'days_before',
                            'type' => 'spinner',
                            'title' => __('Days Before', 'ultimate-tours-manager'),
                            'default' => 1,
                            'min' => 1,
                            'max' => 30,
                        ),
                        array(
                            'id' => 'subject',
                            'type' => 'text',
                            'title' => __('Subject', 'ultimate-tours-manager'),
                            'default' => __('Reminder: Your tour is coming up!', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'content',
                            'type' => 'wp_editor',
                            'title' => __('Content', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
                
            ),
        ));
        
        // =================================================================
        // DESIGN & APPEARANCE
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'design_settings',
            'title' => __('Design & Appearance', 'ultimate-tours-manager'),
            'icon' => 'fas fa-paint-brush',
            'fields' => array(
                
                array(
                    'id' => 'primary_color',
                    'type' => 'color',
                    'title' => __('Primary Color', 'ultimate-tours-manager'),
                    'default' => '#0073aa',
                    'output' => ':root { --utm-primary: $; }',
                ),
                
                array(
                    'id' => 'secondary_color',
                    'type' => 'color',
                    'title' => __('Secondary Color', 'ultimate-tours-manager'),
                    'default' => '#23282d',
                    'output' => ':root { --utm-secondary: $; }',
                ),
                
                array(
                    'id' => 'accent_color',
                    'type' => 'color',
                    'title' => __('Accent Color', 'ultimate-tours-manager'),
                    'default' => '#ffc107',
                    'output' => ':root { --utm-accent: $; }',
                ),
                
                array(
                    'id' => 'tour_card_style',
                    'type' => 'image_select',
                    'title' => __('Tour Card Style', 'ultimate-tours-manager'),
                    'options' => array(
                        'style1' => UTM_PLUGIN_URL . 'assets/images/card-style-1.png',
                        'style2' => UTM_PLUGIN_URL . 'assets/images/card-style-2.png',
                        'style3' => UTM_PLUGIN_URL . 'assets/images/card-style-3.png',
                        'style4' => UTM_PLUGIN_URL . 'assets/images/card-style-4.png',
                    ),
                    'default' => 'style1',
                ),
                
                array(
                    'id' => 'tour_columns',
                    'type' => 'button_set',
                    'title' => __('Tour Grid Columns', 'ultimate-tours-manager'),
                    'options' => array(
                        '2' => '2 Columns',
                        '3' => '3 Columns',
                        '4' => '4 Columns',
                    ),
                    'default' => '3',
                ),
                
                array(
                    'id' => 'enable_animations',
                    'type' => 'switcher',
                    'title' => __('Enable Animations', 'ultimate-tours-manager'),
                    'subtitle' => __('Enable CSS animations', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'custom_css',
                    'type' => 'code_editor',
                    'title' => __('Custom CSS', 'ultimate-tours-manager'),
                    'subtitle' => __('Add custom CSS styles', 'ultimate-tours-manager'),
                    'settings' => array(
                        'theme' => 'monokai',
                        'mode' => 'css',
                    ),
                ),
                
                array(
                    'id' => 'gallery_columns',
                    'type' => 'slider',
                    'title' => __('Gallery Columns', 'ultimate-tours-manager'),
                    'default' => 4,
                    'min' => 2,
                    'max' => 6,
                    'step' => 1,
                ),
                
                array(
                    'id' => 'enable_lightbox',
                    'type' => 'switcher',
                    'title' => __('Enable Lightbox', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
            ),
        ));
        
        // =================================================================
        // MAP SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'map_settings',
            'title' => __('Map Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-map-marker-alt',
            'fields' => array(
                
                array(
                    'id' => 'enable_maps',
                    'type' => 'switcher',
                    'title' => __('Enable Maps', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'map_provider',
                    'type' => 'button_set',
                    'title' => __('Map Provider', 'ultimate-tours-manager'),
                    'options' => array(
                        'google' => 'Google Maps',
                        'openstreetmap' => 'OpenStreetMap',
                        'mapbox' => 'Mapbox',
                    ),
                    'default' => 'google',
                    'dependency' => array('enable_maps', '==', 'true'),
                ),
                
                array(
                    'id' => 'google_maps_api_key',
                    'type' => 'text',
                    'title' => __('Google Maps API Key', 'ultimate-tours-manager'),
                    'dependency' => array(
                        array('enable_maps', '==', 'true'),
                        array('map_provider', '==', 'google'),
                    ),
                ),
                
                array(
                    'id' => 'mapbox_access_token',
                    'type' => 'text',
                    'title' => __('Mapbox Access Token', 'ultimate-tours-manager'),
                    'dependency' => array(
                        array('enable_maps', '==', 'true'),
                        array('map_provider', '==', 'mapbox'),
                    ),
                ),
                
                array(
                    'id' => 'default_map_center',
                    'type' => 'map',
                    'title' => __('Default Map Center', 'ultimate-tours-manager'),
                    'dependency' => array('enable_maps', '==', 'true'),
                    'default' => array(
                        'latitude' => '30.0444',
                        'longitude' => '31.2357',
                        'zoom' => '10',
                    ),
                ),
                
                array(
                    'id' => 'map_style',
                    'type' => 'select',
                    'title' => __('Map Style', 'ultimate-tours-manager'),
                    'options' => array(
                        'standard' => 'Standard',
                        'silver' => 'Silver',
                        'retro' => 'Retro',
                        'dark' => 'Dark',
                        'night' => 'Night',
                        'aubergine' => 'Aubergine',
                    ),
                    'default' => 'standard',
                    'dependency' => array('enable_maps', '==', 'true'),
                ),
                
                array(
                    'id' => 'custom_marker',
                    'type' => 'media',
                    'title' => __('Custom Map Marker', 'ultimate-tours-manager'),
                    'library' => 'image',
                    'dependency' => array('enable_maps', '==', 'true'),
                ),
                
            ),
        ));
        
        // =================================================================
        // SEO SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'seo_settings',
            'title' => __('SEO Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-search',
            'fields' => array(
                
                array(
                    'id' => 'enable_seo',
                    'type' => 'switcher',
                    'title' => __('Enable SEO Features', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'enable_schema',
                    'type' => 'switcher',
                    'title' => __('Enable Schema Markup', 'ultimate-tours-manager'),
                    'subtitle' => __('Add JSON-LD schema for tours', 'ultimate-tours-manager'),
                    'default' => true,
                    'dependency' => array('enable_seo', '==', 'true'),
                ),
                
                array(
                    'id' => 'schema_type',
                    'type' => 'select',
                    'title' => __('Schema Type', 'ultimate-tours-manager'),
                    'options' => array(
                        'TouristTrip' => 'TouristTrip',
                        'Product' => 'Product',
                        'Event' => 'Event',
                        'TravelAction' => 'TravelAction',
                    ),
                    'default' => 'TouristTrip',
                    'dependency' => array(
                        array('enable_seo', '==', 'true'),
                        array('enable_schema', '==', 'true'),
                    ),
                ),
                
                array(
                    'id' => 'enable_og_tags',
                    'type' => 'switcher',
                    'title' => __('Enable Open Graph Tags', 'ultimate-tours-manager'),
                    'default' => true,
                    'dependency' => array('enable_seo', '==', 'true'),
                ),
                
                array(
                    'id' => 'enable_twitter_cards',
                    'type' => 'switcher',
                    'title' => __('Enable Twitter Cards', 'ultimate-tours-manager'),
                    'default' => true,
                    'dependency' => array('enable_seo', '==', 'true'),
                ),
                
                array(
                    'id' => 'tour_title_format',
                    'type' => 'text',
                    'title' => __('Tour Title Format', 'ultimate-tours-manager'),
                    'subtitle' => __('Use {title}, {destination}, {site_name}', 'ultimate-tours-manager'),
                    'default' => '{title} | {destination} | {site_name}',
                    'dependency' => array('enable_seo', '==', 'true'),
                ),
                
                array(
                    'id' => 'default_meta_description',
                    'type' => 'textarea',
                    'title' => __('Default Meta Description', 'ultimate-tours-manager'),
                    'subtitle' => __('Use {title}, {excerpt}, {destination}', 'ultimate-tours-manager'),
                    'default' => '{excerpt}',
                    'dependency' => array('enable_seo', '==', 'true'),
                ),
                
            ),
        ));
        
        // =================================================================
        // REVIEWS SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'review_settings',
            'title' => __('Reviews Settings', 'ultimate-tours-manager'),
            'icon' => 'fas fa-star',
            'fields' => array(
                
                array(
                    'id' => 'enable_reviews',
                    'type' => 'switcher',
                    'title' => __('Enable Reviews', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'review_approval',
                    'type' => 'button_set',
                    'title' => __('Review Approval', 'ultimate-tours-manager'),
                    'options' => array(
                        'auto' => __('Auto Approve', 'ultimate-tours-manager'),
                        'manual' => __('Manual Approval', 'ultimate-tours-manager'),
                        'verified' => __('Verified Purchases Only', 'ultimate-tours-manager'),
                    ),
                    'default' => 'manual',
                    'dependency' => array('enable_reviews', '==', 'true'),
                ),
                
                array(
                    'id' => 'review_rating_criteria',
                    'type' => 'repeater',
                    'title' => __('Rating Criteria', 'ultimate-tours-manager'),
                    'button_title' => __('Add Criterion', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'name',
                            'type' => 'text',
                            'title' => __('Name', 'ultimate-tours-manager'),
                        ),
                    ),
                    'default' => array(
                        array('name' => 'Service'),
                        array('name' => 'Value for Money'),
                        array('name' => 'Location'),
                        array('name' => 'Guide'),
                    ),
                    'dependency' => array('enable_reviews', '==', 'true'),
                ),
                
                array(
                    'id' => 'allow_photos',
                    'type' => 'switcher',
                    'title' => __('Allow Photo Reviews', 'ultimate-tours-manager'),
                    'default' => true,
                    'dependency' => array('enable_reviews', '==', 'true'),
                ),
                
                array(
                    'id' => 'reviews_per_page',
                    'type' => 'spinner',
                    'title' => __('Reviews Per Page', 'ultimate-tours-manager'),
                    'default' => 10,
                    'min' => 5,
                    'max' => 50,
                    'dependency' => array('enable_reviews', '==', 'true'),
                ),
                
            ),
        ));
        
        // =================================================================
        // WOOCOMMERCE INTEGRATION
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'woocommerce_settings',
            'title' => __('WooCommerce', 'ultimate-tours-manager'),
            'icon' => 'fas fa-shopping-cart',
            'fields' => array(
                
                array(
                    'id' => 'enable_woocommerce',
                    'type' => 'switcher',
                    'title' => __('Enable WooCommerce Integration', 'ultimate-tours-manager'),
                    'subtitle' => __('Use WooCommerce for checkout', 'ultimate-tours-manager'),
                    'default' => class_exists('WooCommerce'),
                ),
                
                array(
                    'id' => 'sync_inventory',
                    'type' => 'switcher',
                    'title' => __('Sync Inventory', 'ultimate-tours-manager'),
                    'subtitle' => __('Sync tour availability with WooCommerce products', 'ultimate-tours-manager'),
                    'default' => false,
                    'dependency' => array('enable_woocommerce', '==', 'true'),
                ),
                
                array(
                    'id' => 'product_type',
                    'type' => 'select',
                    'title' => __('Product Type', 'ultimate-tours-manager'),
                    'options' => array(
                        'simple' => 'Simple Product',
                        'variable' => 'Variable Product',
                        'booking' => 'Booking Product',
                    ),
                    'default' => 'simple',
                    'dependency' => array('enable_woocommerce', '==', 'true'),
                ),
                
                array(
                    'id' => 'redirect_after_add',
                    'type' => 'select',
                    'title' => __('Redirect After Add to Cart', 'ultimate-tours-manager'),
                    'options' => array(
                        'cart' => __('Cart Page', 'ultimate-tours-manager'),
                        'checkout' => __('Checkout Page', 'ultimate-tours-manager'),
                        'stay' => __('Stay on Page', 'ultimate-tours-manager'),
                    ),
                    'default' => 'checkout',
                    'dependency' => array('enable_woocommerce', '==', 'true'),
                ),
                
            ),
        ));
        
        // =================================================================
        // IMPORT / EXPORT
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'import_export',
            'title' => __('Import / Export', 'ultimate-tours-manager'),
            'icon' => 'fas fa-exchange-alt',
            'fields' => array(
                
                array(
                    'type' => 'heading',
                    'content' => __('Export Tours', 'ultimate-tours-manager'),
                ),
                
                array(
                    'id' => 'export_format',
                    'type' => 'button_set',
                    'title' => __('Export Format', 'ultimate-tours-manager'),
                    'options' => array(
                        'json' => 'JSON',
                        'csv' => 'CSV',
                        'xml' => 'XML',
                    ),
                    'default' => 'json',
                ),
                
                array(
                    'id' => 'export_button',
                    'type' => 'callback',
                    'function' => 'utm_export_button_callback',
                ),
                
                array(
                    'type' => 'heading',
                    'content' => __('Import Tours', 'ultimate-tours-manager'),
                ),
                
                array(
                    'id' => 'import_file',
                    'type' => 'upload',
                    'title' => __('Import File', 'ultimate-tours-manager'),
                    'library' => 'application/json,text/csv,text/xml',
                    'button_title' => __('Select File', 'ultimate-tours-manager'),
                ),
                
                array(
                    'id' => 'import_button',
                    'type' => 'callback',
                    'function' => 'utm_import_button_callback',
                ),
                
                array(
                    'type' => 'heading',
                    'content' => __('Backup Settings', 'ultimate-tours-manager'),
                ),
                
                array(
                    'id' => 'backup_settings',
                    'type' => 'backup',
                ),
                
            ),
        ));
        
        // =================================================================
        // ADVANCED SETTINGS
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'advanced_settings',
            'title' => __('Advanced', 'ultimate-tours-manager'),
            'icon' => 'fas fa-cogs',
            'fields' => array(
                
                array(
                    'id' => 'enable_cache',
                    'type' => 'switcher',
                    'title' => __('Enable Caching', 'ultimate-tours-manager'),
                    'subtitle' => __('Cache tour queries for better performance', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'cache_duration',
                    'type' => 'spinner',
                    'title' => __('Cache Duration (Hours)', 'ultimate-tours-manager'),
                    'default' => 24,
                    'min' => 1,
                    'max' => 168,
                    'dependency' => array('enable_cache', '==', 'true'),
                ),
                
                array(
                    'id' => 'clear_cache_button',
                    'type' => 'callback',
                    'function' => 'utm_clear_cache_button_callback',
                ),
                
                array(
                    'id' => 'enable_lazy_load',
                    'type' => 'switcher',
                    'title' => __('Enable Lazy Loading', 'ultimate-tours-manager'),
                    'subtitle' => __('Lazy load images for better performance', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'enable_ajax_filter',
                    'type' => 'switcher',
                    'title' => __('Enable AJAX Filtering', 'ultimate-tours-manager'),
                    'subtitle' => __('Filter tours without page reload', 'ultimate-tours-manager'),
                    'default' => true,
                ),
                
                array(
                    'id' => 'enable_debug',
                    'type' => 'switcher',
                    'title' => __('Enable Debug Mode', 'ultimate-tours-manager'),
                    'subtitle' => __('Log errors and debug information', 'ultimate-tours-manager'),
                    'default' => false,
                ),
                
                array(
                    'id' => 'uninstall_data',
                    'type' => 'switcher',
                    'title' => __('Delete Data on Uninstall', 'ultimate-tours-manager'),
                    'subtitle' => __('Remove all plugin data when uninstalling', 'ultimate-tours-manager'),
                    'default' => false,
                ),
                
                array(
                    'id' => 'custom_js',
                    'type' => 'code_editor',
                    'title' => __('Custom JavaScript', 'ultimate-tours-manager'),
                    'settings' => array(
                        'theme' => 'monokai',
                        'mode' => 'javascript',
                    ),
                ),
                
            ),
        ));
        
        // =================================================================
        // LICENSE & UPDATES
        // =================================================================
        CSF::createSection($this->prefix, array(
            'id' => 'license_settings',
            'title' => __('License', 'ultimate-tours-manager'),
            'icon' => 'fas fa-key',
            'fields' => array(
                
                array(
                    'type' => 'content',
                    'content' => '<div class="utm-license-info">
                        <h3>' . __('Ultimate Tours Manager Pro', 'ultimate-tours-manager') . '</h3>
                        <p>' . __('Enter your license key to receive updates and support.', 'ultimate-tours-manager') . '</p>
                    </div>',
                ),
                
                array(
                    'id' => 'license_key',
                    'type' => 'text',
                    'title' => __('License Key', 'ultimate-tours-manager'),
                    'placeholder' => 'XXXX-XXXX-XXXX-XXXX',
                ),
                
                array(
                    'id' => 'license_status',
                    'type' => 'callback',
                    'function' => 'utm_license_status_callback',
                ),
                
            ),
        ));
        
    }
    
    /**
     * Create Tour Metaboxes
     */
    private function create_metaboxes() {
        if (!class_exists('CSF')) {
            return;
        }
        
        $prefix = 'utm_tour_meta';
        
        CSF::createMetabox($prefix, array(
            'title' => __('Tour Settings', 'ultimate-tours-manager'),
            'post_type' => 'tour',
            'data_type' => 'unserialize',
            'context' => 'normal',
            'priority' => 'high',
            'show_restore' => true,
            'enqueue_webfont' => true,
            'async_webfont' => false,
            'nav' => 'inline',
            'theme' => 'light',
        ));
        
        // Pricing Section
        CSF::createSection($prefix, array(
            'title' => __('Pricing', 'ultimate-tours-manager'),
            'icon' => 'fas fa-dollar-sign',
            'fields' => array(
                array(
                    'id' => 'price',
                    'type' => 'number',
                    'title' => __('Regular Price', 'ultimate-tours-manager'),
                    'unit' => '$',
                ),
                array(
                    'id' => 'sale_price',
                    'type' => 'number',
                    'title' => __('Sale Price', 'ultimate-tours-manager'),
                    'unit' => '$',
                ),
                array(
                    'id' => 'child_price',
                    'type' => 'number',
                    'title' => __('Child Price', 'ultimate-tours-manager'),
                    'unit' => '$',
                ),
                array(
                    'id' => 'infant_price',
                    'type' => 'number',
                    'title' => __('Infant Price', 'ultimate-tours-manager'),
                    'unit' => '$',
                    'default' => 0,
                ),
                array(
                    'id' => 'group_discount',
                    'type' => 'fieldset',
                    'title' => __('Group Discount', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'min_guests',
                            'type' => 'number',
                            'title' => __('Min Guests', 'ultimate-tours-manager'),
                            'default' => 5,
                        ),
                        array(
                            'id' => 'discount',
                            'type' => 'number',
                            'title' => __('Discount %', 'ultimate-tours-manager'),
                            'default' => 10,
                        ),
                    ),
                ),
            ),
        ));
        
        // Duration & Schedule
        CSF::createSection($prefix, array(
            'title' => __('Duration & Schedule', 'ultimate-tours-manager'),
            'icon' => 'fas fa-clock',
            'fields' => array(
                array(
                    'id' => 'duration_value',
                    'type' => 'number',
                    'title' => __('Duration', 'ultimate-tours-manager'),
                    'default' => 1,
                ),
                array(
                    'id' => 'duration_unit',
                    'type' => 'select',
                    'title' => __('Duration Unit', 'ultimate-tours-manager'),
                    'options' => array(
                        'hours' => __('Hours', 'ultimate-tours-manager'),
                        'days' => __('Days', 'ultimate-tours-manager'),
                        'nights' => __('Nights', 'ultimate-tours-manager'),
                    ),
                    'default' => 'days',
                ),
                array(
                    'id' => 'start_time',
                    'type' => 'text',
                    'title' => __('Start Time', 'ultimate-tours-manager'),
                    'placeholder' => '09:00',
                ),
                array(
                    'id' => 'end_time',
                    'type' => 'text',
                    'title' => __('End Time', 'ultimate-tours-manager'),
                    'placeholder' => '17:00',
                ),
                array(
                    'id' => 'available_dates',
                    'type' => 'date',
                    'title' => __('Available Dates', 'ultimate-tours-manager'),
                    'settings' => array(
                        'mode' => 'multiple',
                        'dateFormat' => 'Y-m-d',
                    ),
                ),
                array(
                    'id' => 'availability',
                    'type' => 'checkbox',
                    'title' => __('Available Days', 'ultimate-tours-manager'),
                    'options' => array(
                        'sun' => __('Sunday', 'ultimate-tours-manager'),
                        'mon' => __('Monday', 'ultimate-tours-manager'),
                        'tue' => __('Tuesday', 'ultimate-tours-manager'),
                        'wed' => __('Wednesday', 'ultimate-tours-manager'),
                        'thu' => __('Thursday', 'ultimate-tours-manager'),
                        'fri' => __('Friday', 'ultimate-tours-manager'),
                        'sat' => __('Saturday', 'ultimate-tours-manager'),
                    ),
                    'inline' => true,
                    'default' => array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'),
                ),
                array(
                    'id' => 'max_guests',
                    'type' => 'number',
                    'title' => __('Maximum Guests', 'ultimate-tours-manager'),
                    'default' => 20,
                ),
                array(
                    'id' => 'min_guests',
                    'type' => 'number',
                    'title' => __('Minimum Guests', 'ultimate-tours-manager'),
                    'default' => 1,
                ),
            ),
        ));
        
        // Gallery
        CSF::createSection($prefix, array(
            'title' => __('Gallery', 'ultimate-tours-manager'),
            'icon' => 'fas fa-images',
            'fields' => array(
                array(
                    'id' => 'gallery',
                    'type' => 'gallery',
                    'title' => __('Tour Gallery', 'ultimate-tours-manager'),
                    'add_title' => __('Add Images', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'video_url',
                    'type' => 'text',
                    'title' => __('Video URL', 'ultimate-tours-manager'),
                    'subtitle' => __('YouTube or Vimeo URL', 'ultimate-tours-manager'),
                ),
            ),
        ));
        
        // Itinerary
        CSF::createSection($prefix, array(
            'title' => __('Itinerary', 'ultimate-tours-manager'),
            'icon' => 'fas fa-route',
            'fields' => array(
                array(
                    'id' => 'itinerary',
                    'type' => 'group',
                    'title' => __('Tour Itinerary', 'ultimate-tours-manager'),
                    'button_title' => __('Add Day', 'ultimate-tours-manager'),
                    'accordion_title_prefix' => __('Day', 'ultimate-tours-manager'),
                    'accordion_title_number' => true,
                    'fields' => array(
                        array(
                            'id' => 'title',
                            'type' => 'text',
                            'title' => __('Day Title', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'description',
                            'type' => 'wp_editor',
                            'title' => __('Description', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'image',
                            'type' => 'media',
                            'title' => __('Day Image', 'ultimate-tours-manager'),
                            'library' => 'image',
                        ),
                        array(
                            'id' => 'meals',
                            'type' => 'checkbox',
                            'title' => __('Meals', 'ultimate-tours-manager'),
                            'options' => array(
                                'breakfast' => __('Breakfast', 'ultimate-tours-manager'),
                                'lunch' => __('Lunch', 'ultimate-tours-manager'),
                                'dinner' => __('Dinner', 'ultimate-tours-manager'),
                            ),
                            'inline' => true,
                        ),
                        array(
                            'id' => 'accommodation',
                            'type' => 'text',
                            'title' => __('Accommodation', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
            ),
        ));
        
        // Inclusions & Exclusions
        CSF::createSection($prefix, array(
            'title' => __('Inclusions', 'ultimate-tours-manager'),
            'icon' => 'fas fa-check-circle',
            'fields' => array(
                array(
                    'id' => 'inclusions',
                    'type' => 'repeater',
                    'title' => __('Inclusions', 'ultimate-tours-manager'),
                    'button_title' => __('Add Item', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'item',
                            'type' => 'text',
                            'title' => __('Item', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
                array(
                    'id' => 'exclusions',
                    'type' => 'repeater',
                    'title' => __('Exclusions', 'ultimate-tours-manager'),
                    'button_title' => __('Add Item', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'item',
                            'type' => 'text',
                            'title' => __('Item', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
            ),
        ));
        
        // Location & Map
        CSF::createSection($prefix, array(
            'title' => __('Location', 'ultimate-tours-manager'),
            'icon' => 'fas fa-map-marker-alt',
            'fields' => array(
                array(
                    'id' => 'location',
                    'type' => 'map',
                    'title' => __('Tour Location', 'ultimate-tours-manager'),
                    'default' => array(
                        'latitude' => '30.0444',
                        'longitude' => '31.2357',
                        'zoom' => '12',
                    ),
                ),
                array(
                    'id' => 'address',
                    'type' => 'textarea',
                    'title' => __('Address', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'meeting_point',
                    'type' => 'text',
                    'title' => __('Meeting Point', 'ultimate-tours-manager'),
                ),
            ),
        ));
        
        // FAQs
        CSF::createSection($prefix, array(
            'title' => __('FAQs', 'ultimate-tours-manager'),
            'icon' => 'fas fa-question-circle',
            'fields' => array(
                array(
                    'id' => 'faqs',
                    'type' => 'group',
                    'title' => __('FAQs', 'ultimate-tours-manager'),
                    'button_title' => __('Add FAQ', 'ultimate-tours-manager'),
                    'fields' => array(
                        array(
                            'id' => 'question',
                            'type' => 'text',
                            'title' => __('Question', 'ultimate-tours-manager'),
                        ),
                        array(
                            'id' => 'answer',
                            'type' => 'textarea',
                            'title' => __('Answer', 'ultimate-tours-manager'),
                        ),
                    ),
                ),
            ),
        ));
        
    }
    
    /**
     * Create Taxonomy Options
     */
    private function create_taxonomy_options() {
        if (!class_exists('CSF')) {
            return;
        }
        
        $prefix = 'utm_taxonomy_meta';
        
        CSF::createTaxonomyOptions($prefix, array(
            'taxonomy' => 'destination',
            'data_type' => 'serialize',
        ));
        
        CSF::createSection($prefix, array(
            'fields' => array(
                array(
                    'id' => 'image',
                    'type' => 'media',
                    'title' => __('Destination Image', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'icon',
                    'type' => 'icon',
                    'title' => __('Icon', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'color',
                    'type' => 'color',
                    'title' => __('Color', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'map',
                    'type' => 'map',
                    'title' => __('Location', 'ultimate-tours-manager'),
                ),
            ),
        ));
    }
    
    /**
     * Create Profile Options
     */
    private function create_profile_options() {
        if (!class_exists('CSF')) {
            return;
        }
        
        $prefix = 'utm_user_meta';
        
        CSF::createProfileOptions($prefix, array(
            'data_type' => 'serialize',
        ));
        
        CSF::createSection($prefix, array(
            'title' => __('Tour Agent Settings', 'ultimate-tours-manager'),
            'fields' => array(
                array(
                    'id' => 'is_agent',
                    'type' => 'switcher',
                    'title' => __('Is Tour Agent', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'agent_phone',
                    'type' => 'text',
                    'title' => __('Phone Number', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'agent_whatsapp',
                    'type' => 'text',
                    'title' => __('WhatsApp', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'agent_bio',
                    'type' => 'textarea',
                    'title' => __('Bio', 'ultimate-tours-manager'),
                ),
            ),
        ));
    }
    
    /**
     * Create Shortcode Generator
     */
    private function create_shortcode_options() {
        if (!class_exists('CSF')) {
            return;
        }
        
        CSF::createShortcoder('utm_shortcodes', array(
            'button_title' => __('Add Tour Element', 'ultimate-tours-manager'),
            'select_title' => __('Select Element', 'ultimate-tours-manager'),
            'insert_title' => __('Insert', 'ultimate-tours-manager'),
            'show_in_editor' => true,
            'gutenberg' => array(
                'title' => __('UTM Shortcodes', 'ultimate-tours-manager'),
                'icon' => 'admin-site-alt3',
                'category' => 'widgets',
            ),
        ));
        
        CSF::createSection('utm_shortcodes', array(
            'title' => __('Tours Grid', 'ultimate-tours-manager'),
            'view' => 'normal',
            'shortcode' => 'utm_tours',
            'fields' => array(
                array(
                    'id' => 'columns',
                    'type' => 'select',
                    'title' => __('Columns', 'ultimate-tours-manager'),
                    'options' => array(
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                    ),
                    'default' => '3',
                ),
                array(
                    'id' => 'limit',
                    'type' => 'number',
                    'title' => __('Number of Tours', 'ultimate-tours-manager'),
                    'default' => 6,
                ),
                array(
                    'id' => 'destination',
                    'type' => 'text',
                    'title' => __('Destination Slug', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'type',
                    'type' => 'text',
                    'title' => __('Tour Type Slug', 'ultimate-tours-manager'),
                ),
                array(
                    'id' => 'orderby',
                    'type' => 'select',
                    'title' => __('Order By', 'ultimate-tours-manager'),
                    'options' => array(
                        'date' => __('Date', 'ultimate-tours-manager'),
                        'title' => __('Title', 'ultimate-tours-manager'),
                        'price' => __('Price', 'ultimate-tours-manager'),
                        'rating' => __('Rating', 'ultimate-tours-manager'),
                        'rand' => __('Random', 'ultimate-tours-manager'),
                    ),
                    'default' => 'date',
                ),
            ),
        ));
        
        CSF::createSection('utm_shortcodes', array(
            'title' => __('Tour Search', 'ultimate-tours-manager'),
            'view' => 'normal',
            'shortcode' => 'utm_search',
            'fields' => array(
                array(
                    'id' => 'style',
                    'type' => 'select',
                    'title' => __('Style', 'ultimate-tours-manager'),
                    'options' => array(
                        'horizontal' => __('Horizontal', 'ultimate-tours-manager'),
                        'vertical' => __('Vertical', 'ultimate-tours-manager'),
                    ),
                    'default' => 'horizontal',
                ),
            ),
        ));
        
        CSF::createSection('utm_shortcodes', array(
            'title' => __('Destinations', 'ultimate-tours-manager'),
            'view' => 'normal',
            'shortcode' => 'utm_destinations',
            'fields' => array(
                array(
                    'id' => 'columns',
                    'type' => 'select',
                    'title' => __('Columns', 'ultimate-tours-manager'),
                    'options' => array(
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5',
                    ),
                    'default' => '4',
                ),
                array(
                    'id' => 'limit',
                    'type' => 'number',
                    'title' => __('Number of Destinations', 'ultimate-tours-manager'),
                    'default' => 8,
                ),
            ),
        ));
    }
    
    /**
     * Create Customizer Options
     */
    private function create_customizer_options() {
        if (!class_exists('CSF')) {
            return;
        }
        
        CSF::createCustomizeOptions('utm_customizer', array(
            'database' => 'option',
            'transport' => 'refresh',
            'capability' => 'edit_theme_options',
            'save_defaults' => true,
        ));
        
        CSF::createSection('utm_customizer', array(
            'id' => 'utm_customize_colors',
            'title' => __('Tour Colors', 'ultimate-tours-manager'),
            'priority' => 30,
            'fields' => array(
                array(
                    'id' => 'tour_primary_color',
                    'type' => 'color',
                    'title' => __('Primary Color', 'ultimate-tours-manager'),
                    'default' => '#0073aa',
                ),
                array(
                    'id' => 'tour_accent_color',
                    'type' => 'color',
                    'title' => __('Accent Color', 'ultimate-tours-manager'),
                    'default' => '#ffc107',
                ),
            ),
        ));
    }
}

// Callback functions
function utm_export_button_callback() {
    echo '<a href="' . admin_url('admin-post.php?action=utm_export_tours') . '" class="button button-primary">' . __('Export Tours', 'ultimate-tours-manager') . '</a>';
}

function utm_import_button_callback() {
    echo '<button type="button" class="button button-primary" id="utm-import-btn">' . __('Import Tours', 'ultimate-tours-manager') . '</button>';
}

function utm_clear_cache_button_callback() {
    echo '<button type="button" class="button" id="utm-clear-cache-btn">' . __('Clear Cache', 'ultimate-tours-manager') . '</button>';
}

function utm_license_status_callback() {
    $license = get_option('utm_options')['license_key'] ?? '';
    if (empty($license)) {
        echo '<span class="utm-license-inactive">' . __('Not Activated', 'ultimate-tours-manager') . '</span>';
    } else {
        echo '<span class="utm-license-active">' . __('Active', 'ultimate-tours-manager') . '</span>';
    }
}
