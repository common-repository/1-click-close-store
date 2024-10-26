/* global frontend_cn_shipping_object*/

jQuery( function(){

	if( frontend_cn_shipping_object.check_delivery_mgt.length > 0 ) {
		const shippingChoice = frontend_cn_shipping_object.check_delivery_mgt;
		if( frontend_cn_shipping_object.check_checkout_page ) {
			jQuery(document).ajaxComplete(function () { 
				jQuery( 'form.checkout input[name^="shipping_method"]' ).each(function( index ) {
				  let vals = jQuery( this ).val();
				   if ( shippingChoice == 'pickup' && vals.match("^local_pickup") ) {
					 jQuery( this ).parent().siblings().css( "display",'none' );
					 jQuery( this ).prop( "checked", true );
				   } else if( shippingChoice == 'delivery' && vals.match( "^flat_rate" ) ) {
					 jQuery( this ).parent().siblings().css( "display",'none' );
					 jQuery( this ).prop( "checked", true );
				   }
				});
			});
		}else if( frontend_cn_shipping_object.check_cart_page ) {
			jQuery('form.woocommerce-cart-form').on('change', 'input[name^="shipping_method"]', function () {
				 jQuery( 'body input[name^="shipping_method"]' ).each(function( index ) {
					  let vals = jQuery( this ).val();
					   if ( shippingChoice == 'pickup' && vals.match("^local_pickup") ) {
						  jQuery( this ).parent().siblings().css( "display",'none' );
					   } else if( shippingChoice == 'delivery' && vals.match( "^flat_rate" ) ) {
							jQuery( this ).parent().siblings().css( "display",'none' );
					   }
					});
			  });
			
			jQuery( 'body input[name^="shipping_method"]' ).each(function( index ) {
			  let vals = jQuery( this ).val();
			   if ( shippingChoice == 'pickup' && vals.match("^local_pickup") ) {
				   jQuery( this ).parent().siblings().css( "display",'none' );
				   jQuery( this ).prop( "checked", true );
				   jQuery('p.woocommerce-shipping-destination').css("display", "none");
				   jQuery('.woocommerce-shipping-calculator').css("display", "none");
							
			   } else if( shippingChoice == 'delivery' && vals.match( "^flat_rate" ) ) {
				 jQuery( this ).parent().siblings().css( "display",'none' );
				 jQuery( this ).prop( "checked", true );
			   }
			});
		}
	}
	
	jQuery( "body .cnopenclose-bar-footer" ).on( "click",'.close-bar', function() {
		jQuery('body .cnopenclose-bar-footer').hide();
	});
	
});


function cn_close(){
	jQuery('.cn_msg').hide();
}

