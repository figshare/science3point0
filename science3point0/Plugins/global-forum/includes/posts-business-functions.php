<?php
/* 
 * Business functions for forum posts
 * 
 */
/**
 * @desc Get Post details from post id
 */
function gf_get_post( $post_id ) {
	do_action( 'bbpress_init' );
	return bb_get_post( $post_id );
}
/*
 * gets all posts inside a topic
 */
function gf_get_topic_posts( $args = '' ) {
	do_action( 'bbpress_init' );

	$defaults = array(
		'topic_id' => false,
		'page' => 1,
		'per_page' => 15,
		'order' => 'ASC'
	);

	$args = wp_parse_args( $args, $defaults );

	$query = new BB_Query( 'post', $args, 'get_thread' );
	return gf_get_post_extras( $query->results );
}




function gf_new_forum_post( $post_text, $topic_id, $page = false ) {
	global $bp;

	if ( empty( $post_text ) )
		return false;

	$post_text = apply_filters( 'gf_forum_post_text_before_save', $post_text );
	$topic_id = apply_filters( 'gf_forum_post_topic_id_before_save', $topic_id );

	if ( $post_id = gf_insert_post( array( 'post_text' => $post_text, 'topic_id' => $topic_id ) ) ) {
		

                if(gf_enabled_post_activity ()){
                    $topic = gf_get_topic_details( $topic_id );
                    
                    $activity_action = sprintf( __( '%s posted on the forum topic %s in the forum %s:', 'gf'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . gf_get_topic_permalink($topic->topic_slug) .'/">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . gf_get_forum_permalink($topic->forum_id). '">' . attribute_escape(gf_get_forum_name($topic->forum_id) ) . '</a>' );
                    $activity_content = bp_create_excerpt( $post_text );
                    $primary_link = gf_get_topic_permalink($topic->topic_slug);

		//if ( $page )
			//$primary_link .= "?topic_page=" . $page;

		/* Record this in activity streams */
		gf_record_activity( array(
			'action' => apply_filters( 'gf_activity_new_forum_post_action', $activity_action, $post_id, $post_text, &$topic ),
			'content' => apply_filters( 'gf_activity_new_forum_post_content', $activity_content, $post_id, $post_text, &$topic ),
			'primary_link' => apply_filters( 'gf_activity_new_forum_post_primary_link', "{$primary_link}#post-{$post_id}" ),
			'type' => 'new_forum_post',
			'item_id' => $topic->topic_id,
			'secondary_item_id' => $post_id
		) );

                }
		do_action( 'gf_new_forum_topic_post',$post_id );

		return $post_id;
	}

	return false;
}
function gf_update_forum_post( $post_id, $post_text, $topic_id, $page = false ) {
	global $bp;

	$post_text = apply_filters( 'gf_forum_post_text_before_save', $post_text );
	$topic_id = apply_filters( 'gf_forum_post_topic_id_before_save', $topic_id );

	$post =gf_get_post( $post_id );

	if ( $post_id = gf_insert_post( array( 'post_id' => $post_id, 'post_text' => $post_text, 'post_time' => $post->post_time, 'topic_id' => $topic_id, 'poster_id' => $post->poster_id ) ) ) {

             if(gf_enabled_post_activity ()&&bp_is_active("activity")){
                        $topic = gf_get_topic_details( $topic_id );
                        $existing_activity_id=  BP_Activity_Activity::get_id($post->poster_id,$bp->gf->id,'new_forum_post',$topic->topic_id,$post_id,null,null,null);
                
                    $activity_action = sprintf( __( '%s posted on the forum topic %s in the forum %s:', 'gf'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . gf_get_topic_permalink($topic->topic_slug) .'/">' . attribute_escape( $topic->topic_title ) . '</a>', '<a href="' . gf_get_forum_permalink($topic->forum_id). '">' . attribute_escape(gf_get_forum_name($topic->forum_id) ) . '</a>' );
                    $activity_content = bp_create_excerpt( $post_text );
                    $primary_link = gf_get_topic_permalink($topic->topic_slug);

		//if ( $page )
			//$primary_link .= "?topic_page=" . $page;

		/* Record this in activity streams */
		gf_record_activity( array(
                        'id'=>$existing_activity_id,
			'action' => apply_filters( 'gf_activity_new_forum_post_action', $activity_action, $post_id, $post_text, &$topic ),
			'content' => apply_filters( 'gf_activity_new_forum_post_content', $activity_content, $post_id, $post_text, &$topic ),
			'primary_link' => apply_filters( 'gf_activity_new_forum_post_primary_link', "{$primary_link}#post-{$post_id}" ),
			'type' => 'new_forum_post',
			'item_id' => $topic_id,
			'secondary_item_id' => $post_id
		) );
             }
           

               
                
		do_action( 'gf_update_group_forum_post', &$post, &$topic );

		return $post_id;
	}

	return false;
}


function gf_delete_post( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'post_id' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	return bb_delete_post( $post_id, 1 );
}

function gf_insert_post( $args = '' ) {
	global $bp;

	do_action( 'bbpress_init' );

	$defaults = array(
		'post_id' => false,
		'topic_id' => false,
		'post_text' => '',
		'post_time' => date( 'Y-m-d H:i:s' ),
		'poster_id' => $bp->loggedin_user->id, // accepts ids or names
		'poster_ip' => $_SERVER['REMOTE_ADDR'],
		'post_status' => 0, // use bb_delete_post() instead
		'post_position' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( !$post = gf_get_post( $post_id ) )
		$post_id = false;

	if ( !isset( $topic_id ) )
		$topic_id = $post->topic_id;

	if ( empty( $post_text ) )
		$post_text = $post->post_text;

	if ( !isset( $post_time ) )
		$post_time = $post->post_time;

	if ( !isset( $post_position ) )
		$post_position = $post->post_position;

	$post = bb_insert_post( array( 'post_id' => $post_id, 'topic_id' => $topic_id, 'post_text' => stripslashes( trim( $post_text ) ), 'post_time' => $post_time, 'poster_id' => $poster_id, 'poster_ip' => $poster_ip, 'post_status' => $post_status, 'post_position' => $post_position ) );

	if ( $post )
		do_action( 'gf_forums_new_post', $post_id );

	return $post;
}

function gf_get_post_extras( $posts ) {
	global $bp, $wpdb;

	if ( empty( $posts ) )
		return $posts;

	/* Get the user ids */
	foreach ( (array)$posts as $post ) $user_ids[] = $post->poster_id;
	$user_ids = $wpdb->escape( join( ',', (array)$user_ids ) );

	/* Fetch the poster's user_email, user_nicename and user_login */
	$poster_details = $wpdb->get_results( $wpdb->prepare( "SELECT u.ID as user_id, u.user_login, u.user_nicename, u.user_email, u.display_name FROM {$wpdb->users} u WHERE u.ID IN ( {$user_ids} )" ) );

	for ( $i = 0; $i < count( $posts ); $i++ ) {
		foreach ( (array)$poster_details as $poster ) {
			if ( $poster->user_id == $posts[$i]->poster_id ) {
				$posts[$i]->poster_email = $poster->user_email;
				$posts[$i]->poster_login = $poster->user_nicename;
				$posts[$i]->poster_nicename = $poster->user_login;
				$posts[$i]->poster_name = $poster->display_name;
			}
		}
	}

	/* Fetch fullname for each poster. */
	if ( function_exists( 'xprofile_install' ) ) {
		$poster_names = $wpdb->get_results( $wpdb->prepare( "SELECT pd.user_id, pd.value FROM {$bp->profile->table_name_data} pd WHERE pd.user_id IN ( {$user_ids} )" ) );
		for ( $i = 0; $i < count( $posts ); $i++ ) {
			foreach ( (array)$poster_names as $name ) {
				if ( $name->user_id == $topics[$i]->user_id )
				$posts[$i]->poster_name = $poster->value;
			}
		}
	}

	return $posts;
}


function gf_delete_forum_post( $post_id, $topic_id ) {
	global $bp;

	if ( gf_delete_post( array( 'post_id' => $post_id ) ) ) {

                if(gf_enabled_post_activity ()&&function_exists("bp_activity_delete"))
                     bp_activity_delete(array("component"=>$bp->gf->id,"item_id"=>$topic_id, "secondary_item_id"=>$post_id,'type' => 'new_forum_post' ) );//delete all posts activity
		do_action( 'gf_delete_forum_post', $post_id, $topic_id );

		return true;
	}

	return false;
}

?>