<?php get_header() ?>

<div class="activity no-ajax">

	<ul id="activity-stream" class="activity-list item-list">
		<li id="activity-1" class="activity activity_update">
		
		<?php do_action( 'bp_before_edit_activity_edit_form' ) ?>

		<?php if ( bp_edit_the_activity() ) : ?>
			<form action="<?php bp_edit_action() ?>" method="post" id="activity-edit-form" class="standard-form">

				<div class="activity-avatar">
					<?php bp_edit_the_avatar( 'type=full&width=100&height=100' ); ?>
				</div>

				<div class="activity-content">
					<h3><?php _e( 'Edit Activity', 'bp-activity-edit' ) ?></h3>

					<div class="activity-header">
						<?php if ( is_site_admin() ) : ?>
							<label for="activity_action"><?php _e( 'Action:', 'bp-activity-edit' ) ?></label>
							<input type="text" name="activity_action" id="activity_action" value="<?php bp_edit_the_activity_action() ?>" />
						<?php endif; ?>			
					</div>
					<div class="activity-inner">
						<label for="activity_content"><?php _e( 'Content:', 'bp-activity-edit' ) ?></label>
						<textarea name="activity_content" id="activity_content"><?php bp_edit_the_activity_content() ?></textarea>
					</div>
					<div class="activity-meta">
						<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php _e( 'Save Changes', 'bp-activity-edit' ) ?>" /></p>
					</div>
				</div>
				
				<?php do_action( 'bp_edit_activity_edit_form' ) ?>

				<?php wp_nonce_field( 'bp_edit_activity_post'. bp_edit_get_the_activity_id() ) ?>
			</form><!-- #forum-topic-form -->

		<?php else : ?>
			<div id="message" class="info">
				<p><?php _e( 'This activity does not exist.', 'bp-activity-edit' ) ?></p>
			</div>
		<?php endif;?>
		</li>
	</ul>
	
</div>

<?php get_footer() ?>