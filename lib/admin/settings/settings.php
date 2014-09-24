<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function sd_display_settings_page(){

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo add_query_arg('tab', 'general', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'sd'); ?></a>
			</h2>
			<div id="tab_container">
				<form method="post" action="options.php">
					<?php if ( $active_tab == 'general' ): ?>
						<?php 
							$args = array(0, 'error');
							$logs = WP_Logging::get_logs($args);
						?>
						<ul>
						<?php foreach($logs as $log): ?>
							<li><?php print_r($log); ?></li>
						<?php endforeach; ?>
						</ul>
						
					<?php endif; ?>


					<?php submit_button(); ?>
				</form>
			</div>
		</div>
<?php
	echo ob_get_clean();
}