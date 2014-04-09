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

	$args = wp_parse_args( $args, $defaults );

	$tickets = new WP_Query( $args );

	if(!empty($tickets->posts)){ 
		return $tickets->posts; 
	}

	return false;
}

function sd_modify_get_tickets_default($orderby){
	$orderby = "FIELD(post_status, 'waitingonme', 'inprogress', 'new', 'waitingoncustomer'), post_date";
	return $orderby;
}

function sd_get_tickets_count( $status = 'new' ){
	$user = wp_get_current_user();
	global $wpdb;

	if($status == 'all'){
		$query = "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket' AND post_status != 'resolved';";
	}elseif($status == 'mine'){
		$query = "SELECT count(post_status) FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id WHERE $wpdb->posts.post_type = 'simple-desk-ticket' AND $wpdb->posts.post_status != 'resolved' AND $wpdb->postmeta.meta_key = '_sd_ticket_assign' AND $wpdb->postmeta.meta_value = '$user->ID';";
	}else{
		$query = "SELECT count(post_status) FROM $wpdb->posts WHERE post_type = 'simple-desk-ticket' AND post_status = '$status';";
	}

	return $wpdb->get_var($query);
}

function sd_save_ticket( $ticket ){
	if(is_array($ticket)){
		$ticket = array_map('sanitize_text_field', $ticket);

		if(!empty($ticket['status']) && array_key_exists($ticket['status'], sd_get_ticket_statuses())){
			$ticket_status = $ticket['status'];
		}else{
			$ticket_status = 'new';
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

		//log ticket status change. 
		if($ticket_status != 'new'){
			sd_log_status_change($ticket_id, $ticket_status);
		}



		return $ticket_id;
	}

	return false;
}

function sd_update_ticket( $ticket, $response = ''){
	/*
		General ticket information is stored in $ticket while reponse information will be in
		$response.
	*/
	if(is_array($ticket)){
		//grab the current status to check and see if it has changed later
		$current_ticket_status = sd_get_ticket_status($ticket['id']);

		//if the current status has changed, log it. 
		if($current_ticket_status != $ticket['status']){
			sd_log_status_change($ticket['id'], sanitize_text_field($ticket['status']));
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

		//add ticket response to history if present
		if(!empty($response['message'])){
			sd_save_ticket_reply($ticket_id, $response['message'], $response['private']);
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
				sd_send_email($ticket_id, 'update');
			}
		}

		return $reply_id;
	}

	return false;
}

function sd_get_ticket_history( $ticket_id ){

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

function sd_render_ticket_history ( $ticket_id ){
	if(!empty($ticket_id)){
		$responses = sd_get_ticket_history($ticket_id);
		ob_start();
		foreach($responses as $response){
?>
			<div class="issue-response-wrap <?php echo (empty($response->private) ? '' : 'private-reply'); ?>">
				<div class="issue-response">
					<p class="issue-header">
						<span class="issue-response-author">
							<?php echo esc_attr($response->comment_author); ?> updated this ticket.
						</span>
						<span class="issue-meta">
							<?php echo (empty($response->private) ? '' : '[Private Reply]'); ?>
							<?php $time_date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>
							<?php echo mysql2date($time_date_format, $response->comment_date); ?>
						</span>
					</p>
					<p class="issue-message">
						<?php echo sanitize_text_field($response->comment_content); ?>
					</p>
				</div>
			</div>
<?php
		}

		$output = ob_get_clean();
		return $output;	
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

function sd_get_ticket_status($ticket_id){
	return get_post_status($ticket_id);
}
function sd_get_ticket_customer($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_customer', true);
}

function sd_get_ticket_issue($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_issue', true);
}

function sd_log_status_change($ticket_id, $status){
	$statuses = sd_get_ticket_statuses();
	$message = __('The ticket status has changed. The new status is: ' . $statuses[$status]);
	return sd_save_ticket_reply($ticket_id, $message, true);
}

function sd_log_tech_change($ticket_id, $tech){

}

function sd_get_technicians($list = false){
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
	

	return $techs;
}

function sd_get_ticket_tech($ticket_id){
	return get_post_meta($ticket_id, '_sd_ticket_assign', true);
}