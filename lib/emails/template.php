<?php
/**
 * Email Templates
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function sd_get_email_body_header() {
	ob_start();
?>
	<html>
			<head>
				<style type="text/css">#outlook a { padding: 0; }</style>
			</head>
		<body>
<?php
	return ob_get_clean();
}

function sd_get_email_body_footer() {
	ob_start();
?>
		</body>
	</html>
<?php
	return ob_get_clean();
}

function sd_get_email_body( $ticket_id ){
	$customer_id = sd_get_ticket_customer($ticket_id);

	$body = 'A new ticket has been created by ' . html_entity_decode( sd_get_customer_display_name($customer_id), ENT_COMPAT, 'UTF-8' ) . "\n\n";
	$body .= 'Issue: ' . html_entity_decode( sd_get_ticket_issue($ticket_id), ENT_COMPAT, 'UTF-8' ) . "\n\n";
	$body .= 'Details: ';

	return $body;
}

function sd_get_email_subject($ticket_id){
	$subject = sd_get_ticket_issue($ticket_id);
	return $subject;
}
