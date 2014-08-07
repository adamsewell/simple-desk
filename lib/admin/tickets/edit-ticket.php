<?php
/***********************
	Edit Ticket Page
***********************/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
screen_icon();

$ticket_id = absint($_GET['tid']);
$ticket = sd_get_ticket($ticket_id);
?>

<h2><?php echo 'Viewing Ticket #'. $ticket_id .': ' . esc_attr(sd_get_ticket_issue($ticket_id)); ?>
</h2>

<form id="sd-ticket" action="" method="post">
	<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
	<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>"> 
			<div id="post-body-content">
				<?php if($ticket->post_status != 'resolved'): ?>
					<h3 class="section_header">
						<?php _e('Your Response', 'sd'); ?> 
						<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="response-loading" style="display: none;" />
					</h3>
					<div class="ticket_reponse_box">
						<textarea name="response[message]" id="response-message"></textarea>
	<!-- 					<div class="ticket_response_actions">
							<label for="response-private"><input type="checkbox" name="reponse[private]" id="response-private" /><?php _e('Private?', 'sd')?></label>
							<input type="button" name="response[submit]" id="response-submit" class="button-primary" value="Reply" />
						</div> -->
					</div>
				<?php endif; ?>

				<h3 class="section_header"><?php _e('Ticket History', 'sd'); ?></h3>

				<div id="ticket_history">
					<!-- Ticket Responses -->
					<?php
						$responses = sd_get_ticket_log($ticket_id);
						if($responses):
							ob_start();
							foreach($responses as $response):
					?>
								<div class="issue-response-wrap <?php echo (empty($response->private) ? '' : 'private-reply'); ?>">
									<div class="issue-response">
										<p class="issue-header">
											<span class="issue-response-author">
												<?php echo esc_attr($response->comment_author); ?> updated this ticket.
											</span>
											<span class="issue-meta">
												<?php echo (empty($response->private) ? '' : '[Private Reply]'); ?>
												<?php if(strtotime($response->comment_date) > strtotime('-1 week')): ?>
													<?php echo human_time_diff( strtotime($response->comment_date), current_time('timestamp')) . ' ago'; ?>
												<?php else: ?>
													<?php $time_date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>
													<?php echo mysql2date($time_date_format, $response->comment_date); ?>
												<?php endif; ?>
											</span>
										</p>
										<p class="issue-message">
											<?php echo nl2br(wp_kses(trim($response->comment_content), array())); ?>
										</p>
									</div>
								</div>
						<?php 
							endforeach; 
						endif
;						?>
					<?php echo ob_get_clean(); ?>
				</div>

				<div id="original_ticket">
				<!-- Original Ticket Issue -->
					<div class="issue-response-wrap">
						<div class="issue-response">
							<p class="issue-header">
								<span class="issue-response-author">
									<?php echo esc_attr(get_the_author_meta('display_name', $ticket->post_author)); ?> submitted this issue.
								</span>
								<span class="issue-meta">
									<?php if(strtotime($ticket->post_date) > strtotime('-1 week')): ?>
										<?php echo human_time_diff( strtotime($ticket->post_date), current_time('timestamp')) . ' ago'; ?>
									<?php else: ?>
										<?php $time_date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>
										<?php echo mysql2date($time_date_format, $ticket->post_date); ?>
									<?php endif; ?>
								</span>
							</p>
							<p class="issue-message">
								<?php echo nl2br(wp_kses(trim($ticket->post_content), array())); ?>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div id="postbox-container-1" class="postbox-container">
		        <?php do_meta_boxes('sd_edit-ticket-page', 'side', null); ?>
		  </div>    

		  <div id="postbox-container-2" class="postbox-container">
		  		<?php do_meta_boxes('','normal',null);  ?>
		        <?php do_meta_boxes('','advanced',null); ?>
		  </div>
		</div><!-- end post-body -->
	</div>
</form>










