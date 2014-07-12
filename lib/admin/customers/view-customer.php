<?php
/***********************

	View Customer Page

***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();

$customer_id = absint($_GET['cid']);
$customer = sd_get_customer($customer_id);
$customer_company = sd_get_customer_company($customer_id);
?>
<h2><?php _e( 'Viewing Customer: ' . esc_attr(sd_get_customer_display_name($customer_id)) . (!empty($customer_company) ? ' with ' . esc_attr($customer_company): ''), 'sd' ); ?></h2>

<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

		<div id="postbox-container-1" class="postbox-container">
		    <?php do_meta_boxes('sd_view-customer-page', 'side', null); ?>
		</div>    
		<div id="postbox-container-2" class="postbox-container">
	  		<?php do_meta_boxes('sd_view-customer-page','normal',null);  ?>
	        <?php do_meta_boxes('sd_view-customer-page','advanced',null); ?>
	  	</div>	
	</div>
</div>