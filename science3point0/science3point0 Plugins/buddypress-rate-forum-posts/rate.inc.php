<?php

// setup some global variables
$rfp = new stdClass;

/* rating scores that effect how a post is boosted or diminished. Edit these to match your website */
$rfp->superboost = get_option( 'rfp_superboost' );
$rfp->boost = get_option( 'rfp_boost' );
$rfp->diminish = get_option( 'rfp_diminish' );
$rfp->hide = get_option( 'rfp_hide' );


/* stop editing */


$rfp->bb_meta_key = 'rfp_rating';
$rfp->bb_object_type = 'bb_post';


// update the poster's karma points
function rfp_update_post_author_karma( $post_id, $direction ) {
	if ( !$post_id || !$direction ) 
		return false;
	global $wpdb, $bb_posts_table;
	
	//$bb_posts_table = $bb_table_prefix . 'posts';
	$poster_id =  $wpdb->get_var( $wpdb->prepare( "SELECT poster_id FROM {$bb_posts_table} WHERE post_id = {$post_id}" ) );
	$karma = get_usermeta( $poster_id, 'rfp_post_karma' );
	if ( $direction == 'pos' ) $value = 1; // abstract this 
	elseif ( $direction == 'neg' ) $value = -1;
	update_usermeta( $poster_id, 'rfp_post_karma', $karma + $value );
}

// get the poster's karma from the post id
function rfp_get_post_author_karma( $post_id ) {
	if ( !$post_id ) 
		return false;
	global $wpdb, $bb_posts_table;
	
	$poster_id =  $wpdb->get_var( $wpdb->prepare( "SELECT poster_id FROM {$bb_posts_table} WHERE post_id = {$post_id}" ) );
	return get_usermeta( $poster_id, 'rfp_post_karma' );
}
	

// save the user's rating history in user_meta as post_id => direction array
// this is used to make sure the user does not rate more than once
function rfp_update_user_rating_history( $rater, $post_id, $direction ) {
	if ( !$rater || !$post_id ) 
		return false;
	$rating_history = get_usermeta( $rater, 'rfp_rating_history' );
	$rating_history[ $post_id ] = $direction;
	update_usermeta( $rater, 'rfp_rating_history', $rating_history ); 
}


// see if the user has already rated this post, or if it there own post
// return false if they have not rated, otherwise return a status message
function rfp_get_user_rated_post( $rater, $post_id ) {
	if ( !$rater || !$post_id ) 
		return true;
	global $wpdb, $bb_posts_table;
	
	if ( is_site_admin() )
		return false; // site admins can rate as much as they like
	
	//posters can't rate themselves.
	if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$bb_posts_table} WHERE poster_id = {$rater} AND post_id = {$post_id}" ) ) )
		return 'This is your post';	
	
	//see if it's already been rated
	$rating_history = get_usermeta( $rater, 'rfp_rating_history' );
	
	if ( $rating_history[ $post_id ] )
		//return false; // USED FOR TESTING 
		return 'Already rated';
	else
		return false; // all is well, they can rate
}

// return the post rating signed neg or positve
function rfp_get_post_rating_signed( $id ) {
	$meta_value	= rfp_get_post_rating( $id );
	
	if ( $meta_value > 0 ) return '+' . $meta_value;
	elseif ( $meta_value < 0 ) return '-' . $meta_value;
	else return '';
}

// returns the post rating
function rfp_get_post_rating( $id ) {
	if ( !$id  ) 
		return false;
	global $wpdb, $rfp, $bb_meta_table;
	
	return $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$bb_meta_table} WHERE object_type = '{$rfp->bb_object_type}' AND meta_key = '{$rfp->bb_meta_key}' AND object_id = {$id}" ) );
}

// save the post rating in the bb database
// bb_post_meta is the table used to do this, I created a new object_type called bb_post to label the data. kind-of overkill, but whatever
function rfp_update_post_rating( $id, $direction ) {
	if ( !$id || !$direction ) 
		return false;
	global $wpdb, $rfp, $bb_meta_table;
	
	if ( $direction == 'pos' ) $value = 1;
	elseif ( $direction == 'neg' ) $value = -1;

	$rating = $wpdb->get_row( $wpdb->prepare( "SELECT meta_id, meta_value FROM {$bb_meta_table} WHERE object_type = '{$rfp->bb_object_type}' AND meta_key = '{$rfp->bb_meta_key}' AND object_id = {$id}" ) );
		
	$wpdb->query( $wpdb->prepare( "REPLACE INTO {$bb_meta_table} ( meta_id, object_type, object_id, meta_key, meta_value ) VALUES (%d, %s, %d, %s, %d )", $rating->meta_id, $rfp->bb_object_type, $id, $rfp->bb_meta_key, $rating->meta_value + $value ) );
	
	return $rating->meta_value + $value;
}

?>