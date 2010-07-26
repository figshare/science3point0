<?php
/* 
 * Topic Business functions
 *
 */

/**
 *
 * @param <type> $topic_id
 * @return <type> topic Object
 * @desc get details for a perticular topic
 */
function gf_get_topic_details( $topic_id ) {
	do_action( 'bbpress_init' );
	$query = new BB_Query( 'topic', 'topic_id=' . $topic_id . '&page=1' /* Page override so bbPress doesn't use the URI */ );
     return $query->results[0];
}
/**
 *
 * @param <type> $topic_slug
 * @return <type>
 * @desc get the topic id from slug
 */
function gf_get_topic_id_from_slug( $topic_slug ) {
	do_action( 'bbpress_init' );
	return bb_get_id_from_slug( 'topic', $topic_slug );
}


/*
 * Topic management
 */
//create topic

function gf_new_forum_topic( $topic_title, $topic_text, $topic_tags, $forum_id ) {
	global $bp;

	if ( empty( $topic_title ) || empty( $topic_text ) )
		return false;

	$topic_title = apply_filters( 'gf_forum_topic_title_before_save', $topic_title );
	$topic_text = apply_filters( 'gf_forum_topic_text_before_save', $topic_text );
	$topic_tags = apply_filters( 'gf_forum_topic_tags_before_save', $topic_tags );
	$forum_id = apply_filters( 'gf_forum_topic_forum_id_before_save', $forum_id );

	if ( $topic_id =gf_create_topic( array( 'topic_title' => $topic_title, 'topic_text' => $topic_text, 'topic_tags' => $topic_tags, 'forum_id' => $forum_id ) ) ) {
		$topic = gf_get_topic_details( $topic_id );
		return $topic;
	}

	return false;
}

