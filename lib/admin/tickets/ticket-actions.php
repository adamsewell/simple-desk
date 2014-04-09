<?php
/**
 * Ticket Admin Actions 
 *
 * @package     SD
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

function sd_add_ticket( $data ){
	if(isset($data['sd-add-ticket-nonce']) && wp_verify_nonce($data['sd-add-ticket-nonce'], 'sd-add-ticket')){
		if(sd_save_ticket($data['ticket'])){
			wp_redirect( add_query_arg( 'sd-message', 'ticket_added', $data['sd_url_redirect'] ) );
		}
	}
}
add_action('sd_add_ticket', 'sd_add_ticket');

function sd_edit_ticket($data){
	if(isset($data['sd-edit-ticket-nonce']) && wp_verify_nonce($data['sd-edit-ticket-nonce'], 'sd-edit-ticket')){
		if(sd_update_ticket($data['ticket'], $data['response'])){
			wp_redirect( add_query_arg( 'sd-message', 'ticket_updated', $data['sd_url_redirect'] ) );
		}
	}
}
add_action('sd_edit_ticket', 'sd_edit_ticket');


function sd_delete_ticket($data){
	if(isset($data['_wpnonce']) && wp_verify_nonce($data['_wpnonce'], 'sd-delete-ticket')){
		if(wp_delete_post( $data['tid'], true )){
			wp_redirect( add_query_arg( 'sd-message', 'ticket_deleted', admin_url('admin.php?page=simple-desk')));
		}
	}
}
add_action('sd_delete_ticket', 'sd_delete_ticket');

