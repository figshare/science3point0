<?php
/*
Plugin Name: BuddyPress Activity Stream Bump to Top
Plugin URI: http://wordpress.org/extend/plugins/buddypress-activity-stream-bump-to-top/
Description: Bumps an activity record to the top of the stream on activity comment replies
Author: rich fuller - rich! @ etiviti
Author URI: http://buddypress.org/developers/nuprn1/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.3.1
Text Domain: bp-activity-bump
Site Wide Only: true
Network: true
*/

//link to show unbumped activity items?
//not yet using the current has_activities BP_Activity_Activity:get way... i may be able to get around it by building a custom db query to pull out a list of activity_ids (meeting the criteria for included bumped activity types with no activity comments) then pass that into BP_Activity_Activity:get_specific


/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_activity_bump_init() {

    require( dirname( __FILE__ ) . '/bp-activity-bump.php' );
	
}
add_action( 'bp_init', 'bp_activity_bump_init' );

//add admin_menu page
function bp_activity_bump_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-activity-bump-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Activity Bump Admin', 'bp-activity-bump' ), __( 'Activity Bump', 'bp-activity-bump' ), 'manage_options', 'bp-activity-bump-settings', 'bp_activity_bump_admin' );	

	//set up defaults

}

//loader file never works - as it doesn't hook the admin_menu
if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_activity_bump_admin_init' );
} else {
	add_action( 'bp_init', 'bp_activity_bump_admin_init');
}

function bp_activity_bump_admin_init() {
	add_action( 'admin_menu', 'bp_activity_bump_admin_add_admin_menu', 25 );
}

?>