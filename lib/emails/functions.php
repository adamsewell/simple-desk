<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_new_ticket_notification_techs( $ticket_id ){
	$tech = sd_get_ticket_tech($ticket_id);
	$customer = sd_get_ticket_customer($ticket_id);

	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";
	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";

	$subject = '[#' . absint($ticket_id) .']: ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);

	$body = 'A new ticket has been created.' . "\r\n";
	$body .= "\r\n";
	$body .= 'Status: ' . sd_get_ticket_status($ticket_id) . "\r\n";
	$body .= 'Ticket Content:' . "\r\n";
	$body .= '------------------------------------' . "\r\n";
	$body .= wp_kses_post(sd_get_ticket_details($ticket_id)) . "\r\n";
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

function sd_assigned_tech_notify( $ticket_id, $new_tech_email ){
	if(!filter_var($new_tech_email, FILTER_VALIDATE_EMAIL)){
		return false;
	}

	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";
	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";

	$subject = '[#' . absint($ticket_id) .']: ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);

	$body = 'You have been assigned this ticket for: ' . sd_get_ticket_contact_name($ticket_id) . "\r\n";
	$body .= 'Created By: ' . sd_get_tech_display_name(sd_get_ticket_creator($ticket_id)) . "\r\n";
	$body .= 'Status: ' . sd_get_ticket_status($ticket_id) . "\r\n";
	$body .= "\r\n";
	$body .= '------------------------------------' . "\r\n";
	$body .= wp_kses_post(sd_get_ticket_details($ticket_id)) . "\r\n";
	$body .= '------------------------------------' . "\r\n";

	$mail = wp_mail($new_tech_email, $subject, $body, $headers);

	return $mail;
}

function sd_new_ticket_notification_customer( $ticket_id ){
	$customer = sd_get_ticket_customer($ticket_id);
	$statuses = sd_get_ticket_statuses();

	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";
	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";
	if(sd_ticket_has_cc($ticket_id)){
		$headers .= "Cc: " . sd_ticket_get_cc($ticket_id, true) . "\r\n";
	}

	$subject = '[#' . absint($ticket_id) .']: ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);

	$body = 'A new ticket has been created. You may reply to this email to update your ticket. ' . "\r\n";
	$body .= "\r\n";
	$body .= 'Status: ' . $statuses[sd_get_ticket_status($ticket_id)] . "\r\n";
	$body .= 'Ticket Content: ' . "\r\n";
	$body .= '------------------------------------' . "\r\n";
	$body .= wp_kses(sd_get_ticket_details($ticket_id), array()) . "\r\n";
	$body .= '------------------------------------' . "\r\n";

	$to = sd_get_ticket_contact_email($ticket_id);

	$mail = wp_mail($to, $subject, $body, $headers);

	WP_Logging::add('Customer Email Sent', json_encode(array('customer' => $customer, 'to' => $to, 'subject' => $subject), $ticket_id, 'outgoing_email'));

	return $mail;
}

function sd_updated_ticket_notification_customer($ticket_id, $reply_id){
	$customer = sd_get_ticket_customer($ticket_id);
	$statuses = sd_get_ticket_statuses();

	//headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";
	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";
	if(sd_ticket_has_cc($ticket_id)){
		$headers .= "Cc: " . sd_ticket_get_cc($ticket_id, true) . "\r\n";
	}

	$subject = '[#' . absint($ticket_id) .']: Updated - ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);

	$body = 'Ticket updated by: ' . sd_get_tech_display_name(sd_get_ticket_reply_author($reply_id)) . "\r\n";
	$body .= "\r\n";
	$body .= 'Status: ' . $statuses[sd_get_ticket_status($ticket_id)] . "\r\n";
	$body .= 'Ticket Reply: ' . "\r\n";
	$body .= '------------------------------------' . "\r\n";
	$body .= wp_kses(sd_get_ticket_reply($reply_id), array()) . "\r\n";
	$body .= '------------------------------------' . "\r\n";

	$to = sd_get_ticket_contact_email($ticket_id);

	$mail = wp_mail($to, $subject, $body, $headers);

	WP_Logging::add('Customer Email Update Sent', json_encode(array('customer' => $customer, 'to' => $to, 'subject' => $subject), $ticket_id, 'outgoing_email'));

	return $mail;
}
