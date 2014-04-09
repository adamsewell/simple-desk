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

function sd_get_email_body($notification = ''){
	
}

function sd_get_email_subject($ticket_id){
	$subject = sd_get_ticket_issue($ticket_id);
	return $subject;
}
