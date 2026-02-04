<?php
/**
 * YTrip GitHub Helper
 * 
 * Handles GitHub API integration for debugging and updates
 * @package YTrip
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class YTrip_GitHub_Helper {
    
    private static $instance = null;
    private $repo;
    private $token;
    private $debug_mode;
    private $log_level;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function __construct() {
        // Get settings
        $settings = get_option('ytrip_settings', array());
        
        $this->repo = isset($settings['github_repo']) ? $settings['github_repo'] : '';
        $this->token = isset($settings['github_token']) ? $settings['github_token'] : '';
        $this->debug_mode = isset($settings['debug_mode']) ? $settings['debug_mode'] : false;
        $this->log_level = isset($settings['debug_log_level']) ? $settings['debug_log_level'] : 'error';
        
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Add admin notice if repo/token missing
        add_action('admin_notices', array($this, 'admin_notices'));
        
        // Add debug info to admin page
        add_action('ytrip_debug_page_after', array($this, 'debug_info'));
    }
    
    /**
     * Check if GitHub is configured
     */
    public function is_configured() {
        return !empty($this->repo) && !empty($this->token);
    }
    
    /**
     * Get GitHub API base URL
     */
    private function get_api_url($endpoint = '') {
        return 'https://api.github.com/' . $endpoint;
    }
    
    /**
     * Make GitHub API request
     */
    private function api_request($endpoint, $method = 'GET', $data = null) {
        if (!$this->is_configured()) {
            $this->log('GitHub not configured - missing repo or token', 'error');
            return false;
        }
        
        $url = $this->get_api_url($endpoint);
        $args = array(
            'method'  => $method,
            'headers' => array(
                'Authorization' => 'token ' . $this->token,
                'User-Agent'   => 'YTrip-WordPress-Plugin/1.0.0',
                'Accept'       => 'application/vnd.github.v3+json',
            ),
            'timeout' => 15,
        );
        
        if ($data) {
            $args['body'] = json_encode($data);
            $args['headers']['Content-Type'] = 'application/json';
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            $this->log('GitHub API Error: ' . $response->get_error_message(), 'error');
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($status_code >= 400) {
            $this->log('GitHub API Error: HTTP ' . $status_code . ' - ' . $body, 'error');
            return false;
        }
        
        return json_decode($body, true);
    }
    
    /**
     * Get repository info
     */
    public function get_repo_info() {
        return $this->api_request('repos/' . $this->repo);
    }
    
    /**
     * Get latest release
     */
    public function get_latest_release() {
        return $this->api_request('repos/' . $this->repo . '/releases/latest');
    }
    
    /**
     * Create an issue (for debugging)
     */
    public function create_debug_issue($title, $body) {
        return $this->api_request('repos/' . $this->repo . '/issues', 'POST', array(
            'title' => $title,
            'body'  => $body,
            'labels' => array('debug', 'ytrip-plugin'),
        ));
    }
    
    /**
     * Test GitHub connection
     */
    public function test_connection() {
        $this->log('Testing GitHub connection...', 'info');
        
        $repo_info = $this->get_repo_info();
        
        if ($repo_info) {
            $this->log('GitHub connection successful!', 'info');
            return array(
                'success' => true,
                'message' => 'Connected to GitHub successfully',
                'repo'    => $repo_info['full_name'],
                'url'      => $repo_info['html_url'],
            );
        } else {
            $this->log('GitHub connection failed', 'error');
            return array(
                'success' => false,
                'message' => 'Failed to connect to GitHub. Check your repo and token.',
            );
        }
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        
        // GitHub not configured notice
        if (!$this->is_configured() && $this->debug_mode) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>YTrip:</strong> GitHub integration not configured. Go to <a href="' . admin_url('admin.php?page=ytrip-settings') . '">Settings → GitHub Integration</a> to add your repository and token.</p>';
            echo '</div>';
        }
    }
    
    /**
     * Add debug info to admin page
     */
    public function debug_info() {
        ?>
        <div class="debug-section debug-info">
            <h2>🐙 GitHub Integration Status</h2>
            <table class="debug-table">
                <tr>
                    <th>Configured</th>
                    <td><?php echo $this->is_configured() ? '<span class="status-yes">✅ YES</span>' : '<span class="status-no">❌ NO</span>'; ?></td>
                </tr>
                <tr>
                    <th>Repository</th>
                    <td><?php echo esc_html($this->repo ?: 'Not set'); ?></td>
                </tr>
                <tr>
                    <th>Token Set</th>
                    <td><?php echo !empty($this->token) ? '<span class="status-yes">✅ YES</span>' : '<span class="status-no">❌ NO</span>'; ?></td>
                </tr>
                <tr>
                    <th>Debug Mode</th>
                    <td><?php echo $this->debug_mode ? '<span class="status-yes">✅ ENABLED</span>' : '<span class="status-no">❌ DISABLED</span>'; ?></td>
                </tr>
                <tr>
                    <th>Log Level</th>
                    <td><?php echo esc_html($this->log_level); ?></td>
                </tr>
            </table>
            
            <?php if ($this->is_configured()): ?>
            <h3>Test Connection</h3>
            <form method="post">
                <?php wp_nonce_field('ytrip_test_github', 'ytrip_github_nonce'); ?>
                <input type="submit" name="ytrip_test_github" class="button button-primary" value="Test GitHub Connection">
            </form>
            
            <?php
            if (isset($_POST['ytrip_test_github']) && check_admin_referer('ytrip_test_github', 'ytrip_github_nonce')) {
                $result = $this->test_connection();
                echo '<div class="' . ($result['success'] ? 'debug-success' : 'debug-error') . '">';
                echo '<p>' . esc_html($result['message']) . '</p>';
                if ($result['success']) {
                    echo '<p><strong>Repository:</strong> ' . esc_html($result['repo']) . '<br>';
                    echo '<strong>URL:</strong> <a href="' . esc_url($result['url']) . '" target="_blank">' . esc_html($result['url']) . '</a></p>';
                }
                echo '</div>';
            }
            ?>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Log message
     */
    private function log($message, $level = 'info') {
        if (!$this->debug_mode) {
            return;
        }
        
        // Check log level
        $levels = array('error', 'warning', 'info');
        $current_level_index = array_search($this->log_level, $levels);
        $message_level_index = array_search($level, $levels);
        
        if ($message_level_index > $current_level_index) {
            return;
        }
        
        $log_message = sprintf('[YTrip-GitHub] [%s] %s', strtoupper($level), $message);
        
        // Log to error_log
        error_log($log_message);
        
        // Log to file if enabled
        if (isset($_POST['ytrip_settings']['log_to_file']) || get_option('ytrip_settings')['log_to_file']) {
            $log_file = WP_CONTENT_DIR . '/debug.log';
            $timestamp = current_time('Y-m-d H:i:s');
            file_put_contents($log_file, "[$timestamp] $log_message\n", FILE_APPEND);
        }
    }
}

// Initialize
function ytrip_github_helper() {
    return YTrip_GitHub_Helper::instance();
}
