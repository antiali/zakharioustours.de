<?php
/**
 * UTM Export/Import Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class UTM_Export_Import {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_post_utm_export_tours', array($this, 'export_tours'));
        add_action('admin_post_utm_export_bookings', array($this, 'export_bookings'));
        add_action('wp_ajax_utm_import_tours', array($this, 'import_tours'));
    }
    
    public function export_tours() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'ultimate-tours-manager'));
        }
        
        $format = isset($_GET['format']) ? $_GET['format'] : 'json';
        
        $tours = get_posts(array(
            'post_type' => 'tour',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));
        
        $data = array();
        
        foreach ($tours as $tour) {
            $tour_data = array(
                'title' => $tour->post_title,
                'content' => $tour->post_content,
                'excerpt' => $tour->post_excerpt,
                'status' => $tour->post_status,
                'meta' => get_post_meta($tour->ID),
                'destinations' => wp_get_post_terms($tour->ID, 'destination', array('fields' => 'slugs')),
                'tour_types' => wp_get_post_terms($tour->ID, 'tour-type', array('fields' => 'slugs')),
                'features' => wp_get_post_terms($tour->ID, 'tour-feature', array('fields' => 'slugs')),
            );
            
            $data[] = $tour_data;
        }
        
        $filename = 'tours-export-' . date('Y-m-d-His');
        
        switch ($format) {
            case 'csv':
                $this->export_csv($data, $filename);
                break;
            case 'xml':
                $this->export_xml($data, $filename);
                break;
            default:
                $this->export_json($data, $filename);
        }
        
        exit;
    }
    
    private function export_json($data, $filename) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    private function export_csv($data, $filename) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, array('Title', 'Content', 'Excerpt', 'Status', 'Price', 'Duration', 'Destinations'));
        
        foreach ($data as $tour) {
            $price = isset($tour['meta']['utm_tour_meta_price'][0]) ? $tour['meta']['utm_tour_meta_price'][0] : '';
            $duration = isset($tour['meta']['utm_tour_meta_duration_value'][0]) ? $tour['meta']['utm_tour_meta_duration_value'][0] : '';
            
            fputcsv($output, array(
                $tour['title'],
                $tour['content'],
                $tour['excerpt'],
                $tour['status'],
                $price,
                $duration,
                implode(', ', $tour['destinations']),
            ));
        }
        
        fclose($output);
    }
    
    private function export_xml($data, $filename) {
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><tours></tours>');
        
        foreach ($data as $tour) {
            $item = $xml->addChild('tour');
            $item->addChild('title', htmlspecialchars($tour['title']));
            $item->addChild('content', htmlspecialchars($tour['content']));
            $item->addChild('excerpt', htmlspecialchars($tour['excerpt']));
            $item->addChild('status', $tour['status']);
            
            $destinations = $item->addChild('destinations');
            foreach ($tour['destinations'] as $dest) {
                $destinations->addChild('destination', $dest);
            }
        }
        
        echo $xml->asXML();
    }
    
    public function export_bookings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized access', 'ultimate-tours-manager'));
        }
        
        $bookings = get_posts(array(
            'post_type' => 'booking',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ));
        
        $data = array();
        
        foreach ($bookings as $booking) {
            $data[] = array(
                'booking_number' => get_post_meta($booking->ID, '_booking_number', true),
                'tour_id' => get_post_meta($booking->ID, '_tour_id', true),
                'first_name' => get_post_meta($booking->ID, '_first_name', true),
                'last_name' => get_post_meta($booking->ID, '_last_name', true),
                'email' => get_post_meta($booking->ID, '_email', true),
                'phone' => get_post_meta($booking->ID, '_phone', true),
                'booking_date' => get_post_meta($booking->ID, '_booking_date', true),
                'adults' => get_post_meta($booking->ID, '_adults', true),
                'children' => get_post_meta($booking->ID, '_children', true),
                'total' => get_post_meta($booking->ID, '_total_price', true),
                'status' => get_post_meta($booking->ID, '_booking_status', true),
                'payment_status' => get_post_meta($booking->ID, '_payment_status', true),
                'created' => $booking->post_date,
            );
        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="bookings-export-' . date('Y-m-d-His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, array('Booking #', 'Tour ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Date', 'Adults', 'Children', 'Total', 'Status', 'Payment', 'Created'));
        
        foreach ($data as $booking) {
            fputcsv($output, $booking);
        }
        
        fclose($output);
        exit;
    }
    
    public function import_tours() {
        check_ajax_referer('utm_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access', 'ultimate-tours-manager')));
        }
        
        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(array('message' => __('No file uploaded', 'ultimate-tours-manager')));
        }
        
        $file = $_FILES['import_file'];
        $content = file_get_contents($file['tmp_name']);
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        switch ($extension) {
            case 'json':
                $tours = json_decode($content, true);
                break;
            case 'csv':
                $tours = $this->parse_csv($content);
                break;
            default:
                wp_send_json_error(array('message' => __('Unsupported file format', 'ultimate-tours-manager')));
        }
        
        if (empty($tours)) {
            wp_send_json_error(array('message' => __('No tours found in file', 'ultimate-tours-manager')));
        }
        
        $imported = 0;
        
        foreach ($tours as $tour_data) {
            $tour_id = wp_insert_post(array(
                'post_type' => 'tour',
                'post_title' => $tour_data['title'],
                'post_content' => isset($tour_data['content']) ? $tour_data['content'] : '',
                'post_excerpt' => isset($tour_data['excerpt']) ? $tour_data['excerpt'] : '',
                'post_status' => isset($tour_data['status']) ? $tour_data['status'] : 'draft',
            ));
            
            if (!is_wp_error($tour_id)) {
                // Import meta
                if (isset($tour_data['meta'])) {
                    foreach ($tour_data['meta'] as $key => $values) {
                        if (is_array($values)) {
                            foreach ($values as $value) {
                                update_post_meta($tour_id, $key, maybe_unserialize($value));
                            }
                        }
                    }
                }
                
                // Import taxonomies
                if (isset($tour_data['destinations'])) {
                    wp_set_post_terms($tour_id, $tour_data['destinations'], 'destination');
                }
                
                if (isset($tour_data['tour_types'])) {
                    wp_set_post_terms($tour_id, $tour_data['tour_types'], 'tour-type');
                }
                
                $imported++;
            }
        }
        
        wp_send_json_success(array(
            'message' => sprintf(__('%d tours imported successfully', 'ultimate-tours-manager'), $imported),
            'count' => $imported,
        ));
    }
    
    private function parse_csv($content) {
        $lines = explode("\n", $content);
        $header = str_getcsv(array_shift($lines));
        
        $tours = array();
        
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }
            
            $values = str_getcsv($line);
            $tour = array();
            
            foreach ($header as $i => $key) {
                $tour[strtolower(str_replace(' ', '_', $key))] = isset($values[$i]) ? $values[$i] : '';
            }
            
            $tours[] = $tour;
        }
        
        return $tours;
    }
}
