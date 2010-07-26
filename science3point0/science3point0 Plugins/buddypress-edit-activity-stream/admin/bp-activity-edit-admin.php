<?php 

function bp_edit_activity_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp_edit_activity_admin') ) {
	
		if( isset($_POST['ab_activity_edit_time'] ) && !empty($_POST['ab_activity_edit_time']) ) {
			update_option( 'bp_edit_activity_lock_date', $_POST['ab_activity_edit_time'] );
		} else {
			update_option( 'bp_edit_activity_lock_date', '+30 Minutes' );
		}
		
		$updated = true;
	}
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Activity Edit Admin', 'bp-activity-edit' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-activity-edit' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-activity-edit-settings' ?>" name="bp-activity-edit-settings-form" id="bp-activity-edit-settings-form" method="post">

			<h5><?php _e( 'Activity Edit Timeout', 'bp-activity-edit' ); ?></h5>
	
			<table class="form-table">
				<th><label for="ab_activity_edit_time"><?php _e( "Time length:", 'bp-activity-edit' ) ?></label> </th>
				<td><input type="text" name="ab_activity_edit_time" id="ab_activity_edit_time" value="<?php echo get_option( 'bp_edit_activity_lock_date'); ?>"/></td>
			</table>
			
			<p class="description">Please Note: Time length uses <a href="http://www.php.net/manual/en/datetime.formats.relative.php">Relative Formats</a>; such as: +30 Minutes +1 Hour +2 Hours +1 week +3 Weeks +1 Month +2 Years</p>
			
			<?php wp_nonce_field( 'bp_edit_activity_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="<?php _e('Save Settings','bp-activity-edit') ?>"/></p>
			
		</form>
		
		<h3>Author:</h3>
		<div id="bp-activity-edit-admin-tips" style="margin-left:15px;">
			<p><a href="http://etivite.com">Author's Demo BuddyPress site</a></p>
			<p>
			<a href="http://blog.etiviti.com/2010/06/buddypress-edit-activity-stream/">Activity Edit About Page</a><br/> 
			<a href="http://blog.etiviti.com/tag/buddypress-plugin/">My BuddyPress Plugins</a><br/>
			<a href="http://blog.etiviti.com/tag/buddypress-hack/">My BuddyPress Hacks</a><br/>
			<a href="http://twitter.com/etiviti">Follow Me on Twitter</a>
			</p>
			<p><a href="http://buddypress.org/community/groups/buddypress-edit-activity-stream/">BuddyPress.org Plugin Page</a> (with donation link)</p>
		</div>
		
	</div>
<?php
}

?>