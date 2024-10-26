(function( $ ) {
	'use strict';
	$( document ).ready(function() {
		$('.cn_close').click(function() {
			$(".cn_model").hide();
			jQuery('#cn_model_body').html('');
		});

		$('.open_cn_model').click(function() {
			$('.mylod').show();
				var open_cn_model_id = $(this).attr('data-target');
				$(open_cn_model_id).show();
			setTimeout(function() {
				$('.mylod').hide();
			}, 1000);
		});
	})
})( jQuery );

function showmsg(type,title,text,Button=false,timer=1600){
	swal({
		type: 'success',
		title: 'Added successfully',
		text: '',
		showConfirmButton: false,
		timer: 1600
	});
}

function addRecord(title){
	jQuery('.mylod').show();
	jQuery.post(cn_plugin_vars.ajaxurl,{
		'action': 'survey_poll_ajax',
		'param': 'add_poll',
	}, function(response){
		jQuery('.cn_card-header .text').text(title);
		jQuery('#cn_model_body').html(response)
		jQuery(".cn_model").show();
		jQuery('.mylod').hide();
	});

}

function editRecord(id,title){
	jQuery(".cn_checkbox").prop({'checked':''});
	jQuery("#cn"+id).prop({'checked':'checked'});
	editAllRecord(title);
}
function editAllRecord(title){
	jQuery('.cn_card-header .text').text(title);
  	var poll_ids = jQuery("input[name='post']:checked").map(function() {
		 return this.value;
	}).get().join(',');
	if (poll_ids=='') {
		swal({
		type: 'warning',
		title: 'Please select at least one record',
		text: '',
		showConfirmButton: false,
		timer: 1600
	});
	}else{
		jQuery('.mylod').show();
		jQuery.post(cn_plugin_vars.ajaxurl,{
			'action': 'survey_poll_ajax',
			'param': 'edit_all_poll',
			'poll_ids': poll_ids
		}, function(response){
			jQuery('#cn_model_body').html(response)
			jQuery(".cn_model").show();
			jQuery('.mylod').hide();
		});
	}
}
function deleteRecord(id){
	jQuery(".cn_checkbox").prop({'checked':''});
	jQuery("#cn"+id).prop({'checked':'checked'});
	deleteAllRecord();
}
function deleteAllRecord(){
  	var poll_ids = jQuery("input[name='post']:checked").map(function() {
		 return this.value;
	}).get().join(',');
	if (poll_ids=='') {
		swal({
			type: 'warning',
			title: 'Please select at least one record',
			text: '',
			showConfirmButton: false,
			timer: 1600
		});
	}else{
		jQuery('.mylod').show();
		jQuery.post(cn_plugin_vars.ajaxurl,{
			'action': 'survey_poll_ajax',
			'param': 'delete_all_poll',
			'poll_ids': poll_ids
		}, function(response){
			jQuery('.mylod').hide();
			var poll_id=poll_ids.split(',');
				for(var i=0;i<poll_id.length;i++){
				        jQuery('.poll'+poll_id[i]).remove();
				}


			var response =JSON.parse(response);
			swal({
				type: 'success',
				title: response.msg,
				text: '',
				showConfirmButton: false,
				timer: 1600
			});
			
		});
	}
}



