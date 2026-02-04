<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class YTrip_Admin {

    /**
     * Settings page slug
     */
    const SETTINGS_SLUG = 'ytrip-settings';

    /**
     * Constructor
     */
    public function __construct() {
        // Add Settings link on plugins page
        add_filter( 'plugin_action_links_' . YTRIP_BASENAME, array( $this, 'add_plugin_action_links' ) );
        
        // Add admin menu
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        
        // Add admin bar quick link
        add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_link' ), 100 );
    }

    /**
     * Add Settings link to plugins page
     */
    public function add_plugin_action_links( $links ) {
        $settings_url = admin_url( 'admin.php?page=' . self::SETTINGS_SLUG );
        
        $custom_links = array(
            'settings' => sprintf(
                '<a href="%s">%s</a>',
                esc_url( $settings_url ),
                esc_html__( 'Settings', 'ytrip' )
            ),
            'docs' => sprintf(
                '<a href="%s" target="_blank">%s</a>',
                esc_url( 'https://docs.ytrip.dev' ),
                esc_html__( 'Docs', 'ytrip' )
            ),
        );

        return array_merge( $custom_links, $links );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu is created by CodeStar Framework
        // Add submenus here if needed
        
        add_submenu_page(
            'ytrip-settings',          // Parent slug (CSF creates this)
            __( 'Agents', 'ytrip' ),
            __( 'Agents', 'ytrip' ),
            'manage_options',
            'ytrip-agents',
            array( $this, 'render_agents_page' )
        );

        add_submenu_page(
            'ytrip-settings',
            __( 'Commissions', 'ytrip' ),
            __( 'Commissions', 'ytrip' ),
            'manage_options',
            'ytrip-commissions',
            array( $this, 'render_commissions_page' )
        );
    }

    /**
     * Add quick link to admin bar
     */
    public function add_admin_bar_link( $wp_admin_bar ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $wp_admin_bar->add_node( array(
            'id'    => 'ytrip-settings',
            'title' => '<span class="ab-icon dashicons dashicons-palmtree" style="margin-top:2px;"></span> YTrip',
            'href'  => admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ),
        ) );

        $wp_admin_bar->add_node( array(
            'parent' => 'ytrip-settings',
            'id'     => 'ytrip-settings-main',
            'title'  => __( 'Settings', 'ytrip' ),
            'href'   => admin_url( 'admin.php?page=' . self::SETTINGS_SLUG ),
        ) );

        $wp_admin_bar->add_node( array(
            'parent' => 'ytrip-settings',
            'id'     => 'ytrip-agents',
            'title'  => __( 'Agents', 'ytrip' ),
            'href'   => admin_url( 'admin.php?page=ytrip-agents' ),
        ) );

        $wp_admin_bar->add_node( array(
            'parent' => 'ytrip-settings',
            'id'     => 'ytrip-tours',
            'title'  => __( 'All Tours', 'ytrip' ),
            'href'   => admin_url( 'edit.php?post_type=ytrip_tour' ),
        ) );

        $wp_admin_bar->add_node( array(
            'parent' => 'ytrip-settings',
            'id'     => 'ytrip-add-tour',
            'title'  => __( 'Add New Tour', 'ytrip' ),
            'href'   => admin_url( 'post-new.php?post_type=ytrip_tour' ),
        ) );
    }

    /**
     * Render Agents management page
     */
    public function render_agents_page() {
        $agents = get_users( array( 'role' => 'ytrip_agent' ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Travel Agents', 'ytrip' ); ?></h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Company', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Contact', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Commission', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Bookings', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'ytrip' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $agents ) ) : ?>
                        <tr>
                            <td colspan="7"><?php esc_html_e( 'No agents registered yet.', 'ytrip' ); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $agents as $agent ) : 
                            $data = YTrip_Agent_Portal::get_agent_data( $agent->ID );
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html( $data['company_name'] ?: '-' ); ?></strong></td>
                                <td><?php echo esc_html( $agent->display_name ); ?></td>
                                <td><?php echo esc_html( $agent->user_email ); ?></td>
                                <td>
                                    <?php
                                    $status_labels = array(
                                        'pending'   => '<span style="color:#856404;">⏳ Pending</span>',
                                        'approved'  => '<span style="color:#155724;">✓ Approved</span>',
                                        'suspended' => '<span style="color:#721c24;">✗ Suspended</span>',
                                    );
                                    echo $status_labels[ $data['status'] ] ?? $data['status'];
                                    ?>
                                </td>
                                <td><?php echo esc_html( $data['commission_rate'] ); ?>%</td>
                                <td><?php echo esc_html( $data['total_bookings'] ); ?></td>
                                <td>
                                    <?php if ( $data['status'] === 'pending' ) : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ytrip-agents&action=approve&agent=' . $agent->ID ), 'ytrip_agent_action' ); ?>" 
                                           class="button button-primary button-small">
                                            <?php esc_html_e( 'Approve', 'ytrip' ); ?>
                                        </a>
                                    <?php elseif ( $data['status'] === 'approved' ) : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ytrip-agents&action=suspend&agent=' . $agent->ID ), 'ytrip_agent_action' ); ?>" 
                                           class="button button-small">
                                            <?php esc_html_e( 'Suspend', 'ytrip' ); ?>
                                        </a>
                                    <?php else : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ytrip-agents&action=approve&agent=' . $agent->ID ), 'ytrip_agent_action' ); ?>" 
                                           class="button button-small">
                                            <?php esc_html_e( 'Reactivate', 'ytrip' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        
        // Handle actions
        if ( isset( $_GET['action'], $_GET['agent'], $_GET['_wpnonce'] ) ) {
            if ( wp_verify_nonce( $_GET['_wpnonce'], 'ytrip_agent_action' ) ) {
                $agent_id = absint( $_GET['agent'] );
                if ( $_GET['action'] === 'approve' ) {
                    YTrip_Agent_Portal::approve_agent( $agent_id );
                    echo '<script>location.href="' . admin_url( 'admin.php?page=ytrip-agents&approved=1' ) . '";</script>';
                } elseif ( $_GET['action'] === 'suspend' ) {
                    YTrip_Agent_Portal::suspend_agent( $agent_id );
                    echo '<script>location.href="' . admin_url( 'admin.php?page=ytrip-agents&suspended=1' ) . '";</script>';
                }
            }
        }
    }

    /**
     * Render Commissions page
     */
    public function render_commissions_page() {
        global $wpdb;

        // Get all orders with agent commissions
        $orders = wc_get_orders( array(
            'limit'    => 50,
            'meta_key' => '_ytrip_agent_commission',
            'orderby'  => 'date',
            'order'    => 'DESC',
        ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Agent Commissions', 'ytrip' ); ?></h1>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Date', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Agent', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Order Total', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Commission', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Paid', 'ytrip' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'ytrip' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $orders ) ) : ?>
                        <tr>
                            <td colspan="7"><?php esc_html_e( 'No commissions yet.', 'ytrip' ); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ( $orders as $order ) : 
                            $agent_id   = $order->get_meta( '_ytrip_agent_id' );
                            $agent      = get_user_by( 'ID', $agent_id );
                            $commission = $order->get_meta( '_ytrip_agent_commission' );
                            $paid       = $order->get_meta( '_ytrip_commission_paid' );
                        ?>
                            <tr>
                                <td><a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">#<?php echo esc_html( $order->get_id() ); ?></a></td>
                                <td><?php echo esc_html( $order->get_date_created()->format( 'Y-m-d' ) ); ?></td>
                                <td><?php echo $agent ? esc_html( $agent->display_name ) : '-'; ?></td>
                                <td><?php echo wc_price( $order->get_total() ); ?></td>
                                <td><strong><?php echo wc_price( $commission ); ?></strong></td>
                                <td>
                                    <?php if ( $paid ) : ?>
                                        <span style="color:#155724;">✓ <?php esc_html_e( 'Paid', 'ytrip' ); ?></span>
                                    <?php else : ?>
                                        <span style="color:#856404;">⏳ <?php esc_html_e( 'Pending', 'ytrip' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( ! $paid ) : ?>
                                        <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ytrip-commissions&action=mark_paid&order=' . $order->get_id() ), 'ytrip_commission_action' ); ?>" 
                                           class="button button-small">
                                            <?php esc_html_e( 'Mark Paid', 'ytrip' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        
        // Handle mark as paid
        if ( isset( $_GET['action'], $_GET['order'], $_GET['_wpnonce'] ) && $_GET['action'] === 'mark_paid' ) {
            if ( wp_verify_nonce( $_GET['_wpnonce'], 'ytrip_commission_action' ) ) {
                $order = wc_get_order( absint( $_GET['order'] ) );
                if ( $order ) {
                    $order->update_meta_data( '_ytrip_commission_paid', 1 );
                    $order->update_meta_data( '_ytrip_commission_paid_date', current_time( 'mysql' ) );
                    $order->save();
                    echo '<script>location.href="' . admin_url( 'admin.php?page=ytrip-commissions&paid=1' ) . '";</script>';
                }
            }
        }
    }
}

new YTrip_Admin();