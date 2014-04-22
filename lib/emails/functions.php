<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_email_new_ticket_notification( $ticket_id ){
	$customer_id = sd_get_ticket_customer($ticket_id);
	$customer_email = sd_get_customer_email($customer_id);

	//if there is no email address, bail.
	if(empty($customer_email)){
		return false;
	}

	//set our email headers
	$headers = "From: " . stripslashes_deep( html_entity_decode( get_bloginfo('name'), ENT_COMPAT, 'UTF-8' ) ) . " <".get_option('admin_email').">\r\n";

	//notify the techs as well
	$techs = sd_get_technicians(false, true);
	foreach($techs as $id => $email){
		$headers .= 'Bcc: ' . $email . "\r\n";
	}

	$headers .= "Reply-To: ". get_option('admin_email') . "\r\n";
	//$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	//subject
	$subject = '[#' . absint($ticket_id) .'] New Ticket Created - ' . wp_strip_all_tags(sd_get_email_subject($ticket_id), true);
	
	//message
	//$message = sd_get_email_body_header();
	$message = wp_strip_all_tags( sd_get_email_body( $ticket_id ), true);
	//$message .= sd_get_email_body_footer();

	$mail = wp_mail($customer_email, $subject, $message, $headers);

	return $mail;
}

function sd_email_updated_ticket_notification( $ticket_id ){

}

