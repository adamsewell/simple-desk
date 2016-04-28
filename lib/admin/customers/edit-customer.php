<?php
/***********************

	Edit Customer Page

***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();

$customer_id = absint($_GET['cid']);
$customer = sd_get_customer($customer_id);

?>
<h2><?php _e( 'Editing Customer: ' . esc_attr($customer->post_title), 'sd' ); ?></h2>

<form id="sd-customer" action="" method="POST">
	<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>"> 

		  <div id="post-body-content">
		  	<h3 class="section_header"><?php _e('Contact Information', 'sd'); ?></h3>
				<div class="customer_contact">
					<p>
						<span>
							<input type="text" name="customer[fname]" id="customer-fname" class="large" placeholder="First Name" value="<?php echo esc_attr(sd_get_customer_fname($customer_id)); ?>" />
						</span>
						<span>
							<input type="text" name="customer[lname]" id="customer-lname" class="large" placeholder="Last Name" value="<?php echo esc_attr(sd_get_customer_lname($customer_id)); ?>" />
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[email]" id="customer-email" class="large" placeholder="Email" value="<?php echo esc_attr(sd_get_customer_email($customer_id)); ?>" />
						</span>
						<span>
							<input type="text" name="customer[company]" id="customer-company" class="large" placeholder="Company" value="<?php echo esc_attr(sd_get_customer_company($customer_id)); ?>" />
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[phone]" id="customer-phone" class="large" placeholder="Phone Number" value="<?php echo esc_attr(sd_get_customer_phone($customer_id)); ?>" />
						</span>
						<span>
							<input type="text" name="customer[mobile]" id="customer-mobile" class="large" placeholder="Mobile Number" value="<?php echo esc_attr(sd_get_customer_mobile($customer_id)); ?>" />
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[website]" id="customer-website" class="large" placeholder="Website" value="<?php echo esc_attr(sd_get_customer_website($customer_id)); ?>" />
						</span>
					</p>
				</div>

				<h3 class="section_header"><?php _e('Details', 'sd'); ?></h3>

				<div class="customer_details">
					<p>
						<span>
							<?php
								$countries = sd_get_countries();
								$selected = sd_get_customer_country($customer_id);
							?>
							<select name="customer[country]" id="customer-country">
								<option><?php _e('Country'); ?></option>
								<?php echo sd_menuoptions($countries, $selected, true); ?>
							</select>
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[address]" id="customer-address" class="xlarge" placeholder="Address Street 1" value="<?php echo esc_attr(sd_get_customer_address($customer_id)); ?>"/>
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[xaddress]" id="customer-xaddress" class="xlarge" placeholder="Address Street 2" value="<?php echo esc_attr(sd_get_customer_xaddress($customer_id)); ?>" />
						</span>
					</p>
					<p>
						<span>
							<input type="text" name="customer[city]" id="customer-city" class="medium" placeholder="City" value="<?php echo esc_attr(sd_get_customer_city($customer_id)); ?>" />
						</span>
						<span>
							<?php
								$states = sd_get_states();
								$selected = sd_get_customer_state($customer_id);
							?>
							<select name="customer[state]" id="customer-state">
								<option><?php _e('State'); ?></option>
								<?php echo sd_menuoptions($states, $selected, true); ?>
							</select>
						</span>
						<span>
							<input type="text" name="customer[zip]" id="customer-zip" class="medium" placeholder="Zip Code" value="<?php echo esc_attr(sd_get_customer_zip($customer_id)); ?>" />
						</span>
					</p>
				</div>
		  </div>

		  <div id="postbox-container-1" class="postbox-container">
		        <?php do_meta_boxes('sd_edit-customer-page', 'side', null); ?>
		  </div>

		  <div id="postbox-container-2" class="postbox-container">
		  		<?php do_meta_boxes('sd_edit-customer-page','normal', null);  ?>
		        <?php do_meta_boxes('sd_edit-customer-page','advanced', null); ?>
		  </div>

		</div> <!-- #post-body -->
	</div>
</form>
