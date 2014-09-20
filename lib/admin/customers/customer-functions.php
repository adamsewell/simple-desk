<?php
/**
 * Customer Functions
 *
 * @package     SD
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function sd_save_customer( $customer ){
	if(is_array($customer)){
		$customer = array_map('sanitize_text_field', $customer);

		//check if company field is populated, we're assuming it's a business client otherwise a residential
		if(!empty($customer['company'])){
			$customer_id = wp_insert_post(array(
				'post_type' => 'simple-desk-customer',
				'post_title' => $customer['company'],
				'post_status' => 'active'
			));
			//set our customer type - to be stored
			$customer['type'] = 'commercial';
		}else{
			$customer_id = wp_insert_post(array(
				'post_type' => 'simple-desk-customer',
				'post_title' => $customer['fname'] . ' ' . $customer['lname'],
				'post_status' => 'active'
			));
			//set our customer type - to be stored
			$customer['type'] = 'residential';	
		}	

		if(is_int($customer_id)){
			foreach($customer as $key => $value){
				update_post_meta( $customer_id, '_sd_customer_' . $key, $value );
			}			
		}

		return $customer_id;
	}

	return false;
}

function sd_update_customer( $customer ){
	if(is_array($customer)){

		//update the customer display name
		if(sd_get_customer_type($customer['id']) == 'commercial'){
			$display_name = $customer['company'];
		}else{
			$display_name = $customer['fname'] . ' ' . $customer['lname'];
		}

		$customer_id = wp_update_post(array(
			'ID' => absint($customer['id']),
			'post_title' => $display_name
		));

		if(is_int($customer_id)){
			foreach($customer as $key => $value){
				update_post_meta( $customer_id, '_sd_customer_' . $key, $value );
			}			
		}

		return true;
	}

	return false;
}

function sd_get_customers( $args = array(''), $list = false ){
	$defaults = array(
		'post_type' => 'simple-desk-customer',
		'posts_per_page' => 30,
		'paged' => null,
		'post_status' => 'active',
		'orderby' => 'title',
		'order' => 'ASC'
	);

	$args = wp_parse_args( $args, $defaults );

	$customers = get_posts( $args );

	//defaults to return all customers in array format
	if($customers && !$list){ 
		return $customers; 
	}elseif($customers && $list){
		$simple_list = array();
		foreach($customers as $customer){
			$simple_list[$customer->ID] = $customer->post_title;
		}

		return $simple_list;
	}

	return false;
}

function sd_get_customer($customer_id){
	$customer = get_post( $customer_id );

	if ( get_post_type( $customer_id ) != 'simple-desk-customer' ) {
		return false;
	}

	return $customer;
}

function sd_get_customer_id($email){
	global $wpdb;
	$cid = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sd_customer_email' AND meta_value = %s", $email));
	if($cid){
		return $cid;
	}

	return false;
}

function sd_get_customer_display_name($customer_id){
	return sd_get_customer_fname($customer_id) . ' ' . sd_get_customer_lname($customer_id);
}

function sd_get_customer_fname($customer_id){
	return get_post_meta($customer_id, '_sd_customer_fname', true);
}

function sd_get_customer_lname($customer_id){
	return get_post_meta($customer_id, '_sd_customer_lname', true);
}

function sd_get_customer_email($customer_id){
	return sanitize_email(get_post_meta($customer_id, '_sd_customer_email', true));
}

function sd_customer_has_email($customer_id){
	$email = sd_get_customer_email($customer_id);
	if(!empty($email)){
		return true;
	}

	return false;
}

function sd_get_customer_website($customer_id){
	return get_post_meta($customer_id, '_sd_customer_website', true);
}

function sd_get_customer_phone($customer_id){
	return get_post_meta($customer_id, '_sd_customer_phone', true);
}

function sd_get_customer_mobile($customer_id){
	return get_post_meta($customer_id, '_sd_customer_mobile', true);
}

function sd_get_customer_company($customer_id){
	return get_post_meta($customer_id, '_sd_customer_company', true);
}

function sd_get_customer_address($customer_id){
	return get_post_meta($customer_id, '_sd_customer_address', true);
}

function sd_get_customer_xaddress($customer_id){
	return get_post_meta($customer_id, '_sd_customer_xaddress', true);
}

function sd_get_customer_city($customer_id){
	return get_post_meta($customer_id, '_sd_customer_city', true);
}

function sd_get_customer_state($customer_id){
	return get_post_meta($customer_id, '_sd_customer_state', true);
}

function sd_get_customer_country($customer_id){
	return get_post_meta($customer_id, '_sd_customer_country', true);	
}

function sd_get_customer_zip($customer_id){
	return get_post_meta($customer_id, '_sd_customer_zip', true);
}

function sd_get_customer_notes($customer_id){
	return get_post_meta($customer_id, '_sd_customer_notes', true);
}

function sd_get_customer_type($customer_id){
	return get_post_meta($customer_id, '_sd_customer_type', true);
}

function sd_get_customer_created($customer_id){
	$customer = sd_get_customer($customer_id);
	return mysql2date('n/j/y g:ia', $customer->post_date);
}

function sd_get_customer_modified($customer_id){
	$customer = sd_get_customer($customer_id);
	return mysql2date('n/j/y g:ia', $customer->post_modified);
}

function sd_get_customers_count(){
	global $wpdb;

	$query = "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-customer' AND post_status = 'active';";

	return $wpdb->get_var($query);
}





