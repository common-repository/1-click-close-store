<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://mozzoplugins.com/
 * @since      1.0.0
 *
 * @package    Cn_Close_Store
 * @subpackage Cn_Close_Store/admin/partials
 */
 
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="cn_col-md-12 mt-5 cn_col_wrap">
      <div class="cn_col-md-12">
        <div class="cn_card">
          <div class="cn_card-body">
			<script>
			jQuery( function() {				
					
				jQuery('body').on('click', '.ampm-picker .period', function() {
					// Get the value clicked, am or pm?
					var per = jQuery(this).attr('class').substr(7);
					
					// set it in the hidden var
					jQuery(jQuery).parent().children('input').val(per);
					
					// highlight the choice (de-highlight any previous choice)					
					jQuery('input:not(:checked)').parent().removeClass('chosen-period');
					jQuery('input:checked').parent().addClass("chosen-period");
				});
			});
		
		  jQuery( function() {
					jQuery('.icons-wrap input:radio').on('change', function() {
						
					// Only remove the class in the specific `box` that contains the radio
						jQuery('div.active_radio').removeClass('active_radio');
						jQuery(this).closest('.cn_col-md-3').addClass('active_radio');
					});
				
				
					const senderTableSelector = jQuery( 'table#default-hours-table #add_custom_more_time' );
					senderTableSelector.on( 'click', 'a.cn_add_more_time', function() {
						const dataCurrentWeek = jQuery( this ).closest( 'tbody tr.date-time-row' ).find( '.weekend_value' ).val();
						const rowSize = jQuery( '#default-hours-table' ).find( "[data-attr-type="+dataCurrentWeek+"]" ).length;
						
						// only allow max 3 time in a day
						if( rowSize > 2 ) {
							 return; 
						}
					
						let jsHtml = '';
						jsHtml += "<tr class='cn_data key date-time-row' data-attr-type='"+dataCurrentWeek+"'>";
						jsHtml += "<td></td>";
						jsHtml += "<td><select name='"+dataCurrentWeek+"[cn_time_from][time]["+ rowSize +"]'  class='cn-date-time-selection'><option>00:00</option>";
							<?php $start = "1:00";
								$end = "12:59";

								$tStart = strtotime( $start );
								$tEnd = strtotime( $end );
								$tNow = $tStart;
								while( $tNow <= $tEnd ) { 
									$timetoprint = gmdate( "h:i", $tNow ); ?>
									jsHtml += "<option value='<?php echo esc_attr($timetoprint); ?>'><?php echo esc_html($timetoprint); ?></option>";
									<?php $tNow = strtotime('+01 minutes', $tNow);
								} ?>
						jsHtml += "</select>";
						jsHtml += "<div class='cn_radio_button'><span class='ampm-picker'>";
						jsHtml += "<label class='period'><input type='radio' name='"+dataCurrentWeek+"[cn_time_from][period]["+ rowSize +"]' class='cm_radio' value='am'><span class='value'>AM</span></label>";
						jsHtml += " | "; 
						jsHtml += "<label class='period'><input type='radio' name='"+dataCurrentWeek+"[cn_time_from][period]["+ rowSize +"]' class='cm_radio' value='pm'><span class='value'>PM</span></label>";		
						jsHtml += "</span></div>";
						jsHtml += "</td>";
						jsHtml += "<td><select name='"+dataCurrentWeek+"[cn_time_closed][time]["+ rowSize +"]' class='cn-date-time-selection'><option>00:00</option>";
								<?php 							
								$start = "1:00";
								$end = "12:59";

								$tStart = strtotime( $start );
								$tEnd = strtotime( $end );
								$tNow = $tStart;
								while( $tNow <= $tEnd ) { 
									$timetoprint = gmdate( "h:i", $tNow ); ?>
									jsHtml += "<option value='<?php echo esc_attr($timetoprint); ?>'><?php echo esc_html($timetoprint); ?></option>";
									<?php $tNow = strtotime('+01 minutes', $tNow);
								}
							?>
						jsHtml += "</select>";
											
						jsHtml += "<div class='cn_radio_button'><span class='ampm-picker'>";
						jsHtml += "<label class='period'><input type='radio' name='"+dataCurrentWeek+"[cn_time_closed][period]["+ rowSize +"]' class='cm_radio' value='am'><span class='value'>AM</span></label>";
						jsHtml += " | "; 
						jsHtml += "<label class='period'><input type='radio' name='"+dataCurrentWeek+"[cn_time_closed][period]["+ rowSize +"]' class='cm_radio' value='pm'><span class='value'>PM</span></label>";		
						jsHtml += "</span></div>";
						jsHtml += "</td>";
						
						jsHtml += "<td>";
						jsHtml += "<div class='cn_time_wrap' id='cn_eliminate_time'>";
						jsHtml += "<a href='javascript:void(0);' class='cn_eliminate_time'><?php esc_html_e( 'Eliminate', 'cn-close-store' ); ?></a>";
						jsHtml += "</div>";
						jsHtml += "</td>";
						jsHtml += "</tr>";
						jQuery( jsHtml ).insertAfter( jQuery( this ).parent().parent().parent() );
						select2Events();
					});

					jQuery( 'table#default-hours-table' ).on( 'click', 'tr.date-time-row', function() {
						const currentSelectionTr = jQuery( this );
						jQuery( 'table#default-hours-table tr.date-time-row' ).each( function() {
							jQuery( this ).removeClass( 'selected-row' );
						});

						currentSelectionTr.addClass( 'selected-row' );
					});
								
					jQuery('body').on( 'click', '#cn_eliminate_time .cn_eliminate_time', function()	{		
						jQuery( this ).closest( 'tr.date-time-row' ).fadeOut( "slow" ).remove();
					});
					
					jQuery('body').on( 'click', '.cn_clear_all', function()	{	

						jQuery( "select.cn-date-time-selection" ).each( function() {
							jQuery( this ).find("option:first").attr('selected','selected');
							jQuery( this ).trigger("change");
						});
						jQuery( "input.cm_radio" ).each( function() {
							jQuery( this ).prop('checked', false);
							//jQuery( this ).trigger("change");
							//jQuery( this ).val(null).trigger("change");
							jQuery( this ).parent().removeClass("chosen-period");
						});
						
						jQuery( "input.cn_closed_open" ).each( function() {
							jQuery( this ).prop('checked', false);
						});
					});
				
			});
			function select2Events() {
				// select2 js add when added new field
				jQuery( 'select.cn-date-time-selection' ).select2({
					width: '80px',
					placeholder: "Select"
				});
				// select2 match function
				jQuery.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
				 jQuery( "select.cn-date-time-selection" ).select2({
					matcher: cnMatchStart
				  })
				});
			}
			

			</script>
			  <?php
			  if(!empty( $cn_points ) ) { ?>
				<script>
					jQuery( function() {
						//  disable ALL descendants of the DIV
						jQuery( "div.bottom-wrap" ).children().prop('disabled',true);
						 jQuery("div.bottom-wrap :input").prop("disabled", true);
						jQuery( "form.banner-on-message, form.business-on-message,div.bottom-wrap").fadeTo('500',.6).css( "cursor", 'not-allowed' );
					});
				</script>
				<?php
			  }
			    $only_delivery = plugin_dir_url( __FILE__ ) . '../icon/moto.png'; 		  
			    $only_pickup = plugin_dir_url( __FILE__ ) . '../icon/showroom.png';
			    $cn_store_open_status_check = $cn_store_show_banner_status_check = $cn_store_show_banner_message_check = '';
				$cn_store_pickup_check = '';
			    $cn_store_open_status = 'open';
				if( $cn_form && array_key_exists( "cn_store_open", $cn_form ) && $cn_form['cn_store_open'] == 'on' ) {
					$cn_store_open_status_check = 'checked';
					$cn_store_open_status = 'closed';
				}
				
				if( $cn_form && array_key_exists( 'cn_store_delivery_management', $cn_form ) && !empty( $cn_form['cn_store_delivery_management'] ) ) {
					$cn_store_pickup_check =  $cn_form['cn_store_delivery_management'];
				}
				if ( $sm_fs && sm_fs()->is__premium_only() ) {
					if ( sm_fs()->can_use_premium_code() ) {
						
						if( $cn_form && array_key_exists( 'cn_store_closed_banner', $cn_form ) && $cn_form['cn_store_closed_banner'] == 'on' ) {
							$cn_store_show_banner_status_check = 'checked';
						}
						
						if( $cn_form && array_key_exists( "cn_textarea_store_banner", $cn_form ) ) {
							$cn_store_show_banner_message_check = $cn_form['cn_textarea_store_banner'] ;
						}
					}
				}
			  ?>
			<div class="wrap">
			<?php
				if( $sm_fs && $sm_fs->is_not_paying() && !$sm_fs->is_trial()) {
					$up_url =	'<a href="' . esc_url( $sm_fs->get_trial_url() ) . '">' .
							__('Start your free trial now!', 'cn-close-store') .
							'</a>'; ?>
					
					<h3><?php esc_html_e( 'All these awesome features are available in the premium plan. Try them with a free trial! ', 'cn-close-store'); echo wp_kses_post( $up_url ); ?></h3>
				<?php }
				?>
			</div>
			
			<div class="cn-custom-wrap">
			<h1><?php esc_html_e( 'Store Open & Delivery Manager', 'cn-close-store' ); ?></h1>
			
			 <div class="cn_col-md-12">
				<div class="row">
                    <div class="cn_col-md-4">
					<form class="form-horizontal cn_submit" action="" method="post">
					 <?php wp_nonce_field( 'nonce_settings_open_close', '_open_close_nonce' ); ?>
					  <div class="cn_col-sm-8" style="display: none">
						<button type="submit" name="save_settings_info" class="save_settings_info button button-primary button-large"></button>
					  </div>
                      <h2><?php echo $cn_store_open_status_check ? __( 'Open Store', 'cn-close-store' ):  __( 'Close Store', 'cn-close-store' ); ?></h2>
					  <label class="switch">
                        <input type="checkbox" name="cn_store_open" <?php echo esc_html( $cn_store_open_status_check ); ?> class="cn_checkbox cn_submit_btn mobile_responsive">
                        <span class="cn_slider round" <?php echo esc_html( $cn_store_open_status_check ); ?>></span>
                      </label>
					  <p><?php printf( esc_html__( 'Store is now %s', 'cn-close-store' ), esc_html( $cn_store_open_status ) ); ?></p>
					  </form>
                    </div>
					<div class="cn_col-md-8">
                      <h2><?php esc_html_e( 'Delivery & Pick Up Management', 'cn-close-store' ); ?></h2>
					  <form class="form-horizontal" action="" method="post">
						   <?php wp_nonce_field( 'nonce_settings_delivery_pickup', '_delivery_pickup_nonce' ); ?>
							<div class="cn_col-sm-8" style="display: none">
								<button type="submit" name="save_settings_delivery" class="save_settings_delivery button button-primary button-large"></button>
							</div>
							<div class="icons-wrap">
							  <div class="row" >
								  <div class="cn_col-md-3 cn-radio-buttons <?php echo 'pickup' === $cn_store_pickup_check ? 'active_radio' :''; ?>">			
									<label><img src="<?php echo esc_url( $only_pickup ); ?>"/>
										<p><?php esc_html_e( 'Only pickup', 'cn-close-store' ); ?></p>
									</label>
									<input type="radio" <?php echo 'pickup' === $cn_store_pickup_check ? 'selected' :''; ?> name="delivery_status" id="only_pickup" class="cn_submit_delivery" value="pickup">
								  </div>
								   <div class="cn_col-md-3 cn-radio-buttons <?php echo 'delivery' === $cn_store_pickup_check ? 'active_radio' :''; ?>">
									<label><img src="<?php echo esc_url( $only_delivery ); ?>"/>
										<p><?php esc_html_e( 'Only delivery', 'cn-close-store' ); ?></p>
									</label>
									<input type="radio" <?php echo 'delivery' === $cn_store_pickup_check ? 'selected' :''; ?> name="delivery_status" id="only_delivery"  class="cn_submit_delivery" value="delivery">
								   </div>
							  </div>
							</div>
						  <p><?php 
						  echo empty( $cn_store_pickup_check ) ? __( 'Press to activate', 'cn-close-store' ): sprintf( __( 'Only %s option selected', 'cn-close-store' ), esc_html( $cn_store_pickup_check ) );

						  ?></p>
						</form>
                    </div>
                  </div>
				</div>
				
				<div class="cn_col-md-12 bottom-wrap">
				<div class="row">
				<div class="cn_col-md-8">
					<form class="form-horizontal business-on-message" action="" method="post">
						<?php wp_nonce_field( 'nonce_settings_business_message'.$cn_points, '_business_message_nonce'.$cn_points ); ?>
					
						<table class="cn-table default-hour-class" id="default-hours-table">
						<tr>
							<td colspan="2" style="padding: 0;">
								<h2 class="cn-heading"><?php esc_html_e( 'Business Hours', 'cn-close-store' ); ?></h2>
								<span class="cn-small-text"><?php esc_html_e( 'If a given day has no time selected the store it\'s going to be open 24 hrs that given day.', 'cn-close-store' ); ?></span>
							</td>
							<td colspan="2" style="padding: 0;">
								<button name="save_business" class="cn_btn cn_btn_time_saved button-primary" type="submit" value="Save changes" <?php echo !empty($cn_points) ? 'disabled': '';?>><?php esc_html_e( 'Save', 'cn-close-store' ); ?></button>
							</td>
						</tr>
						  <tr>
							<th><?php esc_html_e( 'Days', 'cn-close-store' ); ?></th>
							<th><?php esc_html_e( 'Open From', 'cn-close-store' ); ?></th>
							<th><?php esc_html_e( 'Close', 'cn-close-store' ); ?></th>
							<th style="text-align:center"><a <?php echo !empty($cn_points) ? 'disabled': '';?> href="javascript:void(0);" class="cn_clear_all"><?php esc_html_e( 'Clear All', 'cn-close-store' ); ?></a></th>
						  </tr>
						  <?php 
						  $i = 0;
						  $weekdays = [ 10001 => 'Monday', 10002 => 'Tuesday', 10003 => 'Wednesday', 10004 => 'Thursday', 10005 => 'Friday', 10006 => 'Saturday', 10007 => 'Sunday' ];
						   foreach ( $weekdays as $weekday_key => $weekday_formatted ) {
							   $store_same_closed = '';
							  
							   if( $cn_form && array_key_exists( $weekday_key, $cn_form ) ) {
									
									$all_weeks_values = $cn_form[$weekday_key];
									$store_same_closed = ( array_key_exists( 'cn_closed_open', $all_weeks_values ) ) ? 'checked' :''; 
									$j = 0;
									
									foreach( $all_weeks_values['cn_time_from']['time'] as $single_key => $single_time ) {
										// assing some variable
										$from_open_time = $single_time;
										
										$from_open_time_period = isset( $all_weeks_values['cn_time_from']['period'][$single_key]) ? $all_weeks_values['cn_time_from']['period'][$single_key]: 'am' ;
									
										$top_open_time = isset( $all_weeks_values['cn_time_closed']['time'][$single_key] ) ? $all_weeks_values['cn_time_closed']['time'][$single_key]: '11:59';
										
										$to_open_time_period = isset( $all_weeks_values['cn_time_closed']['period'][$single_key]) ? $all_weeks_values['cn_time_closed']['period'][$single_key] : 'pm';
											
										if( $j > 0 ) {
									
											cn_common_business_hours( $weekday_key, '', $store_same_closed, $j, true, $from_open_time, $from_open_time_period, $top_open_time,$to_open_time_period );
										} else {
											cn_common_business_hours( $weekday_key, $weekday_formatted, $store_same_closed, $j, false, $from_open_time, $from_open_time_period, $top_open_time,$to_open_time_period);
										}
										// increment the key 
										$j++;
									}
								} else {
									cn_common_business_hours( $weekday_key, $weekday_formatted, $store_same_closed, $i, false );
								} 
						   }?>
						</table>
                      </form>
                    </div>
                    <div class="cn_col-md-4">
					 <h2><?php esc_html_e( 'Banner Message', 'cn-close-store' ); ?></h2>
						<form class="form-horizontal banner-on-message" action="" method="post">
						 <?php wp_nonce_field( 'nonce_settings_banner_message'.$cn_points, '_banner_message_nonce'.$cn_points ); ?>
			
						 <label><?php echo $cn_store_show_banner_status_check != 'checked' ? __( 'Turn ', 'cn-close-store' ) .'on': __( 'Turn ', 'cn-close-store' ) . 'off'; ?></label><br>
							
						  <label class="switch">
							<input type="checkbox" name="cn_store_closed_banner" <?php echo esc_attr( $cn_store_show_banner_status_check ); ?> class="cn_checkbox mobile_responsive cn_checkbox_banner_message<?php echo esc_attr( $cn_points ); ?>">
							<span class="cn_slider round"></span>
						  </label>
						  <textarea class="cn_textarea" name="cn_textarea_store_banner" rows="8" cols="35" maxlength="70" placeholder="<?php esc_attr_e( 'When activated, this message will appear to your users as banner in your store\'s homepage.', 'cn-close-store' ); ?>"><?php echo esc_html( trim( $cn_store_show_banner_message_check ) ); ?> </textarea>
						  <button name="save" class="save_banner_message button-primary" type="submit" value="Save changes" <?php echo !empty($cn_points) ? 'disabled': '';?>><?php esc_html_e( 'Save', 'cn-close-store' ); ?></button>
					   </form>
                    </div>
                  </div>
				</div>
			   </div>
          </div>
        </div>
      </div>
