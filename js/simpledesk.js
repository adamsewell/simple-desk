jQuery(document).ready(function($){

	/*********************/
	/*	Customer Page  */
	/********************/
	$('#customer-country').chosen();
	$('#customer-type').chosen();
	$('#customer-state').chosen();

	/*********************/
	/*	Add Ticket Page  */
	/********************/
	$('#ticket-customer').chosen({
		width: '612px'
	});

	$('#ticket-customer').on('change', function(event, params){
		$('#response-loading').show();
		var cid = $('#ticket-customer').val();

		var customer_data = {
			action: 'sd_check_customer_type',
			customer_id: cid,
			nonce: $('#sd_add_customer_nonce').val()
		}

		$.post(ajaxurl, customer_data, function(response){
			var r = $.parseJSON(response);
			console.log(r);
			if(r.type == 'commercial'){
				$('#ticket\\[cname\\]').attr('placeholder', r.name);
				$('#ticket\\[cemail\\]').attr('placeholder', r.email);
				$('#ticket\\[cphone\\]').attr('placeholder', r.phone);
				$('.ticket_perferred_contact').slideDown();
			}else{
				$('.ticket_perferred_contact').slideUp();
			}
			$('#response-loading').hide();
		});

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
});