<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mozzoplugins.com/
 * @since      1.0.0
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/admin
 * @author     Mozzoplugins
 */
class Cn_Close_Store_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
			// de-register select2 js file, as it may be an older version
		wp_dequeue_style( 'wpml-select-2' );		
		wp_enqueue_style( 'select2', plugins_url( 'css/select2.min.css', __FILE__ ), array(), '4.0.13', 'all' );

		wp_enqueue_style( 'cn-custom.css', plugin_dir_url( __FILE__ ) . '../cn_package/css/cn-custom.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'cn-grid', plugin_dir_url( __FILE__ ) . '../cn_package/css/cn-grid.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'sweet-alert.css', plugin_dir_url( __FILE__ ) . '../cn_package/node_modules/sweetalert/sweetalert/lib/sweet-alert.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cn-close-store-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_deregister_script( 'select2' );

		// WC core registers the .full select2 as select2, so we need to do same to avoid compatibility issues
		wp_enqueue_script( 'select2', plugins_url( 'js/select2.full.min.js', __FILE__ ), array( 'jquery' ), '4.0.13', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cn-close-store-admin.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script('cn-custom.js', plugin_dir_url( __FILE__ ) . '../cn_package/js/cn-custom.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'sweetalert2.all.min.js', plugin_dir_url( __FILE__ ) . '../cn_package/node_modules/sweetalert/sweetalert/lib/sweet-alert.min.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( 'cn-custom.js','cn_plugin_vars', array('ajaxurl' => admin_url('admin-ajax.php'),'plugin_url'=>Cn_Close_Store_URI));
			
	}

	public function Cn_Close_Store_plugin_menu(){
		add_menu_page('Store Manager', 'Store Manager', 'manage_options', 'cn-store',  array($this, 'Close_Store'),'dashicons-text-page',5);
	}
	
	/**
	 * display settings
	 * 
	 * @global wpdb
	 * @return void
	 */
	public function Close_Store(){
		global $wpdb;
	
		$cn_form = get_option( 'cn_form', array() );
		// See if the user has posted us some information		
		if ( isset( $_POST['_open_close_nonce'], $_POST['save_settings_info'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_open_close_nonce'] ) ), 'nonce_settings_open_close' ) ) {
			$cn_form['cn_store_open'] = isset( $_POST['cn_store_open'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['cn_store_open'] ) ) ) : '';
			update_option( 'cn_form', $cn_form );
			$this->cn_display_success_message_on_saved();
		}
		
		if ( isset( $_POST['_delivery_pickup_nonce'], $_POST['save_settings_delivery'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_delivery_pickup_nonce'] ) ), 'nonce_settings_delivery_pickup' ) ) {
			
			$get_value_store = array_key_exists('cn_store_delivery_management', $cn_form ) ? $cn_form['cn_store_delivery_management'] : '';
			$get_value_new = isset( $_POST['delivery_status'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['delivery_status'] ) ) ) : '';
			$cn_form['cn_store_delivery_management'] = ( $get_value_store == $get_value_new ) ? '' : $get_value_new;
				 
			
			update_option( 'cn_form', $cn_form );
			$this->cn_display_success_message_on_saved();
		}
		
		if ( isset( $_POST['_banner_message_nonce'], $_POST['save'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_banner_message_nonce'] ) ), 'nonce_settings_banner_message' ) ) {
			
			$cn_form['cn_store_closed_banner'] = isset( $_POST['cn_store_closed_banner'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['cn_store_closed_banner'] ) ) ) : '';
			$cn_form['cn_textarea_store_banner'] = isset( $_POST['cn_textarea_store_banner'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['cn_textarea_store_banner'] ) ) ) : '';
			update_option( 'cn_form', $cn_form );
			$this->cn_display_success_message_on_saved();
		}
		
		if ( isset( $_POST['_business_message_nonce'], $_POST['save_business'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_business_message_nonce'] ) ), 'nonce_settings_business_message' ) ) {
			
			$weekdays = [ 10001 => 'Monday', 10002 =>'Tuesday', 10003 =>'Wednesday', 10004 => 'Thursday', 10005 => 'Friday', 10006 => 'Saturday', 10007 => 'Sunday' ];
			foreach ( array_keys( $weekdays ) as $weekday_key ) {
			   $cn_form[$weekday_key] = map_deep( wp_unslash( $_POST[$weekday_key] ), 'sanitize_text_field' );
			}
			update_option( 'cn_form', $cn_form );
			$this->cn_display_success_message_on_saved();
		}
		global $sm_fs;
		$cn_points = '_nonce';
		if ( $sm_fs && sm_fs()->is__premium_only() ) {
			if ( sm_fs()->can_use_premium_code() ) {
				$cn_points = '';
			}
		}
		$cn_form = get_option( 'cn_form', array() );
		require_once Cn_Close_Store_DIR . 'admin/partials/cn-close-store-admin-display.php';	
	}

	public function cn_admin_footer() {
		$cn_form=get_option( 'cn_form', array() );
		if( $cn_form && array_key_exists( "switch_off", $cn_form ) && array_key_exists( "switch_off_msg", $cn_form ) && $cn_form['switch_off_msg']=='on' && $cn_form['switch_off']!='on'){ ?>
			<div class="cn_msg">
				<i class="cn_close" onclick="cn_close();">X</i>
				<?php echo esc_html( $cn_form['notification_message'] ); ?>
			</div>
			<?php	
		}
	}
	
	public function cn_display_success_message_on_saved(){ ?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				swal({
					type: 'success',
					title: 'Updated successfully',
					text: '',
					showConfirmButton: false,
					timer: 1600
				});
			});
		</script>
	<?php
	}	
}

