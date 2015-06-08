<?php
/**
 * Reports Page
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_display_reports(){
  $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo add_query_arg('tab', 'reports', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>"><?php _e('Reports', 'sd'); ?></a>
			</h2>
			<div id="tab_container">

			</div>
		</div>
<?php
	echo ob_get_clean();
}
