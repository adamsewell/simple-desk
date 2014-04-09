<?php
/**
 * Admin Actions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function sd_process_actions() {
	if ( isset( $_POST['sd_action'] ) ) {
		do_action( 'sd_' . $_POST['sd_action'], $_POST );
	}

	if ( isset( $_GET['sd_action'] ) ) {
		do_action( 'sd_' . $_GET['sd_action'], $_GET );
	}
}
add_action( 'admin_init', 'sd_process_actions' );