<?php
/**
 * Customer Admin Actions 
 *
 * @package     SD
 * @subpackage  Admin/Downloads
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

function sd_add_customer( $data ){
	if(isset($data['sd-add-customer-nonce']) && wp_verify_nonce($data['sd-add-customer-nonce'], 'sd-add-customer')){
		if(sd_save_customer($data['customer'])){
			wp_redirect( add_query_arg( 'sd-message', 'customer_added', $data['sd_url_redirect'] ) );
		}
	}
}
add_action('sd_add_customer', 'sd_add_customer');

function sd_edit_customer($data){
	if(isset($data['sd-edit-customer-nonce']) && wp_verify_nonce($data['sd-edit-customer-nonce'], 'sd-edit-customer')){
		if(sd_update_customer($data['customer'])){
			wp_redirect( add_query_arg( 'sd-message', 'customer_updated', $data['sd_url_redirect'] ) );
		}
	}
}
add_action('sd_edit_customer', 'sd_edit_customer');


function sd_delete_customer($data){
	if(isset($data['_wpnonce']) && wp_verify_nonce($data['_wpnonce'], 'sd-delete-customer')){
		if(!wp_delete_post( $data['cid'], true )){
			wp_redirect( add_query_arg( 'sd-message', 'customer_deleted', admin_url('admin.php?page=simple-desk-customer-page') ) );
		}		
	}
}
add_action('sd_delete_customer', 'sd_delete_customer');

