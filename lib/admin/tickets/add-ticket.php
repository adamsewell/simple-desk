<?php
/***********************

	Add Customer Page

***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();

//to auto assign a new from the customer page
$customer_id = absint($_GET['cid']);
?>
<h2><?php _e( 'Add New Ticket', 'sd' ); ?></h2>

<form id="sd-ticket" action="" method="POST">
	<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>"> 

		<div id="post-body-content">
			<h3 class="section_header"><?php _e('Contact Information', 'sd'); ?></h3>

			<div class="ticket_contact">
				<p>
					<span>
						<?php $customers = sd_get_customers(array('posts_per_page' => '-1'), true); ?>
						<?php if(!empty($customer_id)) $selected = $customer_id; ?>
						<select name="ticket[customer]" id="ticket-customer">
							<option><?php _e('Select Customer'); ?></option>
							<?php echo sd_menuoptions($customers, $selected, true); ?>
						</select>
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Ticket Subject', 'sd'); ?></h3>
			<div class="ticket_details">
				<p>
					<span>
						<input type="text" name="ticket[issue]" id="ticket-issue" class="xlarge" placeholder="Issue"/>
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Ticket Details', 'sd'); ?></h3>

			<div class="ticket_details">
				<p>
					<span>
						<textarea name="ticket[details]" id="ticket-details" class="xlarge" placeholder="A detailed description of the issue."></textarea>
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="ticket[tags]" id="ticket-tags" class="xlarge" placeholder="Tags (Comma Separated)"/>
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









