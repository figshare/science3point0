<?php
function bp_activity_hashtags_filter( $content ) {
	global $bp;
	
	//what are we doing here? - same at atme mentions
	$pattern = '/[#]([_0-9a-zA-Z-]+)/';
	preg_match_all( $pattern, $content, $hashtags );

	/* Make sure there's only one instance of each tag */
	if ( !$hashtags = array_unique( $hashtags[1] ) )
		return $content;

	//but we need to watch for edits and if something was already wrapped in html link - thus check for space or word boundary prior
	foreach( (array)$hashtags as $hashtag ) {
		$pattern = "/(^|\s|\b)#". $hashtag ."/";
		$content = preg_replace( $pattern, ' <a href="' . $bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/" . htmlspecialchars( $hashtag ) . '" rel="nofollow" class="hashtag">#'. htmlspecialchars( $hashtag) .'</a>', $content );
	}

	return $content;
}

function bp_activity_hashtags_querystring( $query_string, $object ) {
	global $bp;

	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return $query_string;

	if ( empty( $bp->action_variables[0] ) )
		return $query_string;

	if ( 'feed' == $bp->action_variables[1] )
		return $query_string;

	if ( strlen( $query_string ) < 1 )
		return 'display_comments=true&search_terms=#'. $bp->action_variables[0] . '<';

	/* Now pass the querystring to override default values. */
	$query_string .= '&display_comments=true&search_terms=#'. $bp->action_variables[0] . '<';

	return $query_string;
}
add_filter( 'bp_ajax_querystring', 'bp_activity_hashtags_querystring', 11, 2 );

//thanks r-a-y for the snippet
function bp_activity_hashtags_header() {
	global $bp, $bp_unfiltered_uri;
	
	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return;
	
	printf( __( '<h3>Activity results for #%s</h3>', 'bp-activity-hashtags' ), $bp->action_variables[0] );
	
}
add_action( 'bp_before_activity_loop', 'bp_activity_hashtags_header' );

function bp_activity_hashtags_page_title( $title, $rawtitle ) {
	global $bp;

	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return $title;

	if ( empty( $bp->action_variables[0] ) )
		return $title;

	if ( !empty( $rawtitle ) )
		return $title;

	return apply_filters( 'bp_activity_page_title', $title . esc_attr( $bp->action_variables[0] ), esc_attr( $bp->action_variables[0] ) );

}
add_filter( 'bp_page_title', 'bp_activity_hashtags_page_title', 1, 2 );

function bp_activity_hashtags_insert_rel_head() {
	global $bp;

	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return false;

	if ( empty( $bp->action_variables[0] ) )
		return false;
		
	$link = $bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/" . esc_attr( $bp->action_variables[0] ) . '/feed/';

	echo '<link rel="alternate" type="application/rss+xml" title="'. get_blog_option( BP_ROOT_BLOG, 'blogname' ) .' | '. esc_attr( $bp->action_variables[0] ) .' | Hashtag" href="'. $link .'" />';
}
add_action('bp_head','bp_activity_hashtags_insert_rel_head');


function bp_activity_hashtags_activity_feed_link( $feedurl ) {
	global $bp;

	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return $feedurl;

	if ( empty( $bp->action_variables[0] ) )
		return $feedurl;

	return $bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/" . esc_attr( $bp->action_variables[0] ) . '/feed/';

}
add_filter( 'bp_get_sitewide_activity_feed_link', 'bp_activity_hashtags_activity_feed_link', 1, 1 );

function bp_activity_hashtags_action_router() {
	global $bp, $wp_query;

	if ( $bp->current_component != $bp->activity->slug || $bp->current_action != BP_ACTIVITY_HASHTAGS_SLUG )
		return false;

	if ( empty( $bp->action_variables[0] ) )
		return false;
		
	if ( 'feed' == $bp->action_variables[1] ) {
	
		$link = $bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/" . esc_attr( $bp->action_variables[0] );
		$link_self = $bp->root_domain . "/" . $bp->activity->slug . "/". BP_ACTIVITY_HASHTAGS_SLUG ."/" . esc_attr( $bp->action_variables[0] ) . '/feed/';

		$wp_query->is_404 = false;
		status_header( 200 );

		include_once( dirname( __FILE__ ) . '/feeds/bp-activity-hashtags-feed.php' );
		die;
	
	} else {
	
		bp_core_load_template( 'activity/index' );
	
	}
	
}
add_action( 'wp', 'bp_activity_hashtags_action_router', 3 );


function bp_activity_hashtags_current_activity() {
	global $activities_template;
	return $activities_template->current_activity;
}
?>