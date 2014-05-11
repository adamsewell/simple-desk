<?php
/**
 * Email Templates
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function sd_get_email_body_header() {
	ob_start();
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta name="viewport" content="width=device-width"/>
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

	ob_start();
?>

<?php
	return ob_get_clean();
}

function sd_get_email_subject($ticket_id){
	$subject = sd_get_ticket_issue($ticket_id);
	return $subject;
}
