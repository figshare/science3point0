<?php 

function bp_activity_bump_admin_unique_types( ) {
	global $bp, $wpdb;
	
	$count = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT a.type FROM {$bp->activity->table_name} a ORDER BY a.date_recorded DESC" ) );
	
	return $count;
}

function bp_activity_bump_admin_type_check( $type, $currenttypes ) {
	if ( in_array( $type, $currenttypes) )
		echo 'checked';
		
	return;
}

function bp_activity_bump_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp_activity_bump_admin') ) {
	
		if( isset($_POST['ab_activity_types'] ) && !empty($_POST['ab_activity_types']) ) {
			update_option( 'bp_activity_bump_denied_activity_types', $_POST['ab_activity_types'] );
		} else {
			update_option( 'bp_activity_bump_denied_activity_types', '' );
		}
		
		$updated = true;
	}
	
	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit_restore'] ) && check_admin_referer('bp_activity_bump_admin_restore') ) {
	
		global $wpdb;
		
		$bumpdates = $wpdb->get_results( $wpdb->prepare( "SELECT activity_id, meta_value FROM {$bp->activity->table_name_meta} WHERE meta_key = 'bp_activity_bump_date_recorded'" ) );

		if ($bumpdates) {

			foreach ($bumpdates as $bumpdate) { 
			
				$q = $wpdb->query( $wpdb->prepare( "UPDATE {$bp->activity->table_name} SET date_recorded = %s WHERE id = %d", $bumpdate->meta_value, $bumpdate->activity_id ) );
			
				wp_cache_delete( 'bp_activity_meta_bp_activity_bump_date_recorded_' . $bumpdate->activity_id, 'bp' );
				
			}			
			wp_cache_delete( 'bp_activity_sitewide_front', 'bp' );
			
			$d = $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->activity->table_name_meta} WHERE meta_key = 'bp_activity_bump_date_recorded'" ) );
			
			$convertupdated = true;
		}
		
	}
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Activity Bump Admin', 'bp-activity-bump' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-activity-bump' ) . "</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-activity-bump-settings' ?>" name="bp-activity-bump-settings-form" id="bp-activity-bump-settings-form" method="post">

			<h4><?php _e( 'Activity Types to Exclude', 'bp-activity-bump' ); ?></h4>
			<p class="description">This list is dynamic depending on what plugins have created new activity types. By selecting a type below - those items will not be bumped if an activity comment reply is made.</p>

			<table class="form-table">
				<?php

				$currenttypes = (array) get_option( 'bp_activity_bump_denied_activity_types');
				$uniquetypes = bp_activity_bump_admin_unique_types();

				foreach ($uniquetypes as $types) { ?>
					<tr>
						<th><label for="type-<?php echo $types->type ?>"><?php echo $types->type ?></label></th>
						<td><input id="type-<?php echo $types->type ?>" type="checkbox" <?php bp_activity_bump_admin_type_check( $types->type, $currenttypes ); ?> name="ab_activity_types[]" value="<?php echo $types->type ?>" /></td>
					</tr>
				<?php } ?>
			</table>
			
			<?php wp_nonce_field( 'bp_activity_bump_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>
			
		</form>
		
		<h3>Restore Activity Date back to Original:</h3>
		<p class="description">If you want to uninstall this plugin - please run this utility to restore the activity date_recorded values. Please note: Once you restore dates - you will lose all previous "bump" dates - even if you install this plugin again. Always backup your database first.</p>
		
		<?php if ( isset($convertupdated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Date Records Restored - You may uninstall this plugin now.', 'bp-activity-bump' ) . "</p>
		<p>Restored Bumped Activity Dates: ". count($bumpdates) ."</p></div>"; endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-activity-bump-settings' ?>" name="bp-activity-bump-restore-form" id="bp-activity-bump-restore-form" method="post">

			<?php wp_nonce_field( 'bp_activity_bump_admin_restore' ); ?>
			
			<p class="submit"><input style="color:red" id="bump_restore" type="submit" name="submit_restore" value="Restore Dates Now"/></p>
			
		</form>
		
		<h3>Author:</h3>
		<div id="bp-activity-bump-admin-tips" style="margin-left:15px;">
			<p><a href="http://etivite.com">Author's Demo BuddyPress site</a></p>
			<p>
			<a href="http://blog.etiviti.com/2010/05/buddypress-activity-stream-bump-to-top-plugin/">Activity Bump to Top About Page</a><br/> 
			<a href="http://blog.etiviti.com/tag/buddypress-plugin/">My BuddyPress Plugins</a><br/>
			<a href="http://blog.etiviti.com/tag/buddypress-hack/">My BuddyPress Hacks</a><br/>
			<a href="http://twitter.com/etiviti">Follow Me on Twitter</a>
			</p>
			<p><a href="http://buddypress.org/community/groups/buddypress-activity-bump-to-top/">BuddyPress.org Plugin Page</a> (with donation link)</p>
		</div>
		
		<script type="text/javascript"> jQuery(document).ready( function() { jQuery("#bump_restore").click( function() { if ( confirm( '<?php _e( 'Are you sure?', 'buddypress' ) ?>' ) ) return true; else return false; }); });</script>
		
	</div>
<?php
}

?>