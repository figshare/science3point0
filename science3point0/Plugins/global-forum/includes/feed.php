<?php
/**
 * Feed suuport for global forum
 */
//will change
function gf_get_posts_rss_link($forum_id=null) {
       //global recent posts
	$link = gf_get_home_url().'/rss/posts';
        if($forum_id){
            $link.="/forum/".gf_get_forum_slug($forum_id);
        }
	return apply_filters( 'gf_get_posts_rss_link', $link);
}

//will add support for individual forum later
function gf_get_topics_rss_link($forum_id=null) {
            $link = gf_get_home_url().'/rss/topics';//global forum topics rss feed
	 if($forum_id){
            $link.="/forum/".gf_get_forum_slug($forum_id);
        }
	return apply_filters( 'gf_get_topics_rss_link', $link);
}
function gf_get_latest_topics($args=null){
    //get_latest_topics($args);
    return gf_get_forum_topics($args);
}
function gf_get_latest_posts($limit,$forum_id=null,$page=1){
   	$limit = (int) $limit;
       $exclude=gf_get_excluded_forums();
        if(!empty ($exclude))
                    $exclude=implode(',', $exclude);
        $hidden_forums_list=$exclude;
        
        if ( $exclude ) {
		$exclude = '-' . str_replace(',', '-,', $exclude);
		$exclude = str_replace('--', '-', $exclude);
		if ( $forum_id )
			$forum_id = (string) $forum_id . ",$exclude";
		else
			$forum_id = $exclude;
	}
	$post_query = new BB_Query( 'post', array( 'forum_id'=>$forum_id,'page' => $page, 'per_page' => $limit ), 'get_latest_posts' );
	return $post_query->results;

}
function gf_is_posts_feed(){
    global $bp;
    if($bp->current_component==$bp->gf->slug&&$bp->current_action=="rss"&&$bp->action_variables[0]=="posts")
            return true;
    return false;

}
function gf_is_feed(){
   global $bp;
    if($bp->current_component==$bp->gf->slug&&$bp->current_action=="rss")
            return true;
    return false;

}
//feed generation
function gf_generate_feed(){
     global $bp,$title,$link,$description,$posts,$topics,$topic;
    do_action("bbpress_init");

    if(gf_is_posts_feed()) {
         $feed="all-posts";
        if(!empty($bp->action_variables[1])&&$bp->action_variables[1]=="forum"&&!empty($bp->action_variables[2]))
           $feed="forum-posts";
   }
    else{ //we assume to be feed
        $feed="all-topics";
        if(!empty($bp->action_variables[1])&&$bp->action_variables[1]=="forum"&&!empty($bp->action_variables[2]))
           $feed="forum-topics";
    }
// Get the posts and the title for the given feed
    //support for individualf forum feed and indiavidual topic feed coming in 1.1

    switch ($feed) {
		
		
		// Get just the first post from the latest topics
		case 'all-topics':
                  $topics = gf_get_latest_topics();
                       if ( !$topics )
				die();

			$posts = array();
			foreach ($topics as $topic) {
				$posts[] = bb_get_first_post($topic->topic_id);
			}

			$title = esc_html( sprintf( __( '%1$s - Recent Topics','gf' ), get_bloginfo( 'name' ) ) );
			$link = gf_get_home_url();
			$link_self = gf_get_topics_rss_link();
			break;
                case 'forum-topics'://for specific forum feed
                $forum_slug=$bp->action_variables[2];
                $forum_id=gf_get_forum_id_from_slug($forum_slug);
                if(empty($forum_id))
                    die();
                
                $topics = gf_get_latest_topics(array("forum_id"=>$forum_id));
                       if ( !$topics )
				die();

			$posts = array();
			foreach ($topics as $topic) {
				$posts[] = bb_get_first_post($topic->topic_id);
			}

			$title = esc_html( sprintf( __( '%1$s - Recent Topics','gf' ), get_bloginfo( 'name' )."-".  gf_get_forum_name($forum_id) ) );
			$link = gf_get_forum_permalink($forum_id);
			$link_self = gf_get_topics_rss_link($forum_id);
			break;

                case 'forum-posts': //for specific forum
                    $forum_slug=$bp->action_variables[2];
                    $forum_id=gf_get_forum_id_from_slug($forum_slug);
                    if(empty($forum_id))
                        die();
                     if ( !$posts = gf_get_latest_posts(20,$forum_id) )
				die();
                                    
                      
			$title = esc_html( sprintf( __( '%1$s - Recent Posts','gf' ), get_bloginfo( 'name' )."-".gf_get_forum_name($forum_id) ) );
			$link = gf_get_forum_permalink($forum_id);
			$link_self = gf_get_posts_rss_link($forum_id);
                    break;
		
                // Get latest posts by default

		case 'all-posts':
		default:
			if ( !$posts = gf_get_latest_posts(20) )
				die();
			$title = esc_html( sprintf( __( '%1$s - Recent Posts','gf' ), get_bloginfo( 'name' ) ) );
			$link = gf_get_home_url();
			$link_self = gf_get_posts_rss_link();
			break;
	}

//bb_send_304( $posts[0]->post_time );

if (!$description = esc_html(get_bloginfo('description') )) {
	$description = $title;
}

$title = apply_filters( 'gf_title_rss', $title, $feed );
$description = apply_filters( 'gf_description_rss', $description, $feed );
$posts = apply_filters( 'gf_posts_rss', $posts, $feed );
$link_self = apply_filters( 'gf_link_self_rss', $link_self, $feed );

}
//add feed to head

function gf_feed_head() {
if(!gf_is_front())
    return;
	$feeds = array();

	$feeds[] = array(
				'title' => sprintf(__('%1$s &raquo; %2$s &raquo; Recent Posts','gf'), get_bloginfo( 'name' ),GF_LINK_TITLE),
                		'href'  => gf_get_posts_rss_link()
			);
	$feeds[] = array(
				'title' => sprintf(__('%1$s &raquo; %2$s &raquo; Recent Topics'), get_bloginfo( 'name' ),GF_LINK_TITLE),
				'href'  => gf_get_topics_rss_link()
			);
	if(gf_is_topic ()||  gf_is_forum()){
          //find forum id
            $forum_id=gf_get_current_forum_id()?gf_get_current_forum_id():gf_get_topic_parent_forum_id();
            //$forum_id=gf_get_current_forum_id();
            //add forum specific feeds too
            $feeds[] = array(
				'title' => sprintf(__('%1$s &raquo; %2$s &raquo; %3$s &raquo; Recent Posts','gf'), get_bloginfo( 'name' ),GF_LINK_TITLE,  gf_get_forum_name($forum_id)),
                		'href'  => gf_get_posts_rss_link($forum_id)
			);
	$feeds[] = array(
				'title' => sprintf(__('%1$s &raquo; %2$s &raquo; %3$s &raquo; Recent Topics'), get_bloginfo( 'name' ),GF_LINK_TITLE,  gf_get_forum_name($forum_id)),
				'href'  => gf_get_topics_rss_link($forum_id)
			);
        }

	

	if (count($feeds)) {
		$feed_links = array();
		foreach ($feeds as $feed) {
			$link = '<link rel="alternate" type="application/rss+xml" ';
			$link .= 'title="' . esc_attr($feed['title']) . '" ';
			$link .= 'href="' . esc_attr($feed['href']) . '" />';
			$feed_links[] = $link;
		}
		$feed_links = join("\n", $feed_links);
	} else {
		$feed_links = '';
	}

	echo apply_filters('gf_feed_head', $feed_links);
}
add_action("wp_head","gf_feed_head");
?>