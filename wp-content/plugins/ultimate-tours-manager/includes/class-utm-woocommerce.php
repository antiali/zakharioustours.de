<?php
/**
 * UTM WooCommerce Integration Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_WooCommerce {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        add_action('init', array($this, 'register_product_type'));
        add_filter('product_type_selector', array($this, 'add_product_type'));
        add_filter('woocommerce_product_class', array($this, 'product_class'), 10, 2);
        add_action('woocommerce_checkout_order_processed', array($this, 'create_booking_from_order'), 10, 3);
        add_action('woocommerce_order_status_completed', array($this, 'confirm_booking'));
        add_action('woocommerce_order_status_cancelled', array($this, 'cancel_booking'));
        add_action('woocommerce_add_order_item_meta', array($this, 'add_tour_meta_to_order'), 10, 3);
        add_action('wp_ajax_utm_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_utm_add_to_cart', array($this, 'ajax_add_to_cart'));
    }
    
    public function register_product_type() {
        // Register tour product type
    }
    
    public function add_product_type($types) {
        $types['tour'] = __('Tour', 'ultimate-tours-manager');
        return $types;
    }
    
    public function product_class($classname, $product_type) {
        if ($product_type === 'tour') {
            $classname = 'WC_Product_Tour';
        }
        return $classname;
    }
    
    public function create_booking_from_order($order_id, $posted_data, $order) {
        foreach ($order->get_items() as $item) {
            $tour_id = $item->get_meta('_tour_id');
            
            if (!$tour_id) {
                continue;
            }
            
            $booking_number = 'UTM-' . strtoupper(wp_generate_password(8, false));
            
            $booking_id = wp_insert_post(array(
                'post_type' => 'booking',
                'post_title' => $booking_number,
                'post_status' => 'publish',
            ));
            
            update_post_meta($booking_id, '_booking_number', $booking_number);
            update_post_meta($booking_id, '_tour_id', $tour_id);
            update_post_meta($booking_id, '_order_id', $order_id);
            update_post_meta($booking_id, '_first_name', $order->get_billing_first_name());
            update_post_meta($booking_id, '_last_name', $order->get_billing_last_name());
            update_post_meta($booking_id, '_email', $order->get_billing_email());
            update_post_meta($booking_id, '_phone', $order->get_billing_phone());
            update_post_meta($booking_id, '_booking_date', $item->get_meta('_booking_date'));
            update_post_meta($booking_id, '_adults', $item->get_meta('_adults'));
            update_post_meta($booking_id, '_children', $item->get_meta('_children'));
            update_post_meta($booking_id, '_infants', $item->get_meta('_infants'));
            update_post_meta($booking_id, '_total_price', $item->get_total());
            update_post_meta($booking_id, '_booking_status', 'pending');
            update_post_meta($booking_id, '_payment_status', 'unpaid');
            
            // Store booking ID in order item meta
            $item->update_meta_data('_booking_id', $booking_id);
            $item->save();
            
            do_action('utm_booking_created', $booking_id);
        }
    }
    
    public function confirm_booking($order_id) {
        $order = wc_get_order($order_id);
        
        foreach ($order->get_items() as $item) {
            $booking_id = $item->get_meta('_booking_id');
            
            if ($booking_id) {
                update_post_meta($booking_id, '_booking_status', 'confirmed');
                update_post_meta($booking_id, '_payment_status', 'paid');
                
                do_action('utm_booking_confirmed', $booking_id);
            }
        }
    }
    
    public function cancel_booking($order_id) {
        $order = wc_get_order($order_id);
        
        foreach ($order->get_items() as $item) {
            $booking_id = $item->get_meta('_booking_id');
            
            if ($booking_id) {
                update_post_meta($booking_id, '_booking_status', 'cancelled');
                
                do_action('utm_booking_cancelled', $booking_id);
            }
        }
    }
    
    public function add_tour_meta_to_order($item_id, $values, $cart_item_key) {
        if (isset($values['tour_id'])) {
            wc_add_order_item_meta($item_id, '_tour_id', $values['tour_id']);
            wc_add_order_item_meta($item_id, '_booking_date', $values['booking_date']);
            wc_add_order_item_meta($item_id, '_adults', $values['adults']);
            wc_add_order_item_meta($item_id, '_children', $values['children']);
            wc_add_order_item_meta($item_id, '_infants', $values['infants']);
        }
    }
    
    public function ajax_add_to_cart() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        $tour_id = absint($_POST['tour_id']);
        $booking_date = sanitize_text_field($_POST['booking_date']);
        $adults = absint($_POST['adults']);
        $children = isset($_POST['children']) ? absint($_POST['children']) : 0;
        $infants = isset($_POST['infants']) ? absint($_POST['infants']) : 0;
        
        // Get or create WooCommerce product for this tour
        $product_id = $this->get_tour_product_id($tour_id);
        
        if (!$product_id) {
            wp_send_json_error(array('message' => __('Unable to add to cart', 'ultimate-tours-manager')));
        }
        
        // Calculate price
        $price = floatval(get_post_meta($tour_id, 'utm_tour_meta_price', true));
        $sale_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_sale_price', true));
        $child_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_child_price', true));
        $infant_price = floatval(get_post_meta($tour_id, 'utm_tour_meta_infant_price', true));
        
        $adult_price = ($sale_price && $sale_price < $price) ? $sale_price : $price;
        $total = ($adult_price * $adults) + ($child_price * $children) + ($infant_price * $infants);
        
        $cart_item_data = array(
            'tour_id' => $tour_id,
            'booking_date' => $booking_date,
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'custom_price' => $total,
        );
        
        $cart_item_key = WC()->cart->add_to_cart($product_id, 1, 0, array(), $cart_item_data);
        
        if ($cart_item_key) {
            wp_send_json_success(array(
                'message' => __('Tour added to cart', 'ultimate-tours-manager'),
                'cart_url' => wc_get_cart_url(),
                'checkout_url' => wc_get_checkout_url(),
            ));
        } else {
            wp_send_json_error(array('message' => __('Unable to add to cart', 'ultimate-tours-manager')));
        }
    }
    
    private function get_tour_product_id($tour_id) {
        $product_id = get_post_meta($tour_id, '_wc_product_id', true);
        
        if (!$product_id) {
            // Create a new WooCommerce product
            $product = new WC_Product_Simple();
            $product->set_name(get_the_title($tour_id));
            $product->set_status('publish');
            $product->set_catalog_visibility('hidden');
            $product->set_price(get_post_meta($tour_id, 'utm_tour_meta_price', true));
            $product->set_regular_price(get_post_meta($tour_id, 'utm_tour_meta_price', true));
            $product->set_virtual(true);
            $product_id = $product->save();
            
            update_post_meta($tour_id, '_wc_product_id', $product_id);
        }
        
        return $product_id;
    }
}
