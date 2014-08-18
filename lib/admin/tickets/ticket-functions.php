<?php
/**
 * Customer Functions
 *
 * @package     SD
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function sd_get_tickets( $args = array() ){
	$defaults = array(
		'post_type' => 'simple-desk-ticket',
		'posts_per_page' => 30,
		'paged' => null,
		'post_status' => array('new', 'inprogress', 'waitingonme', 'waitingoncustomer')
	);

	//check status to handle our custom post statuses - see also sd_get_tickets_count
	if(in_array($args['post_status'], array('mine', 'unassigned', 'notresolved', 'all'))){
		$args['post_status'] = $defaults['post_status'];
	}

	$args = wp_parse_args( $args, $defaults );

	if(empty($args['orderby'])) add_filter('posts_orderby', 'sd_modify_get_tickets_default');

	$tickets = new WP_Query( $args );
	
	if(empty($args['orderby'])) remove_filter('posts_orderby', 'sd_modify_get_tickets_default');
	
	if(!empty($tickets->posts)){ 
		return $tickets->posts; 
	}

	return false;
}

function sd_modify_get_tickets_default($orderby){
	$orderby = "FIELD(post_status, 'waitingonme', 'inprogress', 'new', 'waitingoncustomer', 'resolved'), post_date";
	return $orderby;
}

function sd_get_ticket_link($ticket_id){

}

function sd_get_tickets_count( $status = '', $cid = '' ){
	$user = wp_get_current_user();
	global $wpdb;

	$all_ticket_queries = array(
		'notresolved' => "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket' AND post_status != 'resolved';",
		'unassigned' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->posts.post_status != 'resolved' AND $wpdb->postmeta.meta_key = '_sd_ticket_assign' AND $wpdb->postmeta.meta_value = '0';",
		'mine' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->posts.post_status != 'resolved' AND $wpdb->postmeta.meta_key = '_sd_ticket_assign' AND $wpdb->postmeta.meta_value = '$user->ID';",
		'default' => "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket' AND post_status = '$status';",
		'all' => "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket';",
		'resolved' => "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket' AND post_status = 'resolved';"
	);

	$customer_ticket_queries = array(
		'notresolved' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->postmeta.meta_key = '_sd_ticket_customer' AND $wpdb->postmeta.meta_value = '" . absint($cid) . "' AND $wpdb->posts.post_status != 'resolved';",
		'all' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->postmeta.meta_key = '_sd_ticket_customer' AND $wpdb->postmeta.meta_value = '".absint($cid)."';",
		'lastweek' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->postmeta.meta_key = '_sd_ticket_customer' AND $wpdb->postmeta.meta_value = '".absint($cid)."' AND $wpdb->posts.post_date BETWEEN CURDATE() - INTERVAL 7 DAY AND CURDATE();",
		'resolved' => "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->postmeta.meta_key = '_sd_ticket_customer' AND $wpdb->postmeta.meta_value = '".absint($cid)."' AND $wpdb->posts.post_status = 'resolved'",
	);

	if(empty($status)){
		$query = $all_ticket_queries['all'];
	}elseif(!empty($status) && empty($cid)){
		$query = $all_ticket_queries[$status];
	}elseif(!empty($status) && !empty($cid)){
		$query = $customer_ticket_queries[$status];
	}

	return $wpdb->get_var($query);
}

function sd_add_new_ticket( $ticket ){
	if(is_array($ticket)){
		if(!empty($ticket['status']) && array_key_exists($ticket['status'], sd_get_ticket_statuses())){
			$ticket_status = $ticket['status'];
		}else{
			$ticket_status = 'new';
		}

		//add the default contact information if commercial client and none are present
		if((sd_get_customer_type($ticket['customer']) == 'commercial' )){
			if(empty($ticket['cname'])) $ticket['cname'] = sd_get_customer_display_name($ticket['customer']);
			if(empty($ticket['cphone'])) $ticket['cphone'] = sd_get_customer_phone($ticket['customer']);
			if(empty($ticket['cemail'])) $ticket['cemail'] = sd_get_customer_email($ticket['customer']);
		}

		$ticket_id = wp_insert_post(array(
			'post_type' => 'simple-desk-ticket',
			'post_title' => $ticket['issue'],
			'post_status' => $ticket_status,
			'post_content' => $ticket['details']
		));

		if(is_int($ticket_id)){
			foreach($ticket as $key => $value){
				update_post_meta( $ticket_id, '_sd_ticket_' . $key, $value );
			}			
		}

		//log ticket status change
		if($ticket_status != 'new'){
			sd_log_status_change($ticket_id, $ticket_status);
		}

		//notify technician(s)
		sd_new_ticket_notification_techs($ticket_id);

		//notify customer
		sd_new_ticket_notification_customer($ticket_id);

		return $ticket_id;
	}

	return false;
}

function sd_edit_existing_ticket( $ticket, $response = ''){
	/*
		General ticket information is stored in $ticket while reponse information will be in
		$response.
	*/
	if(is_array($ticket)){
		//grab the current status to check and see if it has changed later
		$current_ticket_status = sd_get_ticket_status($ticket['id']);
		$current_assigned_tech = sd_get_ticket_tech($ticket['id']);

		//if the current status has changed, log it. 
		if($current_ticket_status != $ticket['status']){
			sd_log_status_change($ticket['id'], sanitize_text_field($ticket['status']));
		}

		//if the assigned tech has changed, log it. 
		if($current_assigned_tech != $ticket['assign']){
			sd_log_tech_change($ticket['id'], $ticket['assign']);
		}

		//update the ticket general information. 
		$ticket_id = wp_update_post(array(
			'ID' => absint($ticket['id']),
			'post_status' => sanitize_text_field($ticket['status'])
		));		

		//if ticket update was successful, update all meta data
		if(is_int($ticket_id)){
			foreach($ticket as $key => $value){
				update_post_meta( $ticket_id, '_sd_ticket_' . $key, $value );
			}		
		}

		//add ticket response to log if present
		if(!empty($response['message'])){
			$reply_id = sd_save_ticket_reply($ticket_id, $response['message'], $response['private']);
		}

		return true;
	}

	return false;
}

