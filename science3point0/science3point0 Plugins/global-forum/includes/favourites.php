<?php
/* 
 *Porting bbpress favourite to global forum, the bridge does not allows loading functions.bb-users.php, so we need to port them
 */

/* Favorites */

function gf_get_user_favorites( $user_id, $topics = false ) {
	$user = bb_get_user( $user_id );
	if ( !empty($user->favorites) ) {
		if ( $topics )
			$query = new BB_Query( 'topic', array('favorites' => $user_id, 'index_hint' => 'USE INDEX (`forum_time`)'), 'get_user_favorites' );
		else
			$query = new BB_Query( 'post', array('favorites' => $user_id), 'get_user_favorites' );
		return $query->results;
	}
}

function gf_is_user_favorite( $user_id = 0, $topic_id = 0 ) {
    global $bbdb;
   
	if ( $user_id )
		$user = bb_get_user( $user_id );
	else
	 	global $user;
	if ( $topic_id )
		$topic = get_topic( $topic_id );
	else
		global $topic;
	if ( !$user || !$topic )
		return;
         // $user->favourites=explode(",",bb_get_usermeta( $user->ID, $bbdb->prefix . 'favorites'));
        //print_r($user);
          //$user->favourites=get_usermeta( $user->ID, $bbdb->prefix . 'favorites');
         
	if ( isset($user->favorites) )
	        return in_array($topic->topic_id, explode(',', $user->favorites));
	return false;
}

function gf_add_user_favorite( $user_id, $topic_id ) {
	global $bbdb;
	$user_id = (int) $user_id;
	$topic_id = (int) $topic_id;
	$user = bb_get_user( $user_id );
	$topic = get_topic( $topic_id );
	if ( !$user || !$topic )
		return false;
          $fv=$bbdb->prefix."favorites";
     //   $user->favourites=get_usermeta( $user->ID, $bbdb->prefix . 'favorites');
	$fav = $user->favorites ? explode(',', $user->favorites) : array();
	if ( ! in_array( $topic_id, $fav ) ) {
		$fav[] = $topic_id;
		$fav = implode(',', $fav);
		update_usermeta( $user->ID, 'favorites', $fav);
	}
	do_action('bb_add_user_favorite', $user_id, $topic_id);
	return true;
}

function gf_remove_user_favorite( $user_id, $topic_id ) {
	global $bbdb;
	$user_id = (int) $user_id;
	$topic_id = (int) $topic_id;
	$user = bb_get_user( $user_id );
	if ( !$user )
		return false;
         //$user->favourites=get_usermeta( $user->ID, 'favorites');
	$fav = explode(',', $user->favorites);
	if ( is_int( $pos = array_search($topic_id, $fav) ) ) {
		array_splice($fav, $pos, 1);
		$fav = implode(',', $fav);
		update_usermeta( $user->ID,  'favorites', $fav);
	}
	do_action('bb_remove_user_favorite', $user_id, $topic_id);
	return true;
}

function gf_get_add_remove_fav_link($topic=null){
   // return;//not implemented
    if(!is_user_logged_in())
        return;
    global $gf_forum_topics_template,$bp;
    if(!$topic)
        $topic=$gf_forum_topics_template->topic;

    $link=gf_get_the_topic_permalink($topic->topic_id);
    if(gf_is_user_favorite($bp->loggedin_user->id, $topic->topic_id)){
        $fav_link=$link."unfav/?_wpnonce=".wp_create_nonce("unfav-".$topic->topic_id);
        $output="<a href=\"$fav_link\" class=\"unfav\">".__("Remove from favorite","gf")."</a>";
    }
    else{
         $fav_link=$link."fav/?_wpnonce=".wp_create_nonce("fav-".$topic->topic_id);
        $output="<a href=\"$fav_link\" class=\"unfav\">".__("Add to favorite","gf")."</a>";
    }
 return $output;
}
//get link for add remove to favorite
//get link for view favourite

function gf_is_my_favorite(){
    
    return gf_is_view("favorites");
}

function gf_get_my_favorites_link(){
    return gf_get_home_url()."/view/favorites";
}

function gf_get_unreplied_topics_link(){
       return gf_get_home_url()."/view/unreplied";

}
//get args for current view
function gf_get_current_view(){
 if(!is_user_logged_in())
     return;
 global $bp;
 $args=array("user_id"=>$bp->loggedin_user->id);

 if(gf_is_view("unreplied"))
     $args["type"]="unreplied";
 else if(gf_is_view("favorites"))
        $args["type"]="favorites";

 return apply_filters("gf_current_view",$args);
}
 
 
/*
 * Check what is the current view ?
 */
function gf_is_view($type=null){

  global $bp;
  if(!($bp->current_component==$bp->gf->slug&&$bp->current_action=="view"))
          return false;
  if(!$type)
      return true;
 if($bp->action_variables[0]==$type)
         return true;
 else return false;

}

//v1.1//make easier to create new viw by devs
function gf_get_registered_view(){
    //not implemented in v1.0
}

function gf_get_current_view_description(){
$view_desc=array("unreplied"=>__("Viewing Unreplied topics.","gf"),
                 "favorites"=>__("Viewing Favorite Topics,","gf")
  );
global $bp;
if(gf_is_view())
    return $view_desc[$bp->action_variables[0]];
}


?>