<?php
/*
*	Installation
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_install(){
	// Add Upgraded From Option
	$current_version = get_option( 'sd_version' );
	if ( $current_version ) {
		update_option( 'sd_version_upgraded_from', $current_version );
	}

	sd_add_user_roles();
	sd_add_user_caps();

	update_option('sd_version', SIMPLEDESK_VERSION);
}
register_activation_hook( SIMPLEDESK_FILE, 'sd_install' );

function sd_add_user_roles(){
	add_role('sd_tech', __('Simple Desk Technician', 'sd'), array(
		'read' => true,
		'upload_files' => true
	));
}

function sd_add_user_caps(){
	global $wp_roles;

	if ( class_exists('WP_Roles') )
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

	if(is_object($wp_roles)){
		$wp_roles->add_cap('administrator', 'read_sd_tickets');
		$wp_roles->add_cap('administrator', 'edit_sd_tickets');
		$wp_roles->add_cap('administrator', 'publish_sd_tickets');
		$wp_roles->add_cap('administrator', 'delete_sd_tickets');
		$wp_roles->add_cap('administrator', 'read_sd_customers');
		$wp_roles->add_cap('administrator', 'edit_sd_customers');
		$wp_roles->add_cap('administrator', 'delete_sd_customers');
		$wp_roles->add_cap('administrator', 'publish_sd_customers');

		$wp_roles->add_cap('sd_tech', 'read_sd_tickets');
		$wp_roles->add_cap('sd_tech', 'edit_sd_tickets');
		$wp_roles->add_cap('sd_tech', 'publish_sd_tickets');
		$wp_roles->add_cap('sd_tech', 'read_sd_customers');
		$wp_roles->add_cap('sd_tech', 'edit_sd_customers');
		$wp_roles->add_cap('sd_tech', 'publish_sd_customers');
	}
}
