<?php
/**
 * Admin Pages
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function add_simple_desk_pages() {
	global $menu;
	$position = 52;
	while (isset($menu[$position])) $position++;
	
	add_menu_page('Simple Desk', 'Simple Desk', 'read_sd_tickets', 'simple-desk', 'sd_display_tickets', '', $position);
	add_submenu_page('simple-desk', 'Simple Desk', 'Tickets', 'read_sd_tickets', 'simple-desk', 'sd_display_tickets');
	add_submenu_page('simple-desk', 'Customer', 'Customers', 'read_sd_customers', 'simple-desk-customer-page', 'sd_display_customers' );
	add_submenu_page('simple-desk', 'Import/Export', 'Import/Export', 'edit_posts', 'simple-desk-import-export', 'sd_display_import_page');
	add_submenu_page('simple-desk', 'Settings', 'Settings', 'edit_posts', 'simple-desk-settings-page', array( 'SimpleDeskAdmin', 'display_settings' ));
}

add_action( 'admin_menu', 'add_simple_desk_pages', 10 );