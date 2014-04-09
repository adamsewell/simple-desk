<?php
/**
 * Customer Metaboxes 
 *
 * @package     SD
 * @subpackage  Admin/Downloads
 * @copyright   Copyright (c) 2014, Adam Sewell
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_add_customer_meta_boxes(){
	//add customer page
	add_meta_box('sd_customer_add_save_meta', __('Customer Actions', 'sd'), 'sd_render_add_customer_actions', 'simple-desk_page_simple-desk-add-customer-page', 'side', 'core');
	
	//edit customer page
	add_meta_box('sd_customer_edit_save_meta', __('Customer Actions', 'sd'), 'sd_render_edit_customer_actions', 'sd_edit-customer-page', 'side', 'core');
	add_meta_box('sd_customer_edit_meta', __('Customer Meta', 'sd'), 'sd_render_edit_customer_meta', 'sd_edit-customer-page', 'side', 'core');
	//add_meta_box('sd_customer_edit_history', __('Customer History', 'sd'), 'sd_render_edit_customer_history', 'simple-desk_page_simple-desk-edit-customer-page', 'normal', 'core');

}

add_action('load-simple-desk_page_simple-desk-customer-page', 'sd_add_customer_meta_boxes');


function sd_render_add_customer_actions(){
?>
	<p>
		<span>
			<?php wp_nonce_field('sd-add-customer', 'sd-add-customer-nonce'); ?>
			<input type="hidden" name="sd_action" value="add_customer"/>
			<input type="hidden" name="sd_url_redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=simple-desk-customer-page' ) ); ?>"/>
			<input type="submit" class="button-primary" id="customer[save]" name="customer-save" value="Save Customer" />
		</span>
	</p>
<?php
}

function sd_render_edit_customer_actions(){
	$customer_id = absint($_GET['cid']);
	$customer = sd_get_customer($customer_id);
?>
	<div id="customer_actions">
		<p>
			<span>
				<a class="button-secondary" href="<?php echo add_query_arg( array( 'sd_page' => 'add_ticket', 'cid' => $customer_id ), admin_url() . 'admin.php?page=simple-desk' ); ?>" title="New Ticket">New Ticket</a>
			</span>
		</p>
		<?php if(current_user_can('delete_sd_customers')): ?>
			<p>
				<span>
					<a class="button-delete" href="<?php echo add_query_arg( array( 'sd_action' => 'delete_customer', 'cid' => $customer_id ), admin_url() . 'admin.php?page=simple-desk-customer-page' ); ?>" title="Delete Customer">Delete Customer</a>
				</span>
			</p>
		<?php endif; ?>
		<p>
			<span>
				<?php wp_nonce_field('sd-edit-customer', 'sd-edit-customer-nonce'); ?>
				<input type="hidden" name="customer[id]" id="customer[id]" value="<?php echo absint($customer->ID); ?>" />
				<input type="hidden" name="sd_action" value="edit_customer" />
				<input type="hidden" name="sd_url_redirect" value="<?php echo esc_url( admin_url( 'admin.php?page=simple-desk-customer-page' ) ); ?>"/>
				<input type="submit" class="button-primary" id="customer[save]" name="customer-save" value="Update Customer" />
			</span>
		</p>
	</div>
<?php
}

function sd_render_edit_customer_meta(){
	$customer_id = absint($_GET['cid']);
	$customer = sd_get_customer($customer_id);
?>
	<p>
		<span>
			<strong>Customer ID:</strong> <?php echo absint($customer->ID); ?>
		</span>
	</p>
	<p>
		<span>
			<strong>Last Modified:</strong> <?php echo mysql2date('n/j/y g:ia', $customer->post_modified); ?>
		</span>
	</p>
	<p>
		<span>
			<strong>Created:</strong> <?php echo mysql2date('n/j/y g:ia', $customer->post_date); ?>
		</span>
	</p>
	<p>
		<span>
			<strong>Open Tickets:</strong> <a href="#"><?php echo absint(sd_get_customer_ticket_count($customer_id)); ?></a>
		</span>
	</p>
	<p>
		<span>
			<strong>Total Tickets:</strong> <a href="#">1</a>
		</span>
	</p>
<?php
}

function sd_render_edit_customer_history(){
	$customer_id = absint($_GET['cid']);
	$customer = sd_get_customer($customer_id);
?>
	This is a test
<?php
}













