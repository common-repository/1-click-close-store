 jQuery( function() {
	 
	 jQuery( "select.cn-date-time-selection" ).select2({
		 width: '80px',
		 placeholder: "Select",
	 });
	 

	jQuery.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
	 jQuery( "select.cn-date-time-selection" ).select2({
		matcher: cnMatchStart
	  })
	});
 });
 
 jQuery( function() {
	'use strict';

	jQuery(document).ready(function() {
		jQuery('.cn_submit_btn').on('click', function(event) {
				setTimeout(function(){ 
					jQuery('.save_settings_info').trigger('click');
				}, 100);
		});
		jQuery('.cn_submit_delivery').on('change', function(event) {
				setTimeout(function(){ 
					jQuery('.save_settings_delivery').trigger('click');
				}, 100);
		});
		
		jQuery('.cn_checkbox_banner_message').on('click', function(event) {
			setTimeout(function(){ 
				jQuery('.save_banner_message').trigger('click');
			}, 100);
		});
	

	});

 });
 

function cnMatchStart(params, data) {
	 // If there are no search terms, return all of the data
    if (jQuery.trim(params.term) === '') {
      return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
      return null;
    }
	
	const searchedValue = params.term;
	
	if( searchedValue.indexOf(':') != -1 && data.text.indexOf(searchedValue) > -1 ) {
		var modifiedData = jQuery.extend({}, data, true);

		  // You can return modified objects from here
		  // This includes matching the `children` how you want in nested data sets
		  return modifiedData;
	}
	
	if (data.text.indexOf(searchedValue+":") > -1) {
		 var modifiedData = jQuery.extend({}, data, true);

		  // You can return modified objects from here
		  // This includes matching the `children` how you want in nested data sets
		  return modifiedData;
    }
	
	// Return `null` if the term should not be displayed
    return null;
    
}

function cn_close(){
	jQuery('.cn_msg').hide();
}