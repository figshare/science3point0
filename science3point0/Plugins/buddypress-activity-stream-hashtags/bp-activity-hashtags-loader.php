<?php
/*
 Plugin Name: BuddyPress Activity Stream Hashtags
 Plugin URI: http://wordpress.org/extend/plugins/buddypress-activity-stream-hashtags/
 Description: Enable #hashtags linking within activity stream content - converts before database.
 Author: rich fuller - rich! @ etiviti
 Author URI: http://buddypress.org/developers/nuprn1/
 License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
 Version: 0.3.1
 Text Domain: bp-activity-hashtags
 Site Wide Only: true
*/


//We really need unicode support =) For example ”#tag” works ok, but ”#?????” — nope.

//if you want to change up the /activity/tag/myhashtag 
if ( !defined( 'BP_ACTIVITY_HASHTAGS_SLUG' ) )
	define( 'BP_ACTIVITY_HASHTAGS_SLUG', 'tag' );

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_activity_hashtags_init() {

	require( dirname( __FILE__ ) . '/bp-activity-hashtags.php' );
	
	if ( bp_is_active( 'activity' ) ) {
	
		//same set used for atme mentions
		add_filter( 'bp_activity_comment_content', 'bp_activity_hashtags_filter' );
		add_filter( 'bp_activity_new_update_content', 'bp_activity_hashtags_filter' );
		add_filter( 'group_forum_topic_text_before_save', 'bp_activity_hashtags_filter' );
		add_filter( 'group_forum_post_text_before_save', 'bp_activity_hashtags_filter' );
		add_filter( 'groups_activity_new_update_content', 'bp_activity_hashtags_filter' );		
		
		//what about blog activity?
		//add_filter( 'bp_blogs_activity_new_post_content', 'bp_activity_hashtags_filter' );
		//add_filter( 'bp_blogs_activity_new_comment_content', 'bp_activity_hashtags_filter' );
		
		//support edit activity stream plugin
		add_filter( 'bp_edit_activity_action_edit_content', 'bp_activity_hashtags_filter' );
		
		
		//ignore this - if we wanted to filter after - this would be it 
		//but then we can't search by the #hashtag via search_terms (since the trick is the ending </a>)
		//as the search_term uses LIKE %%term%% so we would match #child #children
		//add_filter( 'bp_get_activity_content_body', 'bp_activity_hashtags_filter' );
		
	}
	
}
add_action( 'bp_init', 'bp_activity_hashtags_init' );

//add admin_menu page
function bp_activity_hashtags_admin_add_admin_menu() {
	global $bp;
	
	if ( !is_site_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	//require ( dirname( __FILE__ ) . '/admin/bp-activity-hashtags-admin.php' );

	//add_submenu_page( 'bp-general-settings', __( 'Activity Hashtags Admin', 'bp-activity-hashtags' ), __( 'Activity Hashtags', 'bp-activity-hashtags' ), 'manage_options', 'bp-activity-hashtags-settings', 'bp_activity_hashtags_admin' );	

	//set up defaults

}

//loader file never works - as it doesn't hook the admin_menu
if ( defined( 'BP_VERSION' ) ) {
	add_action( 'admin_menu', 'bp_activity_hashtags_admin_init' );
} else {
	add_action( 'bp_init', 'bp_activity_hashtags_admin_init');
}

function bp_activity_hashtags_admin_init() {
	add_action( 'admin_menu', 'bp_activity_hashtags_admin_add_admin_menu', 25 );
}

?>