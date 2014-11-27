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

<h2><?php echo 'Viewing Ticket #'. $ticket_id .': ' . esc_attr(sd_get_ticket_title($ticket_id)); ?>
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
					</div>
				<?php endif; ?>

				<h3 class="section_header"><?php _e('Ticket History', 'sd'); ?></h3>

				<div class="ticket_history">
					<!-- Ticket Responses -->
					<?php
						if(is_array($ticket->replies) && !empty($ticket->replies)):
							ob_start();
							foreach($ticket->replies as $reply):
					?>
							<div class="issue-response-wrap <?php echo (empty($reply->private) ? '' : 'private-reply'); ?>">
								<div class="user-meta">
									<div class="user-gravatar">
										<?php echo get_avatar($reply->comment_author_email, 86); ?>
									</div>
									<div class="user-name">
										<?php echo $reply->comment_author; ?>
									</div>
								</div>
								<div class="time">
									<?php if(strtotime($reply->comment_date) > strtotime('-24 hours')): ?>
										<?php echo human_time_diff( strtotime($reply->comment_date), current_time('timestamp')) . ' ago'; ?>
									<?php else: ?>
										<?php $time_date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>
										<?php echo mysql2date($time_date_format, $reply->comment_date); ?>
									<?php endif; ?>
								</div>
								<div class="issue-response">
									<p>
										<?php echo nl2br(wp_kses(trim($reply->comment_content), array())); ?>
									</p>
								</div>
						</div>
						<?php
							endforeach;
						endif;
						?>
					<?php echo ob_get_clean(); ?>

					<!-- Original Ticket Issue -->
					<div class="issue-response-wrap">
						<div class="user-meta">
							<div class="user-gravatar">
								<?php echo get_avatar(sd_get_ticket_contact_email($ticket_id), 86); ?>
							</div>
							<div class="user-name">
								<?php echo sd_get_ticket_contact_name($ticket_id); ?>
							</div>
						</div>
						<div class="time">
							<?php if(strtotime($ticket->post_date) > strtotime('-24 hours')): ?>
								<?php echo human_time_diff( strtotime($ticket->post_date), current_time('timestamp')) . ' ago'; ?>
							<?php else: ?>
								<?php $time_date_format = get_option('date_format') . ' ' . get_option('time_format'); ?>
								<?php echo mysql2date($time_date_format, $ticket->post_date); ?>
							<?php endif; ?>
						</div>
						<div class="issue-response">
							<p>
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
