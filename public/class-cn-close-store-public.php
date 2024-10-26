<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mozzoplugins.com/
 * @since      1.0.0
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/public
 * @author     Mozzoplugins
 */
class Cn_Close_Store_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$cn_form = get_option('cn_form', array());

		$show_bar = false;
		// check if store closed
		if( $cn_form && array_key_exists( "cn_store_open", $cn_form ) && $cn_form['cn_store_open'] == 'on' ) {
			$show_bar = true;
		}

		global $sm_fs;	
		if ( $sm_fs && sm_fs()->is__premium_only() ) {
			if ( sm_fs()->can_use_premium_code() ) {
				add_action( 'wp_footer', array( $this, 'cn_display_popup_statusbar' ) );
				if( $this->is_open() == false ) {
					$show_bar = true;
				} elseif( empty( $this->get_next_time() ) ) {
					$show_bar = false;
				}
			}
		}		
	
		if( $show_bar ) {
			add_action( 'init', array( $this, 'cn_woocommerce_holiday_mode' ) );
		}
		//add_filter( 'woocommerce_package_rates', array( $this, 'cn_hide_show_shipping' ), 10 );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cn_Close_Store_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cn_Close_Store_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cn-close-store-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'cn-custom.css', plugin_dir_url( __FILE__ ) . '../cn_package/css/cn-custom.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cn-close-store-public.js', array( 'jquery' ), $this->version, true );
		
		$cn_form = get_option( 'cn_form', array() );
		$cn_store_delivery_management = '';
		if( $cn_form && array_key_exists('cn_store_delivery_management', $cn_form ) ) {
			$cn_store_delivery_management = trim( $cn_form['cn_store_delivery_management'] );
		}
		
		wp_localize_script( $this->plugin_name, 'frontend_cn_shipping_object',
			array( 
				'check_checkout_page' => is_checkout(),
				'check_cart_page' => is_cart(),
				'check_delivery_mgt' => trim( $cn_store_delivery_management ),
			)
		);
		
		wp_enqueue_script('cn-custom.js', plugin_dir_url( __FILE__ ) . '../cn_package/js/cn-custom.js', array( 'jquery' ), $this->version, false );
	}

	public function cn_footer() {
	}

	public function auto_select_pickup(){ 
	}


	public function cn_head() {
	$cn_form=get_option('cn_form', array());

	$cn_store_open_status = 'open';
	if( $cn_form && array_key_exists( "cn_store_open", $cn_form ) && $cn_form['cn_store_open'] == 'on' ) {
		 $cn_store_open_status = 'close';
	}		
	if( $cn_store_open_status == 'close' ) {
		?>
		<div class="cn_model" id="cn_model" style="display: block;">
			<div class="cn_model_body" style="background: #b71717;text-align: center;color: #fff;border-radius: 15px;">
				<div class="cn_card mb-4" style="border: none !important">
					 <i class="cn_close pull-right">X</i>
				</div>
					<div id="cn_model_body" class="cn_card-body">
						<h3 style="color: #fff"><?php echo __( 'We are not receiving orders at the moment', 'cn-close-store' ); ?></h3>
					</div>
				</div>
			</div>
		<?php	
	}

	if( $cn_store_open_status == 'open' && $cn_form && array_key_exists('cn_store_delivery_management', $cn_form ) && !empty( $cn_form['cn_store_delivery_management']) ) {
		$cn_store_delivery_management = trim( $cn_form['cn_store_delivery_management'] );
		?>
		<div class="cn_model" id="cn_model" style="display: block;">
			<div class="cn_model_body" style="background: #b71717;text-align: center;color: #fff;border-radius: 15px;">
				<div class="cn_card mb-4" style="border: none !important">
					 <i class="cn_close pull-right">X</i>
				 </div>
					<div id="cn_model_body" class="cn_card-body">
						<h3 style="color: #fff"><?php 
							if( $cn_store_delivery_management == 'pickup' ) {
								echo esc_html__( 'We are operating only with pickup delivery method at the moment', 'cn-close-store' );
							} else {
							 echo esc_html__( 'We are operating only with delivery at the moment, Pickup is not available', 'cn-close-store' );
							}
						?></h3>
					</div>
			</div>
		</div>
		
		
		<?php
	}
	global $sm_fs;	
	if ( $sm_fs && sm_fs()->is__premium_only() ) {
		if ( sm_fs()->can_use_premium_code() ) {
			if( $cn_form && array_key_exists( "cn_store_closed_banner", $cn_form ) && $cn_form['cn_store_closed_banner'] == 'on' ){
				?>
				<div class="cn_msg">
					<i class="cn_close" onclick="cn_close();">X</i>
					<?php echo esc_html( $cn_form['cn_textarea_store_banner'] ); ?>
				</div>
				<?php	
			} 	
		}
	}
 }

	/**
	 * Disable Cart, Checkout, Add Cart
	 */ 
	public function cn_woocommerce_holiday_mode() {
		// remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		//remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		add_action( 'woocommerce_before_main_content',  array( $this, 'cn_wc_shop_disabled' ), 5 );
		add_action( 'woocommerce_before_cart',  array( $this, 'cn_wc_shop_disabled' ), 5 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'cn_wc_shop_disabled' ), 5 );
	}
	 
	// Show Holiday Notice
	public function cn_wc_shop_disabled() {
	   wc_print_notice( 'We are not accepting any more orders. Thanks for understanding :)!', 'error');
	} 
	
	/** 
	 * Hide free shipping when the order weight is more than 10kgs. 
	 * 
	 * @param array $rates Array of rates found for the package. 
	 * @return array 
	 */ 
	public function cn_hide_show_shipping( $rates ) { 
	   $cn_form = get_option( 'cn_form', array() );
		if( $cn_form && array_key_exists('cn_store_delivery_management', $cn_form ) ) {
			foreach( $rates as $rate_id => $rate_val ) { 
			//print_r( $rate_val->get_method_id() );
				if ( 'free_shipping' === $rate_val->get_method_id() ) { 
					//unset( $rates[ $rate_id ] );
				} 
				
			} 
			
			$threshold = 100;
		   if ( WC()->cart->subtotal < $threshold ) {
			//  unset( $rates['flat_rate:4'] );
		   }
		  // return $rates;
				
		}
		
		return $rates; 
	} 	
	
	/**
	 * Return Current Day ID
	 *
	 * @return int
	 */
	public function cn_public_get_current_day_id() {
		switch ( strtolower( gmdate( 'D' ) ) ) {
			case 'mon' :
				return 10001;
			case 'tue' :
				return 10002;
			case 'wed' :
				return 10003;
			case 'thu' :
				return 10004;
			case 'fri' :
				return 10005;
			case 'sat' :
				return 10006;
			case 'sun' :
				return 10007;
		}
		return 10001;
	}

	/**
	 * Return Today's Schedules
	 *
	 * @return array|mixed
	 */
	public function get_todays_schedule() {
		$cn_form = get_option( 'cn_form', array() );
		if(empty( $cn_form ) ) {
			return array();
		}
		$all_schedules  = isset( $cn_form[ $this->cn_public_get_current_day_id()] ) ? $cn_form[ $this->cn_public_get_current_day_id()]: array();
		return apply_filters( 'cn_filters_get_todays_schedule', $all_schedules );
	}
	
	/**
	 * Return TimeZone String
	 *
	 * @return false|mixed|string|void
	 */
	public function get_timezone_string() {
		// if site timezone string exists, return it
		if ( $timezone = get_option( 'timezone_string' ) ) {
			return $timezone;
		}
		
		// get UTC offset, if it isn't set then return UTC
		if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
			return 'UTC';
		}

		// adjust UTC offset from hours to seconds
		$utc_offset *= 3600;

		// attempt to guess the timezone string from the UTC offset
		if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
			return $timezone;
		}

		// last try, guess timezone string manually
		$is_dst = gmdate( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}

		// fallback to UTC
		return 'UTC';
	}
	
		/**
	 * Return all days
	 *
	 * @return mixed|void
	 */
	public function get_days() {

		$days_array = array(
	
			'10001' => array(
				'name'  => esc_html( 'Monday' ),
				'label' => __( 'Monday', 'cn-close-store' ),
			),
			'10002' => array(
				'name'  => esc_html( 'Tuesday' ),
				'label' => __( 'Tuesday', 'cn-close-store' ),
			),
			'10003' => array(
				'name'  => esc_html( 'Wednesday' ),
				'label' => __( 'Wednesday', 'cn-close-store' ),
			),
			'10004' => array(
				'name'  => esc_html( 'Thursday' ),
				'label' => __( 'Thursday', 'cn-close-store' ),
			),
			'10005' => array(
				'name'  => esc_html( 'Friday' ),
				'label' => __( 'Friday', 'cn-close-store' ),
			),
			'10006' => array(
				'name'  => esc_html( 'Saturday' ),
				'label' => __( 'Saturday', 'cn-close-store' ),
			),
			'10007' => array(
				'name'  => esc_html( 'Sunday' ),
				'label' => __( 'Sunday', 'cn-close-store' ),
			),
		);

		return apply_filters( 'cn_filters_days_array', $days_array );
	}
	
	
	/**
	 * Return Next Opening Time
	 *
	 * @param string $time_for open | close | toggle
	 * @param string $format
	 *
	 * @return mixed|void
	 */
	function get_next_time( $time_for = '', $format = 'M j Y G:i:s' ) {

		$current_day = $this->cn_public_get_current_day_id();
		$times       = $this->calculate_times( $this->get_todays_schedule(), $time_for );

		if ( empty( $times ) ) {
		$cn_form = get_option('cn_form', array());
		$weekdays = [ 10001 => 'Monday', 10002 =>'Tuesday', 10003 =>'Wednesday', 10004 => 'Thursday', 10005 => 'Friday', 10006 => 'Saturday', 10007 => 'Sunday' ];
		   foreach ( $weekdays as $day_id => $schedules ) {
			   $store_same_closed = '';
			  if ( $current_day >= $day_id || ! empty( $times ) ) {
					continue;
				}
				
			   if( $cn_form && array_key_exists( $day_id, $cn_form ) ) {
				   	
				$all_weeks_values = $cn_form[$day_id];
				if( array_key_exists( 'cn_closed_open', $all_weeks_values ) && !empty($all_weeks_values['cn_closed_open'] ) ) {
					continue;
				}
				
				$times = $this->calculate_times( $all_weeks_values, $time_for, $this->get_day_name( $day_id ) );
			   }
		   }
		}
		$next_time = reset( $times );
		
		$cu_date_time = new DateTime( gmdate( $format, $next_time ), new DateTimeZone($this->get_timezone_string()) );
		
		$new_d_time   = $cu_date_time->format($format);

		return apply_filters( 'cn_filters_next_time', $new_d_time, $next_time, $format );
	}
	
	/**
	 * Calculate times
	 *
	 * @param array $__schedules
	 * @param string $__time_for
	 * @param string $__day_name
	 *
	 * @return mixed|void
	 */
	public function calculate_times( $__schedules = array(), $__time_for = '', $__day_name = '' ) {

		$__times        = array();
		$__time_for     = empty( $__time_for ) && ! in_array( $__time_for, array(
			'open',
			'close',
			'toggle',
		) ) ? 'toggle' : $__time_for;
		$__time_for     = $__time_for == 'toggle' && $this->is_open() ? 'close' : 'open';

		
		$cu_date_time = new DateTime("now", new DateTimeZone($this->get_timezone_string()) );
		$__current_time   = $cu_date_time->format('U');
		
		$__day_name     = empty( $__day_name ) ? $this->get_day_name() : $__day_name;
		$__day_name     = substr( $__day_name, 0, 3 );	
		
		// if current day is closed then we return back to check some next day time
		if( !empty( $__schedules['cn_time_from']['cn_closed_open'] ) ) {
			return apply_filters( 'cn_filters_calculate_times', $__times, $__time_for, $__day_name );
		}
		if( array_key_exists('cn_time_from', $__schedules) ) {
			foreach( $__schedules['cn_time_from']['time'] as $single_key => $single_time ) {
				if( $single_time == '00:00') {
					continue;
				}
				$from_open_time_period = isset( $__schedules['cn_time_from']['period'][$single_key]) ? $__schedules['cn_time_from']['period'][$single_key]: 'am' ;
				$ss_time_key = $__day_name . ' ' . $single_time. ' ' .$from_open_time_period;
				
				$cu_date_time_open = new DateTime( $ss_time_key, new DateTimeZone($this->get_timezone_string()) );
				
				$__this_time = $cu_date_time_open->format('U');
				if ( ! empty( $__this_time ) && $__current_time < $__this_time ) {
					$__times[] = $__this_time;
				}
			}
		}

		return apply_filters( 'cn_filters_calculate_times', $__times, $__time_for, $__day_name );
	}
	
	/**
	 * Return Current Day Name
	 *
	 * @param string $day_id
	 * @param bool $return_label
	 *
	 * @return mixed|void
	 */
	public function get_day_name( $day_id = '', $return_label = false ) {

		$day_id = empty( $day_id ) ? $this->cn_public_get_current_day_id() : $day_id;
		$day    = isset( $this->get_days()[ $day_id ] ) ? $this->get_days()[ $day_id ] : array();

		if ( $return_label ) {
			$day_name = isset( $day['label'] ) ? $day['label'] : __( 'Not Found!', 'cn-close-store' );
		} else {
			$day_name = isset( $day['name'] ) ? $day['name'] : __( 'Not Found!', 'cn-close-store' );
		}

		return apply_filters( 'cn_filters_day_name', $day_name, $day_id );
	}
	
	/**
	 * Return Whether shop is open or not
	 *
	 * @return mixed|void
	 */
	public function is_open() {
		// set the default timezone to use.
		
		$cu_date_time = new DateTime("now", new DateTimeZone($this->get_timezone_string()) );
		$current_time   = $cu_date_time->format('U');
	
		$cn_is_open     = false;		
		$today_schedules = $this->get_todays_schedule();
		if( empty( $today_schedules ) ) {
			$cn_is_open     = true;
		}
		if( !empty($today_schedules['cn_closed_open'] ) ) {
			return apply_filters( 'cn_is_open', $cn_is_open );
		}
		$set_zero_shop_flag = false;
		if( $today_schedules && array_key_exists('cn_time_from', $today_schedules) ) {
			foreach( $today_schedules['cn_time_from']['time'] as $single_key => $single_time ) {
				if( $single_time == '00:00') {
					$set_zero_shop_flag =  true;
					continue;
				}
				$set_zero_shop_flag =  false;
				$from_open_time_period = isset( $today_schedules['cn_time_from']['period'][$single_key]) ? $today_schedules['cn_time_from']['period'][$single_key]: 'am' ;
										
				$to_open_time = isset( $today_schedules['cn_time_closed']['time'][$single_key] ) ? $today_schedules['cn_time_closed']['time'][$single_key]: '11:59';
				
				$to_open_time_period = isset( $today_schedules['cn_time_closed']['period'][$single_key]) ? $today_schedules['cn_time_closed']['period'][$single_key] : 'pm';
				
				$cu_date_time_open = new DateTime( $single_time. ' ' .$from_open_time_period, new DateTimeZone($this->get_timezone_string()) );
				
				$open_time   = $cu_date_time_open->format('U');
				
				$cu_date_time_close = new DateTime( $to_open_time. ' '. $to_open_time_period, new DateTimeZone($this->get_timezone_string()) );
				$close_time = $cu_date_time_close->format('U');
				
				if ( empty( $open_time ) || empty( $close_time ) ) {
					continue;
				}
				if ( $current_time >= $open_time && $current_time <= $close_time ) {
					$cn_is_open = true;
				}
			}
		}
		if( $set_zero_shop_flag ) {
			$cn_is_open = true;
		}
		return apply_filters( 'cn_is_open', $cn_is_open );
	}
	
	/**
	 * Display Footer Content of Popup and Statusbar
	 */
	public function cn_display_popup_statusbar() {
		$cn_form = get_option('cn_form', array());
		$show_bar = false;
		// check if store closed
		if( $cn_form && array_key_exists( "cn_store_open", $cn_form ) && $cn_form['cn_store_open'] == 'on' ) {
			$show_bar = true;
		}

		if( $this->is_open() == false ) {
			$show_bar = true;
		}

		if( $show_bar ) {
		$time_diff     = gmdate( 'U', strtotime( $this->get_next_time() ) ) - gmdate( 'U' );
			?>
		<div class="shop-status-bar cnopenclose-bar-footer">
			<div class="shop-status-bar-inline status-message">
				<span><?php echo __( 'Offline ! We will start taking orders in', 'cn-close-store' ); ?>
				<div id="cnopenclose-countdown-timer-begin" class="cnopenclose-countdown-timer-1 ">
					<span style="display: none;" class="distance" data-distance="<?php echo esc_attr( $time_diff ); ?>"></span>
					<span class="hours"><span class="count-number">0</span> <span class="count-text"><?php echo __( 'Hours', 'cn-close-store' ); ?></span></span>
					<span class="minutes"><span class="count-number">0</span> <span class="count-text"><?php echo __( 'Minutes', 'cn-close-store' ); ?></span></span>
					<span class="seconds"><span class="count-number">0</span> <span class="count-text"><?php echo __( 'Seconds', 'cn-close-store' ); ?></span></span>
				</div>
				</span>    
			</div>
			<div class="shop-status-bar-inline close-bar"><span><?php echo __( 'Hide Message', 'cn-close-store' ); ?></span></div>
		</div>
		<script>
			(function ($) {
				"use strict";

				(function updateTime() {

					let timerArea = $("#cnopenclose-countdown-timer-begin"),
						spanDistance = timerArea.find('span.distance'),
						distance = parseInt(spanDistance.data('distance')),
						spanHours = timerArea.find('span.hours > span.count-number'),
						spanMinutes = timerArea.find('span.minutes > span.count-number'),
						spanSeconds = timerArea.find('span.seconds > span.count-number'),
						days = 0, hours = 0, minutes = 0, seconds = 0;

					if (distance > 0) {
						days = Math.floor(distance / (60 * 60 * 24));
						hours = Math.floor((distance % (60 * 60 * 24)) / (60 * 60) + days * 24);
						minutes = Math.floor((distance % (60 * 60)) / (60));
						seconds = Math.floor((distance % (60)));
					}

					spanHours.html(hours);
					spanMinutes.html(minutes);
					spanSeconds.html(seconds);
					spanDistance.data('distance', distance - 1);

					setTimeout(updateTime, 1000);
				})();

			})(jQuery);
		</script>
		<?php
		}
	}
}

