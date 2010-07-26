<?php
/* 
 * Stats
 * and open the template in the editor.
 */

//this is an in efficient method to find the count, we wil use a better methiod in 1.1
//find the total topic count across all forums managed by global forum
/* 
 * used in global-forum dashboard
 */
function gf_get_total_topic_count(){
    return gf_get_forums_topic_count_recursive(gf_get_root_forum_id());

}
/**
 * Recursively counts all the  the topics inside a forum or its child forum
 * 
 */
function gf_get_forums_topic_count_recursive($forum_id){

    global $bbdb;
    $topic_count=gf_get_forum_topic_count($forum_id);
    $children=$bbdb->get_col($bbdb->prepare("select forum_id from {$bbdb->forums} where forum_parent=%d",$forum_id));

    if(!empty($children))
    foreach($children as $child) {
         $topic_count+=gf_get_forums_topic_count_recursive($child);
    }
        return intval($topic_count);

}
/**
 * @desc recursively count all the posts insuide a forum and all of its sub forums
 * @global <type> $bbdb
 * @param <type> $forum_id
 * @return <type> 
 */
function gf_get_forum_posts_count_recursive($forum_id){
    global $bbdb;
    $posts_count=gf_get_forum_posts_count($forum_id);
    $children=$bbdb->get_col($bbdb->prepare("select forum_id from {$bbdb->forums} where forum_parent=%d",$forum_id));

    if(!empty($children))
    foreach($children as $child) 
         $posts_count+=gf_get_forum_posts_count_recursive($child);
    
        return intval($posts_count);
}
/**
 * @desc Count all the posts inside the forums managed by global forums
 * used in admin dashboard
 * @return <type> 
 */
function gf_get_total_posts_count(){
return gf_get_forum_posts_count_recursive(gf_get_root_forum_id());
}

/**
 * @desc Count all the subforums inside a forum
 * @global  $bbdb
 * @param <type> $forum_id
 * @return <type> 
 */
function gf_get_total_forums_count_recursive($forum_id){
    global $bbdb;
    $forum_count= gf_get_child_forum_count($forum_id);
    $q="select forum_id from {$bbdb->forums} where forum_parent=%d";
    $child_forums=$bbdb->get_col($bbdb->prepare($q,$forum_id));
     if(!empty($child_forums))
     foreach($child_forums as $child)
         $forum_count+=gf_get_total_forums_count_recursive($child);
   return intval($forum_count);
}
/**
 * @desc Total Forums managed by global forums plugin
 *  Used in  Admin dashboard of global forum
 * @return <type> 
 */
function gf_get_total_forums_count(){
    return gf_get_total_forums_count_recursive(gf_get_root_forum_id());
  }
/**
 *
 * @global  $bbdb
 * @param <type> $forum_id
 * @return <type> 
 */
function gf_get_child_forum_count($forum_id){
    global $bbdb;
    $query="select count('*') from {$bbdb->forums} where forum_parent= %d";
    $count=$bbdb->get_var($bbdb->prepare($query,$forum_id));
    return intval($count);
}
/**
 * @desc count total users
 * @global <type> $wpdb
 * @return <type> 
 */
function gf_get_total_users(){
  global $wpdb;
  $count=$wpdb->get_var($wpdb->prepare("select count('*') from {$wpdb->users} where user_status='0'"));
  return intval($count);
}
/*
 * @desc count total tags
 */
function gf_get_total_tags(){
    global $bb_total_topic_tags;
	if ( isset($bb_total_topic_tags) ) {
		return $bb_total_topic_tags;
	}
	global $wp_taxonomy_object;
	$bb_total_topic_tags = $wp_taxonomy_object->count_terms( 'bb_topic_tag' );
	return $bb_total_topic_tags;
   
}

function gf_total_public_forum_topic_count( $type = 'newest' ) {
	//v 1.1
}
function gf_forums_get_forum_topicpost_count( $forum_id ) {
	global $wpdb, $bbdb;

	do_action( 'bbpress_init' );

	/* Need to find a bbPress function that does this */
	return $wpdb->get_results( $wpdb->prepare( "SELECT topics, posts from {$bbdb->forums} WHERE forum_id = %d", $forum_id ) );
}

function gf_forums_total_topic_count_for_user( $user_id = false ) {
	global $bp;

	do_action( 'bbpress_init' );

	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	$query = new BB_Query( 'topic', array( 'topic_author_id' => $user_id, 'page' => 1, 'per_page' => -1, 'count' => true ) );
	$count = $query->count;
	$query = null;

	return $count;
}


function gf_forums_total_topic_count() {
	do_action( 'bbpress_init' );

	$query = new BB_Query( 'topic', array( 'page' => 1, 'per_page' => -1, 'count' => true ) );
	$count = $query->count;
	$query = null;

	return $count;
}


/* total poasts count for a uaser*/
function gf_get_total_posts_count_for_user($user_id){
    global $bbdb,$wpdb;
   
  $exclude=gf_get_excluded_forums();
  $sql=$wpdb->prepare("select COUNT('*') from {$bbdb->posts} where poster_id=%d",$user_id);
  
  if(!empty($exclude))
  {//exclude posts in group forums
      $exclude_list="(".join(",",$exclude).")";
      $sql.=$wpdb->prepare(" AND forum_id not in {$exclude_list}");
  }
  
$num=$wpdb->get_var($wpdb->prepare($sql));
return intval($num);
}
?>