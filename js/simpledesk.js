jQuery(document).ready(function($){

	/*********************/
	/*	Customer Page  */
	/********************/
	$('#customer-country').chosen();
	$('#customer-type').chosen();

	/*********************/
	/*	Add Ticket Page  */
	/********************/
	$('#ticket-customer').chosen({
		width: '612px'
	});

	/*********************/
	/*	Validation       */
	/********************/

	$('#sd-ticket').validate({
		errorPlacement: function(error, element){
    		return true;
  		}
	});

	$('#sd-customer').validate({
		errorPlacement: function(error, element){
			return true;
		}
	})

	/*********************/
	/*Admin Ticket Reply*/
	/********************/
	// $('#response-submit').on('click', function(e){
	// 	$('#response-loading').show();

	// 	var message_data = {
	// 		action: 'sd_ticket_reply',
	// 		ticket_id: $('#ticket-id').val(),
	// 		reply: $('#response-message').val(),
	// 		private_reply: $('#response-private').is(':checked')
	// 	};

	// 	$.post(ajaxurl, message_data, function(response){
	// 		//clear old data
	// 		$('#response-message').val('');
	// 		$('#response-private').attr('checked', false);

	// 		//update the responses
	// 		var data = $.parseJSON(response);
	// 		$('#ticket_history').html(data).fadeIn();
	// 		$('#response-loading').hide();
	// 	});
	// });
});