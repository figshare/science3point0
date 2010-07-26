<?php
/**
 * bp_example_setup_globals()
 *
 * Sets up global variables for your component.
 */
function oqp_bp_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->oqp->id = 'oqp';

	//$bp->example->format_notification_function = 'bp_example_format_notifications';
	$bp->oqp->slug = ONEQUICKPOST_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->oqp->slug] = $bp->oqp->id;
}
/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_globals', 'oqp_bp_setup_globals' );
 */
//add_action( 'bp_setup_globals', 'oqp_bp_setup_globals' );
add_action( 'wp', 'oqp_bp_setup_globals', 2 );
add_action( 'admin_menu', 'oqp_bp_setup_globals', 2 );

/**
 * oqp_bp_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 */
function oqp_bp_setup_nav() {
	global $bp;

	bp_core_new_nav_item( array( 'name' => __( 'Quick Post','oqp'), 'slug' => $bp->oqp->slug, 'position' => 35, 'screen_function' => 'oqp_bp_screen_new', 'default_subnav_slug' => __( 'new-post', 'oqp-slugs' ), 'item_css_id' => $bp->oqp->id ) );

		
	$oqp_link = $bp->loggedin_user->domain . $bp->oqp->slug . '/';

	
	bp_core_new_subnav_item( array( 'name' => __( 'Add new', 'oqp' ), 'slug' => __( 'new-post', 'oqp-slugs' ), 'parent_url' => $oqp_link, 'parent_slug' => $bp->oqp->slug, 'screen_function' => 'oqp_bp_screen_new', 'position' => 10, 'user_has_access' => bp_is_my_profile() ) );
	//bp_core_new_subnav_item( array( 'name' => __( 'Edit pending', 'oqp' ), 'slug' => __( 'pending-posts', 'oqp-slugs' ), 'parent_url' => $oqp_link, 'parent_slug' => $bp->oqp->slug, 'screen_function' => 'oqp_bp_screen_pending', 'position' => 20, 'user_has_access' => bp_is_my_profile() ) );
	//bp_core_new_subnav_item( array( 'name' => __( 'Edit published', 'oqp' ), 'slug' => __( 'published-posts', 'oqp-slugs' ), 'parent_url' => $oqp_link, 'parent_slug' => $bp->oqp->slug, 'screen_function' => 'oqp_bp_screen_published', 'position' => 30, 'user_has_access' => bp_is_my_profile() ) );
	
	do_action( 'oqp_bp_setup_nav' );
}
/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'oqp_bp_setup_nav' );
 */
add_action( 'wp', 'oqp_bp_setup_nav', 2 );
add_action( 'admin_menu', 'oqp_bp_setup_nav', 2 );

//POST SCREEN
function oqp_bp_screen_new() {

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'oqp_bp_screen' );
	
	add_action( 'bp_template_title', 'oqp_bp_screen_title' );
	add_action( 'bp_template_content', 'oqp_bp_screen_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}



function oqp_bp_screen_title() {
	//TO FIX
}

function oqp_bp_screen_content() {
	global $bp;
	$args = array(
		'form_id'=>'buddypress',
		'form_url'=>$bp->loggedin_user->domain . $bp->oqp->slug . '/new-post'
	);
	Oqp_Form::oqp_block($args);
}

?>