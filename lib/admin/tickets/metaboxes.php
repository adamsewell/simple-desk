<?php
/**
 * Ticket Metaboxes 
 *
 * @package     SD
 * @subpackage  Admin/Tickets
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_add_ticket_meta_boxes(){
	//add ticket page
	add_meta_box('sd_ticket_save_meta', __('Ticket Actions', 'sd'), 'sd_render_add_ticket_actions', 'toplevel_page_simple-desk', 'side', 'core');

	//view/edit ticket page
	add_meta_box('sd_ticket_edit_meta', __('Ticket Actions', 'sd'), 'sd_render_edit_ticket_actions', 'sd_edit-ticket-page', 'side', 'core');
	add_meta_box('sd_ticket_edit_customer_info', __('Contact Information', 'sd'), 'sd_render_customer_information', 'sd_edit-ticket-page', 'side', 'core');
	add_meta_box('sd_ticket_customer_history', __('Customer History', 'sd'), 'sd_render_customer_history', 'sd_edit-ticket-page', 'side', 'core');

}
add_action('load-toplevel_page_simple-desk', 'sd_add_ticket_meta_boxes');


function sd_render_add_ticket_actions(){
?>
	<div id="ticket_actions">
		<p>
			<span>
				<label for="ticket-status">
					<?php _e('Status:'); ?>
					<select name="ticket[status]" id="ticket-status">
						<?php $statuses = sd_get_ticket_statuses(); ?>
						<?php echo sd_menuoptions($statuses, $selected, true); ?>
					</select>
				</label>
			</span>
		</p>
		<p>
			<span>
				<label for="ticket-assign">
					<?php _e('Assign Ticket To:'); ?>
					<select name="ticket[assign]" id="ticket-assign">
						<?php $techs = sd_get_technicians(true); ?>
						<option value="0"><?php _e('Not Assigned'); ?></option>
						<?php echo sd_menuoptions($techs, $selected, true); ?>
					</select>
				</label>
			</span>
		</p>		
		<p>
			<span>
				<?php wp_nonce_field('sd-add-ticket', 'sd-add-ticket-nonce'); ?>
				<input type="hidden" name="sd_action" value="add_ticket" id="sd_action" />
				<input type="hidden" name="sd_url_redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=simple-desk' ) ); ?>"/>
				<input type="submit" class="button-primary" id="ticket[save]" name="ticket-save" value="Save Ticket" />
			</span>
		</p>
	</div>
	<?php
}

function sd_render_edit_ticket_actions(){
	$ticket_id = absint($_GET['tid']);
	$ticket = sd_get_ticket($ticket_id);
?>
	<div id="ticket_actions">
		<p>
			<span>
				<label for="ticket-status">
					<?php _e('Status:'); ?>
					<select name="ticket[status]" id="ticket-status">
						<?php $statuses = sd_get_ticket_statuses(); ?>
						<?php $selected = $ticket->post_status; ?>
						<?php echo sd_menuoptions($statuses, $selected, true); ?>
					</select>
				</label>
			</span>
		</p>
		<p>
			<span>
				<label for="ticket-assign">
					<?php _e('Assign Ticket To:'); ?>
					<select name="ticket[assign]" id="ticket-assign">
						<?php $techs = sd_get_technicians(true); ?>
						<?php $selected = sd_get_ticket_tech($ticket_id); ?>
						<option value="0"><?php _e('Not Assigned'); ?></option>
						<?php echo sd_menuoptions($techs, $selected, true); ?>
					</select>
				</label>
			</span>
		</p>	
		<p>
			<span>
				<label for="response-private">
					<?php _e('Private Reply?:'); ?>
					<input type="checkbox" name="response[private]" id="response-private" />
				</label>
			</span>
		</p>
		<?php if(current_user_can('delete_sd_tickets')): ?>
			<p>
				<span>
					<a class="button-delete" href="<?php echo add_query_arg( array( 'sd_action' => 'delete_ticket', 'cid' => $ticket_id ), admin_url() . 'admin.php?page=simple-desk-customer-page' ); ?>" title="Delete Ticket">Delete Ticket</a>
				</span>
			</p>
		<?php endif; ?>
		<p>
			<span>
				<?php wp_nonce_field('sd-edit-ticket', 'sd-edit-ticket-nonce'); ?>
				<input type="hidden" name="ticket[id]" id="ticket-id" value="<?php echo absint($ticket->ID); ?>" />
				<input type="hidden" name="sd_action" value="edit_ticket" />
				<input type="hidden" name="sd_url_redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=simple-desk' ) ); ?>"/>
				<input type="submit" class="button-primary" id="ticket[save]" name="ticket-save" value="Update Ticket" />
			</span>
		</p>
	</div>
<?php
}

function sd_render_customer_history(){
	$ticket_id = absint($_GET['tid']);
	$customer_id = sd_get_ticket_customer($ticket_id);
	$customer_history = sd_get_tickets(array(
		'post_status' => 'resolved', 
		'meta_key' => '_sd_ticket_customer', 
		'meta_value' => $customer_id, 
		'orderby' => 'post_modified', 
		'order' => 'DESC',
		'posts_per_page' => 5
	));
?>
	<?php if(!empty($customer_history)): ?>
		<ol>
			<?php foreach($customer_history as $history): ?>
				<li>
					<?php $url = add_query_arg( array('sd_page' => 'edit_ticket', 'tid' => $history->ID),  admin_url('admin.php?page=simple-desk')); ?>
					<a href="<?php echo $url; ?>"><?php echo $history->post_title; ?></a> - 
					<?php echo mysql2date(get_option('date_format'), $history->post_modified); ?>
				</li>

			<?php endforeach; ?>
		</ol>
	<?php else: ?>
		<?php _e('No previous tickets found', 'sd'); ?>
	<?php endif; ?>
<?php
}

function sd_render_customer_information(){
	$ticket_id = absint($_GET['tid']);
	$customer_id = sd_get_ticket_customer($ticket_id);
	$ticket_url = admin_url( 'admin.php?page=simple-desk' );
?>
	<p>
		<span>
			<strong>Name:</strong> <?php echo sanitize_text_field(sd_get_ticket_contact_name($ticket_id)); ?>
		</span>
	</p>
	<p>
		<span>
			<strong>Contact Number:</strong> <a href="tel:<?php echo esc_attr(sd_get_ticket_contact_phone($ticket_id));?>"><?php echo sanitize_text_field(sd_get_ticket_contact_phone($ticket_id)); ?></a>
		</span>
	</p>
	<p>
		<span>
			<strong>Email Address:</strong> <a href="mailto:<?php echo esc_attr(sd_get_ticket_contact_email($ticket_id)); ?>"><?php echo sanitize_email(sd_get_ticket_contact_email($ticket_id)); ?></a>
		</span>
	</p>
	<p>
		<span>
			<?php $address = sd_get_customer_address($customer_id) . ' ' . sd_get_customer_xaddress($customer_id) . ' ' . sd_get_customer_city($customer_id) . ', ' . sd_get_customer_state($customer_id) . ' ' . sd_get_customer_zip($customer_id); ?>
			<strong>Address:</strong> <a target="_blank" href="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php echo urlencode($address); ?>"><?php echo esc_attr($address); ?></a>
		</span>
	</p>
	<p>
		<span>
			<strong>Open Tickets:</strong> <a href="<?php echo add_query_arg(array('cid' => $customer_id, 'status' => 'open'), $ticket_url) ?>"><?php echo absint(sd_get_customer_ticket_count($customer_id)); ?></a>
		</span>
	</p>

<?php
}
