<?php
/*
Plugin Name: BuddyPress Rate Forum Posts
Plugin URI: http://wordpress.org/extend/plugins/buddypress-rate-forum-posts/
Description: This plugin allows rating of BuddyPress forum posts and user karma. 
Version: 1.5.1
Revision Date: June 2, 2010
Requires at least: WPMU 2.9.1, BuddyPress 1.2
Tested up to: WPMU 2.9.2, BuddyPress 1.2.4
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Deryk Wenaus
Author URI: http://www.bluemandala.com
*/


/*
TODO:	internationalize
		highlight or hide posts in forum view

Thanks to the ilikethis plugin for inspiration, and Intense Debate for their thumb graphics and good layout. 

*/

//load the $rfp object and variables
require_once 'rate.inc.php';

// define default text
$rfp->pos_text = '&nbsp;';
$rfp->neg_text = '&nbsp;';
//$rfp->pos_text = 'Like';
//$rfp->neg_text = 'Dislike';


/* stop editing */


// set up karma levels defaults. I uses natural log (e) for levels :)
if ( !get_option( 'rfp_karma_levels' ) ) {
	update_option( 'rfp_karma_levels', maybe_serialize( array( 7, 19, 51, 138 ) ) );
	update_option( 'rfp_karma_label', 'Post Rating:' );
}
$rfp_karma = maybe_unserialize( get_option( 'rfp_karma_levels' ) );


// set up post highlighting defaults
if ( get_option( 'rfp_boost' ) == NULL ) {
	update_option( 'rfp_superboost', 25 );
	update_option( 'rfp_boost', 10 );
	update_option( 'rfp_diminish', -3 );
	update_option( 'rfp_hide', -6 );
}


// add the rating code into the topic page
function rfp_filter_rating_link( $post_text ) {
	global $bp;
	
	// only logged in users can rate - but you don't need to be a member of the group (too restrictive)
	if ( $bp->loggedin_user->id )
		$post_text .= rfp_get_rating_links();
		
	return $post_text;
}
add_filter( 'bp_get_the_topic_post_content', 'rfp_filter_rating_link', 3 ); 


// spits out html for making ratings - should test for group membership (don't use cookies)
function rfp_get_rating_links() {
	global $bp, $topic_template, $rfp;
	$post_id = $topic_template->post->post_id;
	$rating = rfp_get_post_rating_signed( $post_id );
	$rater = $bp->loggedin_user->id;
	
    $rate_link  = '<div id="rfp-rate-'.$post_id.'" class="rfp-rate">';
    $rate_link .= '<i></i>';     //status message will go here
    $rate_link .= '<span class="counter">' . $rating . '</span>';
    $rate_link .= '<a onclick="rfp_rate_js(' . $post_id . ',\'pos\','. $rater . ');" class="pos">'.$rfp->pos_text.'</a>';
    // $rate_link .= ' | ';  // use this for word links
    $rate_link .= '<a onclick="rfp_rate_js('.$post_id . ',\'neg\','. $rater . ');" class="neg">'.$rfp->neg_text.'</a>';
    $rate_link .= '</div>';
   
    return $rate_link;
}


// add little karma numbers next to the users name in forum topics
function rfp_filter_poster_karma( $poster_name_link ) {
	global $topic_template, $wpdb, $bb_posts_table;
	
	if ( get_option( 'rfp_karma_hide' ) )
		return $poster_name_link;
	
	$karma = rfp_get_post_author_karma( $topic_template->post->post_id ); 
	$relative_karma = rfp_calculate_relative_karma( $karma, $topic_template->post->poster_id );
	$poster_name_link .= rfp_poster_karma( $relative_karma );
	
	return $poster_name_link;
}
add_filter( 'bp_get_the_topic_post_poster_name', 'rfp_filter_poster_karma', 3 ); 


// show the user's karma in their member page
function rfp_show_poster_karma() {
	global $bp;

	$karma = get_usermeta( $bp->displayed_user->id, 'rfp_post_karma' );
	$relative_karma = rfp_calculate_relative_karma( $karma, $bp->displayed_user->id );
	
	if ( get_option( 'rfp_karma_hide' ) || $relative_karma == 0 || get_option( 'rfp_karma_never_minus' ) && $karma < 0 )
		return;

	echo '<div class="rfp-member-profile-karma">' . get_option( 'rfp_karma_label' ) . rfp_poster_karma( $relative_karma ) . '</div>';
}
add_action( 'bp_before_member_header_meta', 'rfp_show_poster_karma' );


// calculate relative karma based on number of posts
function rfp_calculate_relative_karma( $karma, $poster_id ) {
	global $bp, $topic_template, $wpdb, $bb_table_prefix, $bb_posts_table;
	
	if ( !$karma ) 
		return 0;
	
	$karma_calc = get_option( 'rfp_karma_calc' );
	
	if ( !$karma_calc || $karma_calc == 'total' )
		return intval( $karma );  // total karma - for quiet sites
	
	if ( $bp->current_component != 'groups' || $bp->current_action == 'my-groups' ) { // a bit of a hack in case we're not in the forum, but on the members page
		require_once $bp->site_options[ 'bb-config-location' ];
		$bb_posts_table = $bb_table_prefix . 'posts';
	}
	
	$count_posts = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( poster_id ) FROM {$bb_posts_table} WHERE poster_id = {$poster_id}" ) ); // this calculation includes deleted posts... not sure if it shouldn't
	
	if ( $karma_calc == 'average' ) {
		return intval( $karma / ( $count_posts + 0.5 ) );  // average karma - for busy sites
	} else if ( $karma_calc == 'mixed' ) {
		return intval( $karma / ( $count_posts / 25 + 1 ) - 0.5 ); // mix average & total - for medium traffic sites  // the division number (25) acts like a reference, say someone posted 1000 posts and each post got one point they would end up with 24.4 points. increase it to increase average points.
	} else if ( $karma_calc == 'mixed2' ) {
		return intval( $karma / ( $count_posts / 50 + 1 ) - 0.5 ); // higher value version of above - default
	}

}


