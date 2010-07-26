<?php
define ( 'BP_POSTSONPROFILE_IS_INSTALLED', 1 );
define ( 'BP_POSTSONPROFILE_VERSION', '0.1' );

if ( !defined( 'BP_POSTSONPROFILE_SLUG' ) )
	define ( 'BP_POSTSONPROFILE_SLUG', 'posts' );

if ( file_exists( WP_CONTENT_DIR . '/languages/bp-postsonprofile-' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-postsonprofile', WP_CONTENT_DIR . '/languages/bp-postsonprofile-' . get_locale() . '.mo' );
elseif ( file_exists( dirname( __FILE__ ) . '/languages/bp-postsonprofile-' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-postsonprofile', dirname( __FILE__ ) . '/languages/bp-postsonprofile-' . get_locale() . '.mo' );

/**
 * bp_postsonprofile_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_postsonprofile_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->postsonprofile->id = 'postsonprofile';
	$bp->postsonprofile->table_name = $wpdb->base_prefix . 'bp_postsonprofile';
	$bp->postsonprofile->format_notification_function = 'bp_postsonprofile_format_notifications';
	$bp->postsonprofile->slug = BP_POSTSONPROFILE_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->postsonprofile->slug] = $bp->postsonprofile->id;
}
add_action( 'wp', 'bp_postsonprofile_setup_globals', 2 );
function bp_pop_cur_page() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    else
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $urlEnd = substr($pageURL, -3);
    return str_replace("/", "", $urlEnd);
}
function bp_postsonprofile_setup_nav() {
	global $bp, $wpdb;
	$user_id = bp_displayed_user_id() ? bp_displayed_user_id() : bp_loggedin_user_id();
    $where = $wpdb->prepare('WHERE post_author = %d AND post_type = %s AND post_status=%s', $user_id, 'post', 'publish');
    $post_count  = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );
    $post_count = $post_count ? $post_count : 0;
    $postsonprofile_link = $bp->loggedin_user->domain . $bp->postsonprofile->slug . '/';

	bp_core_new_nav_item( array(
		'name' => sprintf(__( 'Posts <span>(%d)</span>', 'bp-postsonprofile' ), $post_count),
		'slug' => $bp->postsonprofile->slug,
		'position' => 80,
		'screen_function' => 'bp_postsonprofile_screen_one',
	) );

	if ( (int) bp_pop_cur_page() > 0) {
	    bp_core_new_subnav_item( array(
		    'name' => 'Page '. bp_pop_cur_page(),
		    'slug' => 'page',
		    'parent_slug' => $bp->postsonprofile->slug,
		    'parent_url' => $postsonprofile_link,
		    'screen_function' => 'bp_postsonprofile_screen_one',
		    'position' => 20,
	    ) );
	    bp_core_new_subnav_item( array(
		    'name' => 'Page '.bp_pop_cur_page(),
		    'slug' => bp_pop_cur_page(),
		    'parent_slug' => $bp->postsonprofile->slug .'/page',
		    'parent_url' => $postsonprofile_link .'/page/',
		    'screen_function' => 'bp_postsonprofile_screen_one',
		    'position' => 20,
	    ) );
	}
}

/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'bp_postsonprofile_setup_nav' );
 */
add_action( 'wp', 'bp_postsonprofile_setup_nav', 2 );

function bp_postsonprofile_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the postsonprofile component pages.
	 */
	if ( $bp->current_component != $bp->postsonprofile->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/members/single/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/members/single/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_postsonprofile_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_postsonprofile_load_template_filter', 10, 2 );

function bp_postsonprofile_screen_one() {
	global $bp;

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_postsonprofile_screen_one' );

	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_postsonprofile_template_screen_one', 'postsonprofile/screen-one' ) );

}


?>
