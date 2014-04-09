<?php
/**
 * AJAX Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die();


// function sd_ajax_add_ticket_reply(){
// 	if(!wp_verify_nonce($_POST['nonce'], 'shopp_admin_orders_nonce')) wp_die('Security Check');

// 	$ticket_id = absint($_POST['ticket_id']);
// 	$reply = sanitize_text_field($_POST['reply']);
// 	$private = $_POST['private_reply'];

// 	if(sd_save_ticket_reply($ticket_id, $reply, $private)){
// 		echo json_encode(sd_render_ticket_history($ticket_id));
// 	}else{
// 		echo json_encode(array('failed'));
// 	}

// 	die();
// }

// add_action( 'wp_ajax_sd_ticket_reply', 'sd_ajax_add_ticket_reply' );