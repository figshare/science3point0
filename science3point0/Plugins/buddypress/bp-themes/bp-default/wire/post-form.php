<?php do_action( 'bp_before_wire_post_content' ) ?>
<div id="wire-post-new">
	<form action="<?php bp_wire_get_action() ?>" id="wire-post-new-form" method="post">
		<div id="wire-poster-avatar">
		<a href="<?php echo bp_loggedin_user_domain() ?>">
			<?php bp_loggedin_user_avatar( 'width=60&height=60' ) ?>
		</a>
		</div>
		<div id="wire-post-new-metadata">
		<h5><?php global $bp; _e(sprintf("Write something to %s wire",bp_is_home()?__("your"):bp_core_get_user_displayname($bp->displayed_user->id)."'s"));?>
			
		</div>
	
		<div id="wire-post-new-input">
			<?php do_action( 'bp_before_wire_post_form' ); /* Deprecated -> */ do_action( 'bp_wire_custom_wire_boxes_after' ); ?>
			<textarea name="wire-post-textarea" id="wire-post-textarea" onfocus="if (this.value == '<?php _e( 'Start writing a short message...', 'buddypress' ) ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'Start writing a short message...', 'buddypress' ) ?>';}"><?php _e( 'Start writing a short message...', 'buddypress' ) ?></textarea>
	
			<?php do_action( 'bp_after_wire_post_form' ); /* Deprecated -> */ do_action( 'bp_wire_custom_wire_boxes_after' ); ?>
			
			<?php if ( bp_wire_show_email_notify() ) : ?>
			<div><span id="wire-email-notify"><input type="checkbox" name="wire-post-email-notify" id="wire-post-email-notify" value="1" /> <?php _e( 'Notify members via email (will slow down posting)', 'buddypress' ) ?></span></div>
			<?php endif; ?>
			
			<br class="clear" />
			<input type="submit" name="wire-post-submit" id="wire-post-submit" value="<?php _e( 'Post &raquo;', 'buddypress' ) ?>" />
			<input type="hidden" name="bp_wire_item_id" id="bp_wire_item_id" value="<?php echo bp_get_wire_item_id() ?>" />

			<?php wp_nonce_field( 'bp_wire_post' ) ?>
			
		</div>
	</form>
	
</div>

<?php do_action( 'bp_after_wire_post_content' ) ?>