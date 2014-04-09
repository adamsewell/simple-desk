<?php
/***********************

	Add Customer Page

***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();
?>
<h2><?php _e( 'Add New Customer', 'sd' ); ?></h2>

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
						<input type="text" name="customer[fname]" id="customer-fname" class="large" placeholder="First Name"/>
					</span>
					<span>
						<input type="text" name="customer[lname]" id="customer-lname" class="large" placeholder="Last Name" />
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="customer[email]" id="customer-email" class="large" placeholder="Email" />
					</span>
					<span>
						<input type="text" name="customer[company]" id="customer-company" class="large" placeholder="Company" />
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="customer[phone]" id="customer-phone" class="large" placeholder="Phone Number" />
					</span>
					<span>
						<input type="text" name="customer[mobile]" id="customer-mobile" class="large" placeholder="Mobile Number" />
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Details', 'sd'); ?></h3>

			<div class="customer_details">
				<p>
					<span>
						<?php $countries = sd_get_countries(); ?>
						<select name="customer[country]" id="customer-country">
							<option><?php _e('Country'); ?></option>
							<?php echo sd_menuoptions($countries, $selected, true); ?>
						</select>
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="customer[address]" id="customer-address" class="xlarge" placeholder="Address Street 1" />
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="customer[xaddress]" id="customer-xaddress" class="xlarge" placeholder="Address Street 2" />
					</span>
				</p>
				<p>
					<span>
						<input type="text" name="customer[city]" id="customer-city" class="medium" placeholder="City" />
					</span>
					<span>
						<input type="text" name="customer[state]" id="customer-state" class="medium" placeholder="State" />
					</span>
					<span>
						<input type="text" name="customer[zip]" id="customer-zip" class="medium" placeholder="Zip Code" />
					</span>
				</p>
			</div>

			<h3 class="section_header"><?php _e('Notes', 'sd'); ?></h3>
			<div class="customer_other">
				<textarea name="customer[notes]" id="customer-notes" class="xlarge"></textarea>
			</div>
		  </div>    

		  <div id="postbox-container-1" class="postbox-container">
		        <?php do_meta_boxes('simple-desk_page_simple-desk-add-customer-page', 'side', null); ?>
		  </div>    

		  <div id="postbox-container-2" class="postbox-container">
		        <?php do_meta_boxes('simple-desk_page_simple-desk-add-customer-page','normal',null);  ?>
		        <?php do_meta_boxes('simple-desk_page_simple-desk-add-customer-page','advanced',null); ?>
		  </div>	     					

		</div> <!-- #post-body -->
	</div>
</form>









