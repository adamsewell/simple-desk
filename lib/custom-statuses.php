<?php
/**
 * Register Custom Statuses
 *
 * @package     SD
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_register_custom_status() {
	//ticket status
	register_post_status( 'new', array(
			'label' => _x( 'New', 'post' ),
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'New <span class="count">(%s)</span>', 'New <span class="count">(%s)</span>' ),
		) );

	register_post_status( 'inprogress', array(
			'label' => _x( 'In Progress', 'post' ),
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Waiting On Me <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>' ),
		) );

	register_post_status( 'waitingonme', array(
			'label' => _x( 'Waiting On Me', 'post' ),
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Waiting On Me <span class="count">(%s)</span>', 'Waiting On Me <span class="count">(%s)</span>' ),
		) );

	register_post_status( 'waitingoncustomer', array(
			'label' => _x( 'Waiting On Customer', 'post' ),
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Waiting On User <span class="count">(%s)</span>', 'Waiting On Customer <span class="count">(%s)</span>' ),
		) );

	register_post_status( 'resolved', array(
			'label' => _x( 'Resolved', 'post' ),
			'public' => false,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Resolved <span class="count">(%s)</span>', 'Resolved <span class="count">(%s)</span>' ),
		) );

	//customer status

	register_post_status( 'active', array(
		'label' => _x( 'Active', 'post' ),
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop( 'Waiting On Me <span class="count">(%s)</span>', 'Waiting On Me <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'archived', array(
			'label' => _x( 'Archived', 'post' ),
			'public' => true,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop( 'Waiting On Me <span class="count">(%s)</span>', 'Waiting On Me <span class="count">(%s)</span>' ),
		) );
}

add_action( 'init', 'sd_register_custom_status' );


