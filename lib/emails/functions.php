<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_new_ticket_notification_techs( $ticket_id ){
	$tech = sd_get_ticket_tech($ticket_tech);
	$customer = sd_get_ticket_customer($ticket_id);

	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";
	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";

	$subject = '[#' . absint($ticket_id) .']: ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);

	$body = 'A new ticket has been created by: ' . sd_get_customer_display_name($customer) . "\r\n";
	$body .= 'Status: ' . sd_get_ticket_status($ticket_id) . "\r\n";
	$body .= '------------------------------------' . "\r\n";
	$body .= 'Content: ' . wp_strip_all_tags(sd_get_ticket_details($ticket_id)) . "\r\n";
	$body .= '------------------------------------' . "\r\n";


	if(!empty($tech)){
		//send notification to assigned tech only
		$tech_data = get_userdata(sd_get_ticket_tech($ticket_id));

		if(empty($tech_data->user_email)){
			return false;
		}

		$to = $tech_data->user_email;

	}else{
		$to = array_values(sd_get_technicians(false, true));
	}


	$mail = wp_mail($to, $subject, $body, $headers);
	return $mail;
}

function sd_email_updated_ticket_notification( $ticket_id ){

}

