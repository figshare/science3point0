<?php
/*
 Plugin Name: BuddyPress Edit Activity Stream
 Plugin URI: http://wordpress.org/extend/plugins/buddypress-edit-activity-stream/
 Description: Allow user (set timeout) or admins to edit activity items
 Author: rich fuller - rich! @ etiviti
 Author URI: http://buddypress.org/developers/nuprn1/
 License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 Version: 0.3.0
 Text Domain: bp-activity-edit
 Site Wide Only: true
*/

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_edit_activity_init() {

    require( dirname( __FILE__ ) . '/bp-activity-edit.php' );
	
}
add_action( 'bp_init', 'bp_edit_activity_init' );

//add admin_menu page
function bp_edit_activity_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-activity-edit-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Activity Edit Admin', 'bp-activity-edit' ), __( 'Activity Edit', 'bp-activity-edit' ), 'manage_options', 'bp-activity-edit-settings', 'bp_edit_activity_admin' );	

	//set up defaults

}

//loader file never works - as it doesn't hook the admin_menu
if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_edit_activity_admin_init' );
} else {
	add_action( 'bp_init', 'bp_edit_activity_admin_init');
}

function bp_edit_activity_admin_init() {
	add_action( 'admin_menu', 'bp_edit_activity_admin_add_admin_menu', 25 );
}

?>