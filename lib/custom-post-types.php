<?php
/**
 * Register Post Types
 *
 * @package     SD
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_register_custom_post_types() {
	$ticket_labels = array(
		'name' => _x( 'Tickets', 'Ticket', 'simple-desk' ),
		'singular_name' => _x( 'Ticket', 'Ticket', 'simple-desk' ),
		'add_new' => _x( 'Add New', 'Ticket', 'simple-desk' ),
		'add_new_item' => __( 'Add New Ticket', 'simple-desk' ),
		'edit_item' => __( 'Edit Ticket', 'simple-desk' ),
		'new_item' => __( 'New Ticket', 'simple-desk' ),
		'all_items' => __( 'All Tickets', 'simple-desk' ),
		'view_item' => __( 'View Ticket', 'simple-desk' ),
		'search_items' => __( 'Search Tickets', 'simple-desk' ),
		'not_found' =>  __( 'No tickets found', 'simple-desk' ),
		'not_found_in_trash' => __( 'No tickets found in Trash', 'simple-desk' ),
		'parent_item_colon' => '',
		'menu_name' => __( 'Tickets', 'simple-desk' )
	);

	$ticket_args = array(
		'labels' => $ticket_labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => false,
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => array('sd_ticket', 'sd_tickets'),
		'map_meta_cap' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 42,
		'supports' => array( '' )
	);
	register_post_type( 'simple-desk-ticket', $ticket_args );

	$customer_labels = array(
		'name' => _x( 'Customers', 'Customers', 'simple-desk' ),
		'singular_name' => _x( 'Customer', 'Customers', 'simple-desk' ),
		'add_new' => _x( 'Add New', 'Customers', 'simple-desk' ),
		'add_new_item' => __( 'Add New Customer', 'Customers', 'simple-desk' ),
		'edit_item' => __( 'Edit Customer', 'simple-desk' ),
		'new_item' => __( 'New Customer', 'simple-desk' ),
		'all_items' => __( 'All Customers', 'simple-desk' ),
		'view_item' => __( 'View Customer', 'simple-desk' ),
		'search_items' => __( 'Search Customers', 'simple-desk' ),
		'not_found' =>  __( 'No customers found', 'simple-desk' ),
		'not_found_in_trash' => __( 'No customers found in Trash', 'simple-desk' ),
		'parent_item_colon' => '',
		'menu_name' => __( 'Customers', 'simple-desk' )
	);

	$customer_args = array(
		'labels' => $customer_labels,
		'public' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_menu' => false,
		'query_var' => false,
		'rewrite' => false,
		'capability_type' => array('sd_customer', 'sd_customers'),
		'map_meta_cap' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'menu_position' => 42,
		'supports' => array( '' )
	);

	register_post_type( 'simple-desk-customer', $customer_args );
}

add_action( 'init', 'sd_register_custom_post_types' );

