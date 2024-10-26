<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mozzoplugins.com/
 * @since      1.0.0
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/includes
 * @author     Mozzoplugins
 */
class Cn_Close_Store {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cn_Close_Store_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $cn_shop_status;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CN_CLOSE_STORE_VERSION' ) ) {
			$this->version = CN_CLOSE_STORE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cn-close-store';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cn_Close_Store_Loader. Orchestrates the hooks of the plugin.
	 * - Cn_Close_Store_i18n. Defines internationalization functionality.
	 * - Cn_Close_Store_Admin. Defines all hooks for the admin area.
	 * - Cn_Close_Store_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cn-close-store-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cn-close-store-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cn-close-store-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cn-close-store-public.php';

		$this->loader = new Cn_Close_Store_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cn_Close_Store_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cn_Close_Store_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cn_Close_Store_Admin( $this->get_plugin_name(), $this->get_version() );
		// the $_GET['page'] parameter is created & set by admin page URL, so we cannot set/use a nonce here
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'cn-store' ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		}
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'Cn_Close_Store_plugin_menu' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'cn_admin_footer' );
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cn_Close_Store_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'cn_head' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'cn_footer' );
		$this->loader->add_action('wp_print_footer_scripts', $plugin_public, 'auto_select_pickup', 500);
		
		$this->cn_shop_status = $plugin_public->is_open();
		add_action( 'admin_notices', array( $this, 'show_admin_notice_shop_notice' ) );

		
	}

	public function show_admin_notice_shop_notice() {
		$cn_form = get_option('cn_form', array());
		$show_bar = false;
		$message = '';
		// check if store closed
		if( $cn_form && array_key_exists( "cn_store_open", $cn_form ) && $cn_form['cn_store_open'] == 'on' ) {
			$show_bar = true;
			$message = __( 'Shop is currently closed from taking orders.', 'cn-close-store' );
		}
		if( $this->cn_shop_status == false ) {
			$show_bar = true;
			$message = __( 'Shop is currently closed from taking orders.', 'cn-close-store' );
		}
		
		$class = 'notice notice-warning is-dismissible';
	
		 if( !empty( $message ) ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		 }
		 
		$cn_store_delivery_management = '';
		if( $cn_form && array_key_exists('cn_store_delivery_management', $cn_form ) ) {
			$cn_store_delivery_management = trim( $cn_form['cn_store_delivery_management'] );
		}
		
		 if( !empty( $cn_store_delivery_management ) ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( sprintf( __( 'Only %s option selected', 'cn-close-store' ),$cn_store_delivery_management ) ) ); 
		 }
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cn_Close_Store_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