function sd_save_ticket_reply( $ticket_id, $reply, $private = false){
	if(!empty($reply)){
		//set user display name.
		if(is_user_logged_in()){
			$current_user = wp_get_current_user();
			$reply_author = $current_user->display_name;
		}

		$reply_id = wp_insert_comment(array(
			'comment_post_ID' => absint($ticket_id),
			'comment_content' => $reply,
			'user_id' => get_current_user_id(),
			'comment_author_IP' => sd_get_ip(),
			'comment_author' => $reply_author //needs to be the display name of the user or email
		));
		
		if(is_int($reply_id)){
			//if private save the meta tag and DON'T email
			if('on' == $private){
				update_comment_meta($reply_id, '_sd_reply_private', 'true');
			}else{
				//send ticket update notification
				sd_updated_ticket_notification_customer($ticket_id, $reply_id);
			}
		}

		return $reply_id;
	}

	return false;
}

function sd_get_ticket_log( $ticket_id ){

	$args = array(
		'orderby' => 'comment_date',
		'order' => 'desc',
		'post_id' => absint($ticket_id),
		'status' => 'approve'
	);

	$replies = get_comments($args);

	foreach($replies as $reply){
		$reply->private = get_comment_meta($reply->comment_ID, '_sd_reply_private', true);
	}

	if(count($replies) > 0){
		return $replies;
	}

	return false;
}

function sd_get_ticket( $ticket_id ){
	$ticket = get_post( $ticket_id );

	if ( get_post_type( $ticket_id ) != 'simple-desk-ticket' ) {
		return false;
	}

	return $ticket;
}

function sd_get_ticket_reply( $reply_id ){
	$reply = get_comment($reply_id);
	return $reply->comment_content;
}

function sd_get_ticket_status($ticket_id){
	return get_post_status($ticket_id);
}

function sd_get_ticket_customer($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_customer', true);
}

function sd_get_ticket_contact_name($ticket_id){
	$contact_name = get_post_meta($ticket_id, '_sd_ticket_cname', true);
	if(!empty($contact_name)){
		return $contact_name;
	}
	return sd_get_customer_display_name(sd_get_ticket_customer($ticket_id));
}

function sd_get_ticket_contact_phone($ticket_id){
	$contact_phone = get_post_meta($ticket_id, '_sd_ticket_cphone', true);
	if(!empty($contact_phone)){
		return $contact_phone;
	}
	return sd_get_customer_phone(sd_get_ticket_customer($ticket_id));
}

function sd_get_ticket_contact_email($ticket_id){
	$contact_email = get_post_meta($ticket_id, '_sd_ticket_cemail', true);
	if(!empty($contact_email)){
		return $contact_email;
	}
	return sd_get_customer_email(sd_get_ticket_customer($ticket_id));	
}

function sd_get_ticket_creator($ticket_id){
	$ticket = get_post(absint($ticket_id));
	return $ticket->post_author;
}

function sd_get_ticket_reply_author($reply_id){
	$comment = get_comment(absint($reply_id));
	return $comment->user_id;
}

function sd_get_ticket_issue($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_issue', true);
}

function sd_get_ticket_details($ticket_id){
	$ticket = sd_get_ticket($ticket_id);
	return $ticket->post_content;
}

function sd_log_status_change($ticket_id, $status){
	$statuses = sd_get_ticket_statuses();
	$message = __('The ticket status has changed. The new status is: ' . $statuses[$status]);
	return sd_save_ticket_reply($ticket_id, $message, true);
}

function sd_log_tech_change($ticket_id, $tech_id){
	$tech_data = get_userdata($tech_id);
	$message = __('The assigned technician has changed. The new technician is: ' . $tech_data->display_name);
	return sd_save_ticket_reply($ticket_id, $message, true);
}

function sd_get_technicians($list = false, $email = false){
	$roles = array('sd_tech', 'administrator');
	$techs = array();

	foreach($roles as $role){
		$results = get_users(array('role' => $role));
		if($results) $techs = array_merge($techs, $results);
	}

	if($list){
		$simple_list = array();
		foreach($techs as $tech){
			$simple_list[$tech->data->ID] = $tech->data->display_name;
		}
		asort($simple_list);
		return $simple_list;
	}

	if($email){
		$email_list = array();
		foreach($techs as $tech){
			$email_list[$tech->data->ID] = $tech->data->user_email;
		}
		asort($email_list);
		return $email_list;
	}

	return $techs;
}

function sd_get_tech_display_name($tech_id){
	$tech = get_userdata($tech_id);
	return $tech->display_name;
}

function sd_get_ticket_tech($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_assign', true);
}