// returns karma html with altered color depending on karma 'level'
function rfp_poster_karma( $karma ) {
	global $rfp_karma;
	
	// if karma is zero, or if karma should not be less than zero, return nada
	if ( $karma == 0 || get_option( 'rfp_karma_never_minus' ) && $karma < 0 )
		return;
		
	if ( $karma >= $rfp_karma[3] && $rfp_karma[3] != 0 ) $k = ' rfp-k4';
	elseif ( $karma >= $rfp_karma[2] && $rfp_karma[2] != 0 ) $k = ' rfp-k3';
	elseif ( $karma >= $rfp_karma[1] && $rfp_karma[1] != 0 ) $k = ' rfp-k2';
	elseif ( $karma >= $rfp_karma[0] && $rfp_karma[0] != 0 ) $k = ' rfp-k1';
	
	return "<span class='rfp-karma{$k}'>" . $karma . "p</span>";
}


// show the rating of the first post in the group forum directory and site-wide forum directory
function rfp_after_topic_title() {
	global $forum_template;
	$topic_id = $forum_template->topic->topic_id;
	$post = bb_get_first_post( $topic_id, false );
	$rating = rfp_get_post_rating_signed( $post->post_id );
	echo '<td class="rfp-topic-rating">' . $rating . '</td>';
}
add_filter( 'bp_directory_forums_extra_cell', 'rfp_after_topic_title', 3 ); 


// add a title to the rating above (in the th tag)
function rfp_after_topic_title_head() {
	echo '<th id="th-rating">Rating</th>';
}
add_filter( 'bp_directory_forums_extra_cell_head', 'rfp_after_topic_title_head', 3 ); 



// alter the look of the page by instering classes depending on rating. 
// change the values below in rate.inc.php
// only works in version 1.2.4 and above
function rfp_alter_post_based_on_rating( $class ) {
	global $rfp, $topic_template;
	
	$post_rating = rfp_get_post_rating( $topic_template->post->post_id );
	
	if ( $post_rating == 0 )
		return $class;	
	elseif ( $post_rating >= $rfp->superboost && $rfp->superboost != 0 ) 
		$class .= ' rfp-superboost';
	elseif ( $post_rating >= $rfp->boost && $rfp->boost != 0 ) 
		$class .= ' rfp-boost';
	elseif ( $post_rating <= $rfp->hide  && $rfp->hide != 0 ) 
		$class .= ' rfp-hide';
	elseif ( $post_rating <= $rfp->diminish && $rfp->diminish != 0 ) 
		$class .= ' rfp-diminish';
	
	return $class;
}
add_filter( 'bp_get_the_topic_post_css_class', 'rfp_alter_post_based_on_rating', 1, 1 );








// insert the javascript
function rfp_init() {
	global $bp;
	
	if ( $bp->current_component == 'groups' && $bp->action_variables[0] == 'topic') {
		wp_enqueue_script('rfp_rating_forum_posts', WP_PLUGIN_URL.'/buddypress-rate-forum-posts/js/rating.js');
	}
}
add_action('init', 'rfp_init');

//set up some globals, add css, and add a blogUrl variable
function rft_header() {
	global $bp, $bb_table_prefix, $bb_posts_table, $bb_meta_table;
	$bb_posts_table = $bb_table_prefix . 'posts';
	$bb_meta_table = $bb_table_prefix . 'meta';
	echo '<link rel="stylesheet" type="text/css" href="'.WP_PLUGIN_URL.'/buddypress-rate-forum-posts/css/rating.css" media="screen" />'."\n";
	echo '<script type="text/javascript">var blogUrl = \''.get_bloginfo('wpurl').'\'</script>'."\n";
	
	if (! function_exists('bp_the_topic_post_css_class'))	
		echo '<script type="text/javascript">var rfp_alter_posts_legacy = "1";</script>'."\n";
}
add_action('wp_head', 'rft_header');

function rft_footer() {
	global $bp;
	
	if ( $bp->action_variables[0] == 'topic' ) {
		echo '<script type="text/javascript">var topic_id = \''.bp_get_the_topic_id().'\'</script>'."\n";
	}
}
add_action('wp_footer', 'rft_footer');



// setting for the admin page
function rfp_add_admin_menu() {
	global $bp;
	if ( !$bp->loggedin_user->is_site_admin )
		return false;
	require ( dirname( __FILE__ ) . '/admin.php' );
	add_submenu_page( 'bp-general-settings', 'Rate Forum Posts', 'Rate Forum Posts', 'manage_options', 'rfp_admin', 'rfp_admin' );
}
add_action( 'admin_menu', 'rfp_add_admin_menu', 20 );

?>