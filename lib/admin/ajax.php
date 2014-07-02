<?php
/**
 * AJAX Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) die();

function sd_check_customer_type(){
	if(!wp_verify_nonce($_POST['nonce'], 'sd_add_customer')) wp_die('Security Check');
	$type = sd_get_customer_type(absint($_POST['customer_id']));
	echo json_encode($type);
	die();
}

add_action( 'wp_ajax_sd_check_customer_type', 'sd_check_customer_type' );