<?php
/***********************

	Add Customer Page

***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();

//to auto assign a new from the customer page
$customer_id = false;
if(isset($_GET['cid'])) $customer_id = absint($_GET['cid']);

?>

<h2><?php _e( 'Add New Ticket', 'sd' ); ?></h2>

<form id="sd-ticket" action="" method="post">
	<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
	<?php wp_nonce_field( 'sd_add_customer', 'sd_add_customer_nonce' ); ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

		<div id="post-body-content">
			<h3 class="section_header">
				<?php _e('Customer Information', 'sd'); ?>
				<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="response-loading" style="display: none;" />
			</h3>

			<div class="ticket_contact">
				<p>
					<span>
						<?php $customers = sd_get_customers(array('posts_per_page' => '-1'), true); ?>
						<?php if(!empty($customer_id)) $selected = $customer_id; ?>
						<select name="ticket[customer]" id="ticket-customer" required>
							<option><?php _e('--- Select Customer ---', 'sd'); ?></option>
							<?php echo sd_menuoptions($customers, $selected, true); ?>
						</select>
					</span>
				</p>
			</div>

			<h3 class="section_header ticket_perferred_contact"><?php _e('Contact Information', 'sd'); ?></h3>
			<div class="ticket_contact">
				<p class="ticket_perferred_contact">
					<span>
						<input class="medium" type="text" id="ticket[cname]" name="ticket[cname]" placeholder="<?php _e('Contact Name'); ?>" value="" />
						<input class="medium" type="text" id="ticket[cemail]" name="ticket[cemail]" placeholder="<?php _e('Contact Email'); ?>" value="" />
						<input class="medium" type="text" id="ticket[cphone]" name="ticket[cphone]" placeholder="<?php _e('Contact Phone'); ?>" value="" />
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Ticket Subject', 'sd'); ?></h3>
			<div class="ticket_details">
				<p>
					<span>
						<input type="text" name="ticket[issue]" id="ticket-issue" class="xlarge" placeholder="Issue" required/>
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Ticket Details', 'sd'); ?></h3>

			<div class="ticket_details">
				<p>
					<span>
						<textarea name="ticket[details]" id="ticket-details" class="xlarge" placeholder="A detailed description of the issue." required></textarea>
					</span>
				</p>
			</div>
		</div>


		<div id="postbox-container-1" class="postbox-container">
		    <?php do_meta_boxes('toplevel_page_simple-desk', 'side', null); ?>
		</div>

		<div id="postbox-container-2" class="postbox-container">
		    <?php do_meta_boxes('toplevel_page_simple-desk','normal',null);  ?>
		    <?php do_meta_boxes('toplevel_page_simple-desk','advanced',null); ?>
		</div>

		</div> <!-- #post-body -->
	</div>
</form>