</div>
<?php

function cn_common_business_hours( $weekday_key, $weekday_formatted, $store_same_closed, $i, $repeat_entry = false, $from_open_time = '', $from_open_time_period = '', $to_open_time = '',$to_open_time_period= '' ) {
	?>
	<tr class="cn_data key date-time-row" data-attr-type="<?php echo esc_attr( $weekday_key ); ?>">
	   <input type="hidden" class='weekend_value' value="<?php echo esc_attr( $weekday_key ); ?>">
		<td><?php echo esc_html( $weekday_formatted ); ?></td>
		<td>
			<select name="<?php echo esc_attr( $weekday_key ); ?>[cn_time_from][time][<?php echo esc_attr( $i ); ?>]" class='cn-date-time-selection'>
			<option>00:00</option>
			<?php cn_display_drop_down_time( $from_open_time ); ?>
		  </select>
		  <div class="cn_radio_button">
			  <span class="ampm-picker">
				<label class="period <?php echo $from_open_time_period == 'am' ? 'chosen-period' : ''; ?>">
					<input type="radio" name="<?php echo esc_attr( $weekday_key ); ?>[cn_time_from][period][<?php echo esc_attr( $i ); ?>]" class="cm_radio " value="am" <?php echo $from_open_time_period == 'am' ? "checked" : ""; ?> >
					<span class="value">AM</span>
				</label> |  
				
				<label class="period <?php echo $from_open_time_period == 'pm' ? 'chosen-period' : ''; ?>">
					<input type="radio" name="<?php echo esc_attr( $weekday_key ); ?>[cn_time_from][period][<?php echo esc_attr( $i ); ?>]" class="cm_radio " value="pm" <?php echo $from_open_time_period == 'pm' ? 'checked' : ''; ?>>
					<span class="value">PM</span>
				</label>
			</span>
		</div>
		</td>
		<td>
		<select name="<?php echo esc_attr( $weekday_key ); ?>[cn_time_closed][time][<?php echo esc_attr( $i ); ?>]"  class='cn-date-time-selection'>
				<option>00:00</option>
			<?php cn_display_drop_down_time( $to_open_time ); ?>
		  </select>
		  <div class="cn_radio_button">
			  <span class="ampm-picker">
				<label class="period <?php echo $to_open_time_period == 'am' ? 'chosen-period' : ''; ?>">
					<?php $name_ampm_picker = $weekday_key.'[cn_time_closed][period]['.$i.']'; ?>
					<input type="radio" name="<?php echo esc_attr( $name_ampm_picker ); ?>" class="cm_radio " value="am" <?php echo $to_open_time_period == 'am' ? 'checked' : ''; ?>>
					<span class="value">AM</span>
				</label> |  

				<label class="period <?php echo $to_open_time_period == 'pm' ? 'chosen-period' : ''; ?>">
					<input type="radio" name="<?php echo esc_attr( $weekday_key ); ?>[cn_time_closed][period][<?php echo esc_attr( $i ); ?>]" class="cm_radio" value="pm" <?php echo $to_open_time_period == 'pm' ? 'checked' : ''; ?>>
					<span class="value">PM</span>
				</label>
			</span>
		</div>
		</td>
		<td>
		<?php if( $repeat_entry == false ) {?>
			<div class="cn_time_wrap" id="add_custom_more_time">
				<a href='javascript:void(0);' class='cn_add_more_time'><?php esc_html_e( 'Add Time', 'cn-close-store' ); ?></a>
				
				<span style="display: inline-table;"><span class="cn_closed_open_label"><?php echo !empty( $store_same_closed ) ? __( 'Open Store', 'cn-close-store' ) : __( 'Close Store', 'cn-close-store' ); ?></span><br>
				
				<label class="switch">
					<input type="checkbox" name="<?php echo esc_attr( $weekday_key ); ?>[cn_closed_open]" <?php echo esc_attr( $store_same_closed ); ?> class="mobile_responsive cn_checkbox_business_message cn_closed_open">
					<span class="cn_slider round"></span>
				</label>
				</span>
			</div>
		<?php } else { ?> 
			<div class='cn_time_wrap' id='cn_eliminate_time'>
				<a href='javascript:void(0);' class='cn_eliminate_time'><?php esc_html_e( 'Eliminate', 'cn-close-store' ); ?></a>
			</div>
		<?php } ?>
		</td>
	  </tr>
	<?php
 } 
 
 function cn_display_drop_down_time( $match_time ) {
	$start = "1:00";
	$end = "12:59";

	$tStart = strtotime( $start );
	$tEnd = strtotime( $end );
	$tNow = $tStart;
	while( $tNow <= $tEnd ) { 
		$timetoprint = gmdate( "h:i", $tNow ); ?>
		<option value="<?php echo esc_attr($timetoprint); ?>" <?php echo $match_time == $timetoprint ? 'selected' : ''; ?> ><?php echo esc_html( $timetoprint ); ?></option>
		<?php $tNow = strtotime('+01 minutes', $tNow);
	}
}