function gf_update_forum_topic( $topic_id, $topic_title, $topic_text ) {
	global $bp;

	$topic_title = apply_filters( 'gf_forum_topic_title_before_save', $topic_title );
	$topic_text = apply_filters( 'gf_forum_topic_text_before_save', $topic_text );

	if ( $topic = gf_update_topic( array( 'topic_title' => $topic_title, 'topic_text' => $topic_text, 'topic_id' => $topic_id ) ) ) {

	/* Update the activity stream item */
            if(gf_enabled_post_activity ()){
		if ( function_exists( 'bp_activity_delete_by_item_id' ) )
			   bp_activity_delete(array("component"=>$bp->gf->id,"item_id"=>$topic->forum_id,"secondary_item_id"=>$topic_id, 'type' => 'new_forum_topic' ) );

		 
                $activity_action = sprintf( __( '%s started a new topic %s in the forum %s:', 'gf'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . gf_get_topic_permalink( $topic->topic_slug )  .'/">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . gf_get_forum_permalink( $topic->forum_id ) . '">' . attribute_escape(gf_get_forum_name($topic->forum_id) ) . '</a>' );
		$activity_content = bp_create_excerpt( $topic_text );

		/* Record this in activity streams */
		gf_record_activity( array(
			'action' => apply_filters( 'gf_activity_new_forum_topic_action', $activity_action, $topic_text, &$topic ),
			'content' => apply_filters( 'gf_activity_new_forum_topic_content', $activity_content, $topic_text, &$topic ),
			'primary_link' => apply_filters( 'gf_activity_new_forum_topic_primary_link', gf_get_topic_permalink($topic->topic_slug) . '/' ),
			'type' => 'new_forum_topic',
			'item_id' => $topic->forum_id,
			'user_id' => (int)$topic->topic_poster,
			'secondary_item_id' => $topic->topic_id,
			'recorded_time' => $topic->topic_time
		) );

            }
            do_action( 'gf_update_forum_topic', &$topic );

		return $topic;
	}

	return false;
}



function gf_delete_forum_topic( $topic_id ) {
	global $bp;

	if ( gf_delete_topic( array( 'topic_id' => $topic_id ) ) ) {

		do_action( 'gf_delete_forum_topic', $topic_id );

		return true;
	}

	return false;
}
function gf_create_topic( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_title' => '',
		'topic_slug' => '',
		'topic_text' => '',
		'topic_poster' => $bp->loggedin_user->id, // accepts ids
		'topic_poster_name' => $bp->loggedin_user->fullname, // accept names
		'topic_last_poster' => $bp->loggedin_user->id, // accepts ids
		'topic_last_poster_name' => $bp->loggedin_user->fullname, // accept names
		'topic_start_time' => date( 'Y-m-d H:i:s' ),
		'topic_time' => date( 'Y-m-d H:i:s' ),
		'topic_open' => 1,
		'topic_tags' => false, // accepts array or comma delim
		'forum_id' => 0 // accepts ids or slugs
	);

	$r = wp_parse_args( $args, $defaults );

       // print_r($r);

	extract( $r, EXTR_SKIP );

	$topic_title = strip_tags( $topic_title );

	if ( empty( $topic_title ) || !strlen( trim( $topic_title ) ) )
		return false;

	if ( empty( $topic_slug ) )
		$topic_slug = sanitize_title( $topic_title );

	if ( !$topic_id = bb_insert_topic( array( 'topic_title' => stripslashes( $topic_title ), 'topic_slug' => $topic_slug, 'topic_poster' => $topic_poster, 'topic_poster_name' => $topic_poster_name, 'topic_last_poster' => $topic_last_poster, 'topic_last_poster_name' => $topic_last_poster_name, 'topic_start_time' => $topic_start_time, 'topic_time' => $topic_time, 'topic_open' => $topic_open, 'forum_id' => (int)$forum_id, 'tags' => $topic_tags ) ) )
		return false;


	/* Now insert the first post. */
	if ( !gf_insert_post( array( 'topic_id' => $topic_id, 'post_text' => $topic_text, 'post_time' => $topic_time, 'poster_id' => $topic_poster ) ) )
		return false;

        $topic=get_topic($topic_id);
        //create topic feed
        if(gf_enabled_post_activity()){
            /* Record this in activity streams */
            $activity_action = sprintf( __( '%s started a new topic %s in the forum %s:', 'gf'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . gf_get_topic_permalink( $topic->topic_slug )  .'/">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . gf_get_forum_permalink( $topic->forum_id ) . '">' . attribute_escape(gf_get_forum_name($topic->forum_id) ) . '</a>' );
		$activity_content = bp_create_excerpt( $topic_text );

		/* Record this in activity streams */
		gf_record_activity( array(
			'action' => apply_filters( 'gf_activity_new_forum_topic_action', $activity_action, $topic_text, &$topic ),
			'content' => apply_filters( 'gf_activity_new_forum_topic_content', $activity_content, $topic_text, &$topic ),
			'primary_link' => apply_filters( 'gf_activity_new_forum_topic_primary_link', gf_get_topic_permalink($topic->topic_slug ). '/' ),
			'type' => 'new_forum_topic',
			'item_id' => $forum_id,
			'secondary_item_id' => $topic->topic_id
		) );

        }
            
	do_action( 'gf_create_topic', $topic_id );


	return $topic_id;
}

//update topic
function gf_update_topic( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_id' => false,
		'topic_title' => '',
		'topic_text' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( !$topic_id = bb_insert_topic( array( 'topic_id' => $topic_id, 'topic_title' => stripslashes( $topic_title ) ) ) )
		return false;

	if ( !$post = bb_get_first_post( $topic_id ) )
		return false;

	/* Update the first post */
	if ( !$post = gf_insert_post( array( 'post_id' => $post->post_id, 'topic_id' => $topic_id, 'post_text' => $topic_text, 'post_time' => $post->post_time, 'poster_id' => $post->poster_id, 'poster_ip' => $post->poster_ip, 'post_status' => $post->post_status, 'post_position' => $post->post_position ) ) )
		return false;

	return gf_get_topic_details( $topic_id );
}

//make a topic sticky/unsticky
function gf_sticky_topic( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_id' => false,
		'mode' => 'stick' // stick/unstick
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( 'stick' == $mode )
		return bb_stick_topic( $topic_id );
	else if ( 'unstick' == $mode )
		return bb_unstick_topic( $topic_id );

	return false;
}

//openclose a topic
function gf_openclose_topic( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_id' => false,
		'mode' => 'close' // stick/unstick
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( 'close' == $mode )
		return bb_close_topic( $topic_id );
	else if ( 'open' == $mode )
		return bb_open_topic( $topic_id );

	return false;
}
//delete topic
function gf_delete_topic( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_id' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
        $topic=get_topic($topic_id);
        //delete from activity
        if(gf_enabled_post_activity ()&&function_exists("bp_activity_delete") ){
             bp_activity_delete(array("component"=>$bp->gf->id,"item_id"=>$topic->forum_id,"secondary_item_id"=>$topic_id,'type' => 'new_forum_topic'));
             bp_activity_delete(array("component"=>$bp->gf->id,"item_id"=>$topic->topic_id, 'type' => 'new_forum_post' ) );//delete all posts activity


        }
            
	return bb_delete_topic( $topic_id, 1 );
}

function gf_get_topic_extras( $topics ) {
	global $bp, $wpdb, $bbdb;

	if ( empty( $topics ) )
		return $topics;

	/* Get the topic ids */
	foreach ( (array)$topics as $topic ) $topic_ids[] = $topic->topic_id;
	$topic_ids = $wpdb->escape( join( ',', (array)$topic_ids ) );

	/* Fetch the topic's last poster details */
	$poster_details = $wpdb->get_results( $wpdb->prepare( "SELECT t.topic_id, t.topic_last_poster, u.user_login, u.user_nicename, u.user_email, u.display_name FROM {$wpdb->users} u, {$bbdb->topics} t WHERE u.ID = t.topic_last_poster AND t.topic_id IN ( {$topic_ids} )" ) );
	for ( $i = 0; $i < count( $topics ); $i++ ) {
		foreach ( (array)$poster_details as $poster ) {
			if ( $poster->topic_id == $topics[$i]->topic_id ) {
				$topics[$i]->topic_last_poster_email = $poster->user_email;
				$topics[$i]->topic_last_poster_nicename = $poster->user_nicename;
				$topics[$i]->topic_last_poster_login = $poster->user_login;
				$topics[$i]->topic_last_poster_displayname = $poster->display_name;
			}
		}
	}

	/* Fetch fullname for the topic's last poster */
	if ( function_exists( 'xprofile_install' ) ) {
		$poster_names = $wpdb->get_results( $wpdb->prepare( "SELECT t.topic_id, pd.value FROM {$bp->profile->table_name_data} pd, {$bbdb->topics} t WHERE pd.user_id = t.topic_last_poster AND pd.field_id = 1 AND t.topic_id IN ( {$topic_ids} )" ) );
		for ( $i = 0; $i < count( $topics ); $i++ ) {
			foreach ( (array)$poster_names as $name ) {
				if ( $name->topic_id == $topics[$i]->topic_id )
					$topics[$i]->topic_last_poster_displayname = $name->value;
			}
		}
	}

	return $topics;
}





/**
 * @desc Get forum topics
 * @global <type> $bp
 * @global <type> $wpdb
 * @global <type> $hidden_forums_list
 * @param <type> $args
 * @return <type> 
 */
function gf_get_forum_topics( $args = '' ) {
	global $bp,$wpdb,$hidden_forums_list;

	do_action( 'bbpress_init' );

	$defaults = array(
		'type' => 'newest',
		'forum_id' =>gf_get_root_forum_id(),
		'user_id' => false,
		'page' => 1,
		'per_page' => 15,
		'exclude' => false,
		'show_stickies' => 'all',
		'filter' => false // if $type = tag then filter is the tag name, otherwise it's terms to search on.
	);
       
	$r = wp_parse_args( $args, $defaults );
        extract( $r, EXTR_SKIP );
        if($forum_id==gf_get_root_forum_id())
            $forum_id=false;//if it is root forum, we need to include topics from all the sub forums
        /* if viewing root forum, allow to show recent topics*/
       $exclude=gf_get_excluded_forums();
	   
	 
        if(!empty ($exclude))
                    $exclude=implode(',', $exclude);
        $hidden_forums_list=$exclude;
       
        if ( $exclude ) {
		$exclude = '-' . str_replace(',', ',-', $exclude);
		$exclude = str_replace('--', '-', $exclude);
		if ( $forum_id )
			$forum_id = (string) $forum_id . ",$exclude";
		else
			$forum_id = $exclude;
	}
     
	switch ( $type ) {
		case 'newest':
			$query = new BB_Query( 'topic', array( 'forum_id' => $forum_id, 'topic_author_id' => $user_id, 'per_page' => $per_page, 'page' => $page, 'number' => $per_page, 'exclude' => $exclude, 'topic_title' => $filter, 'sticky' => $show_stickies ), 'get_latest_topzzics' );
		$topics =& $query->results;

		break;

		case 'popular':
			$query = new BB_Query( 'topic', array( 'forum_id' => $forum_id, 'topic_author_id' => $user_id, 'per_page' => $per_page, 'page' => $page, 'order_by' => 't.topic_posts', 'topic_title' => $filter, 'sticky' => $show_stickies ) );
			$topics =& $query->results;
		break;

		case 'unreplied':
			$query = new BB_Query( 'topic', array( 'forum_id' => $forum_id, 'topic_author_id' => $user_id, 'post_count' => 1, 'per_page' => $per_page, 'page' => $page, 'order_by' => 't.topic_time', 'topic_title' => $filter, 'sticky' => $show_stickies ) );
			$topics =& $query->results;
		break;

		case 'tags':
			$query = new BB_Query( 'topic', array( 'forum_id' => $forum_id, 'topic_author_id' => $user_id, 'tag' => $filter, 'per_page' => $per_page, 'page' => $page, 'order_by' => 't.topic_time', 'sticky' => $show_stickies ) );
			$topics =& $query->results;
		break;
            case 'favorites':
                $user=bb_get_user($user_id);

                $user_favorites=$user->favorites;
                if(!empty($user_favorites)){
               $query = new BB_Query( 'topic', array('forum_id' => $forum_id,'favorites' => $user_id, 'index_hint' => 'USE INDEX (`forum_time`)'), 'get_user_favorites' );
                 $topics =& $query->results;
                }
                else
                    $topics=null;
                 break;
            case 'my-topics':
                 $query = new BB_Query( 'topic',array( 'forum_id' => $forum_id,'page' => $page,'per_page' => $per_page, 'topic_author_id' => $user_id, 'order_by' => 't.topic_time'), 'get_recent_user_threads' );
                 $topics =& $query->results;
                 break;
	}

	return apply_filters( 'gf_get_forum_topics', $topics );
}


function gf_filter_out_forum($where){
global $hidden_forums_list;
	$prefix=""; if (strpos($where," t.")) {$prefix="t.";} elseif (strpos($where," p.")) {$prefix="p.";}
	echo  $where." AND ".$prefix."forum_id NOT IN ($hidden_forums_list) ";
    return  $where." AND ".$prefix."forum_id NOT IN ($hidden_forums_list) ";

}


function gf_is_my_topics(){
    global $bp;
    return $bp->gf->is_my_topics;
}

function gf_get_my_topics_link(){
    return gf_get_home_url()."/personal/my-topics";
}
?>