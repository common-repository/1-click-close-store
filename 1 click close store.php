<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mozzoplugins.com/
 * @since             1.0.0
 * @package           Cn_Close_Store
 *
 * Plugin Name:       1 Click Close Store
 * Plugin URI:        https://mozzoplugins.com
 * Description:       1 Click Close Store allows you to set business hours, open/close your store with one button and push messages directly on your homepage to your clients.
 * Version:           1.1.0
 * Author:            Mozzoplugins
 * Author URI:        https://mozzoplugins.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.5
 * Tested up to: 6.0
 *
 * Requires PHP: 5.6
 * PHP tested up to: 8.1
 *
 * WC requires at least: 2.5
 * WC tested up to: 6.7
 * Text Domain:       cn-close-store
 * Domain Path:       /languages
 */
if ( !defined( 'ABSPATH' ) ) {
    // Exit if accessed directly
    exit;
}
/**
 * Currently plugin version.
 */
define( 'CN_CLOSE_STORE_VERSION', '1.1.0' );
$path_array = wp_upload_dir();
$upload_url = $path_array['baseurl'];
$upload_dir = $path_array['basedir'];
define( 'Cn_Close_Store_DIR', plugin_dir_path( __FILE__ ) );
define( 'Cn_Close_Store_URI', plugin_dir_url( __FILE__ ) );
define( 'Cn_Close_Store_UPLOAD_URI', $upload_url );
define( 'Cn_Close_Store_UPLOAD_DIR', $upload_dir );

if ( function_exists( 'sm_fs' ) ) {
    sm_fs()->set_basename( false, __FILE__ );
} else {
    /**
     * sm_fs
     * 
     * @return object
     */
    function sm_fs()
    {
        global  $sm_fs ;
        
        if ( !isset( $sm_fs ) ) {
            require_once dirname( __FILE__ ) . '/includes/freemius/start.php';
            $sm_fs = fs_dynamic_init( array(
                'id'             => '10825',
                'slug'           => 'store-manager',
                'type'           => 'plugin',
                'public_key'     => 'pk_5882c04d86d6bbf740fa97bfa9f47',
                'is_premium'     => false,
                'premium_suffix' => 'Pro Plan',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 7,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'slug'       => 'cn-store',
                'first-path' => 'admin.php?page=cn-store',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $sm_fs;
    }
    
    sm_fs();
    do_action( 'sm_fs_loaded' );
}

global  $sm_fs ;

if ( $sm_fs ) {
    sm_fs()->add_filter(
        'connect_message_on_update',
        'sm_fs_custom_connect_message_on_update',
        10,
        6
    );
    sm_fs()->add_action( 'after_uninstall', 'sm_fs_uninstall_cleanup' );
}

/**
 * connect_message_on_update
 * 
 * @param string $message Description for $message
 * @param string $user_first_name Description for $user_first_name
 * @param string $plugin_title Description for $plugin_title
 * @param string $user_login Description for user_login
 * @param string $site_link site link
 * @param string $freemius_link freemius_link
 * 
 * @return string
 */
function sm_fs_custom_connect_message_on_update(
    $message,
    $user_first_name,
    $plugin_title,
    $user_login,
    $site_link,
    $freemius_link
)
{
    return sprintf(
        __( 'Hey %1$s' ) . ',<br>' . __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'store-manager' ),
        esc_html( $user_first_name ),
        '<b>' . esc_html( $plugin_title ) . '</b>',
        '<b>' . esc_html( $user_login ) . '</b>',
        esc_url( $site_link ),
        esc_url( $freemius_link )
    );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cn-close-store-activator.php
 */
function activate_cn_close_store()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cn-close-store-activator.php';
    Cn_Close_Store_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cn-close-store-deactivator.php
 */
function deactivate_cn_close_store()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cn-close-store-deactivator.php';
    Cn_Close_Store_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cn_close_store' );
register_deactivation_hook( __FILE__, 'deactivate_cn_close_store' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cn-close-store.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cn_close_store()
{
    $plugin = new Cn_Close_Store();
    $plugin->run();
}

run_cn_close_store();
/**
* Add settings link to plugin actions
*
* @param array<string, string> $actions an array of plugin action links
* @param string $plugin_file plugin file name
* @return array<string, string>
*/
function filter_cn_plugin_plugins_page_links( $actions, $plugin_file )
{
    $plugin = plugin_basename( __DIR__ );
    
    if ( $plugin == dirname( $plugin_file ) ) {
        $settings_link = array(
            'settings' => '<a href="admin.php?page=cn-store">' . __( 'Settings', 'cn-close-store' ) . '</a>',
        );
        $actions = array_merge( $settings_link, $actions );
    }
    
    return $actions;
}

add_filter(
    'plugin_action_links',
    'filter_cn_plugin_plugins_page_links',
    10,
    2
);
add_action( 'admin_init', 'cn_require_woocommerce' );
//require woocommerce
function cn_require_woocommerce()
{
    
    if ( is_admin() && current_user_can( 'activate_plugins' ) && !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        add_action( 'admin_notices', 'cn_require_woocommerce_notice' );
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }

}

// show admin notice if woocommerce is not active
function cn_require_woocommerce_notice()
{
    ?>
	 <style>
        #toplevel_page_cn-store {
            display: none;
        }
    </style>
	<?php 
    $class = 'notice notice-error is-dismissible';
    $message = __( 'Sorry, but Store Manager requires the Woocommerce plugin to be installed and activated.', 'cn-close-store' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
