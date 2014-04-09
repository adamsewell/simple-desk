<?php
/**
 * Admin Pages
 *
 * @package     SD
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function simpledesk_notices() {
	if ( !defined( 'AUTH_SALT' ) ) {
		add_settings_error( 'sd-notices', 'salt-warning', 'Simple Desk: This plugin requires that you have a unique AUTH_SALT defined in your wp-config file', 'error' );
	}

	if ( isset( $_GET['sd-message'] ) && 'customer_added' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'customer-added', 'Simple Desk: Customer added', 'updated' );
	}

	if ( isset( $_GET['sd-message'] ) && 'customer_updated' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'customer-updated', 'Simple Desk: Customer updated', 'updated' );
	}

	if ( isset( $_GET['sd-message'] ) && 'customer_deleted' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'customer-deleted', 'Simple Desk: Customer deleted', 'updated' );
	}

	if ( isset( $_GET['sd-message'] ) && 'ticket_added' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'ticket-added', 'Simple Desk: Ticket added', 'updated' );
	}

	if ( isset( $_GET['sd-message'] ) && 'ticket_updated' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'ticket-updated', 'Simple Desk: Ticket updated', 'updated' );
	}

	if ( isset( $_GET['sd-message'] ) && 'ticket_deleted' == $_GET['sd-message'] ) {
		 add_settings_error( 'sd-notices', 'customer-deleted', 'Simple Desk: Ticket deleted', 'updated' );
	}


	settings_errors('sd-notices');
}

add_action( 'admin_notices', 'simpledesk_notices' );
