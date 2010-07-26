<?php
/* 
 * Topic template Tags
 * original work of @apeatling, modified by @sbrajesh
 * 
 */

class BP_GF_Forum_Topics_Template {
	var $current_topic = -1;
	var $topic_count;
	var $topics;
	var $topic;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_topic_count;

	var $single_topic = false;

	var $sort_by;
	var $order;

	function BP_GF_Forum_Topics_Template( $type, $forum_id, $user_id, $page, $per_page, $max, $no_stickies, $search_terms ) {
		global $bp;
               
		$this->pag_page = isset( $_REQUEST['p'] ) ? intval( $_REQUEST['p'] ) : $page;
		$this->pag_num = isset( $_REQUEST['n'] ) ? intval( $_REQUEST['n'] ) : $per_page;
		$this->type = $type;
		$this->search_terms = $search_terms;

		switch ( $type ) {
			case 'newest': default:
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'forum_id' => $forum_id, 'filter' => $search_terms, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;

			case 'popular':
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'type' => 'popular', 'filter' => $search_terms, 'forum_id' => $forum_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;

			case 'unreplied':
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'type' => 'unreplied', 'filter' => $search_terms, 'forum_id' => $forum_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;

			case 'tags':
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'type' => 'tags', 'filter' => $search_terms, 'forum_id' => $forum_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;
                        case 'my-topics':
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'type' => 'my-topics', 'filter' => $search_terms, 'forum_id' => $forum_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;
                        case 'favorites':
				$this->topics = gf_get_forum_topics( array( 'user_id' => $user_id, 'type' => 'favorites', 'filter' => $search_terms, 'forum_id' => $forum_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num, 'show_stickies' => $no_stickies ) );
				break;
		}

		$this->topics = apply_filters( 'gf_forums_template_topics', $this->topics, $type, $forum_id, $per_page, $max, $no_stickies );

		if ( !count($this->topics) ) {
			$this->topic_count = 0;
			$this->total_topic_count = 0;
		} else {
			if ( $forum_id &&$forum_id!=gf_get_root_forum_id()) {
				$topic_count = gf_get_forum( $forum_id );
				$topic_count = (int)$topic_count->topics;
			//} else if ( function_exists( 'gf_total_public_forum_topic_count' ) ) {
				//$topic_count = (int)gf_total_public_forum_topic_count( $type );
			} else {
				$topic_count = count( $this->topics );
			}

			if ( !$max || $max >= $topic_count )
                        $this->total_topic_count = $topic_count;
			else
				$this->total_topic_count = (int)$max;

			if ( $max ) {
				if ( $max >= count($this->topics) )
					$this->topic_count = count( $this->topics );
				else
					$this->topic_count = (int)$max;
			} else {
				$this->topic_count = count( $this->topics );
			}
		}
               
		$this->topic_count = apply_filters( 'gf_forums_template_topic_count', $this->topic_count, &$topics, $type, $forum_id, $per_page, $max, $no_stickies );
		$this->total_topic_count = apply_filters( 'gf_forums_template_total_topic_count', $this->total_topic_count, $this->topic_count, &$topics, $type, $forum_id, $per_page, $max, $no_stickies );

		if ( !$no_stickies) {
			/* Place stickies at the top  */
			foreach( (array)$this->topics as $topic ) {
				if ( 1 == (int)$topic->topic_sticky )
					$stickies[] = $topic;
				else
					$standard[] = $topic;
			}
			$this->topics = array_merge( (array)$stickies, (array)$standard );
		}
               
		/* Fetch extra information for topics, so we don't have to query inside the loop */
		//$this->topics = gf_get_topic_extras( &$this->topics );
                    
		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'p' => '%#%', 'n' => $this->pag_num ) ),
			'format' => '',
			'total' => ceil($this->total_topic_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
	}

	function has_topics() {
		if ( $this->topic_count )
			return true;

		return false;
	}

	function next_topic() {
		$this->current_topic++;
		$this->topic = $this->topics[$this->current_topic];
               
		return $this->topic;
	}

	function rewind_topics() {
		$this->current_topic = -1;
		if ( $this->topic_count > 0 ) {
			$this->topic = $this->topics[0];
		}
	}

	function user_topics() {
		if ( $this->current_topic + 1 < $this->topic_count ) {
			return true;
		} elseif ( $this->current_topic + 1 == $this->topic_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_topics();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_topic() {
		global $topic;

		$this->in_the_loop = true;
		$this->topic = $this->next_topic();
		$this->topic = (object)$this->topic;

		if ( $this->current_topic == 0 ) // loop has just started
			do_action('loop_start');
	}
}

function gf_has_forum_topics( $args = '' ) {
	global $gf_forum_topics_template, $bp;

	/***
	 * Set the defaults based on the current page. Any of these will be overridden
	 * if arguments are directly passed into the loop. Custom plugins should always
	 * pass their parameters directly to the loop.
	 */
	$type = 'newest';
	$user_id = false;
	$forum_id = gf_get_current_forum_id()?gf_get_current_forum_id():gf_get_root_forum_id();
	$search_terms = false;
	$no_stickies = false;

	
	if ( gf_is_front() && !empty( $_GET['gfs'] )&&$_GET['gfs']!='Search Forum...' )
		$search_terms = $_GET['gfs'];

	
	//if ( $bp->gf->current_forum )
	//	$no_stickies = null;

	$defaults = array(
		'type' => $type,
		'forum_id' => $forum_id,
		'user_id' => $user_id,
		'page' => 1,
		'per_page' => 20,
		'max' => false,
		'no_stickies' => $no_stickies,
		'search_terms' => $search_terms
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	/* If we're viewing a tag URL in the directory, let's override the type and set it to tags and the filter to the tag name */
	if ( 'tag' == $bp->current_action && !empty( $bp->action_variables[0] ) ) {
		$search_terms = $bp->action_variables[0];
		$type = 'tags';
	}

	$gf_forum_topics_template = new BP_GF_Forum_Topics_Template( $type, $forum_id, $user_id, $page, $per_page, $max, $no_stickies, $search_terms );
	return apply_filters( 'gf_has_topics', $gf_forum_topics_template->has_topics(), &$gf_forum_topics_template );
}

function gf_forum_topics() {
	global $gf_forum_topics_template;
	return $gf_forum_topics_template->user_topics();
}

function gf_the_forum_topic() {
	global $gf_forum_topics_template;
	return $gf_forum_topics_template->the_topic();
}

function gf_the_topic_id() {
	echo gf_get_the_topic_id();
}
	function gf_get_the_topic_id() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_id', $gf_forum_topics_template->topic->topic_id );
	}

function gf_the_topic_title() {
	echo gf_get_the_topic_title();
}
	function gf_get_the_topic_title($topic=null) {
		global $gf_forum_topics_template;
                if(!$topic)
                    $topic=$gf_forum_topics_template->topic;

		return apply_filters( 'gf_get_the_topic_title', stripslashes( $topic->topic_title ) );
	}

function gf_the_topic_slug() {
	echo gf_get_the_topic_slug();
}
	function gf_get_the_topic_slug($topic=null) {
		global $gf_forum_topics_template;
                if(!$topic)
                    $topic=$gf_forum_topics_template->topic;
		return apply_filters( 'gf_get_the_topic_slug', $topic->topic_slug );
	}

function gf_the_topic_text() {
	echo gf_get_the_topic_text();
}
	function gf_get_the_topic_text($topic=null) {
		global $gf_forum_topics_template;
                if(!$topic)
                    $topic=$gf_forum_topics_template->topic;
		$post = bb_get_first_post( (int)$topic->topic_id, false );
               
               	return apply_filters( 'gf_get_the_topic_text', $post->post_text );
	}

function gf_the_topic_poster_id() {
	echo gf_get_the_topic_poster_id();
}
	function gf_get_the_topic_poster_id($topic=null) {
		global $gf_forum_topics_template;
                if(!$topic)
                    $topic=$gf_forum_topics_template->topic;
		return apply_filters( 'gf_get_the_topic_poster_id', $topic->topic_poster );
	}

function gf_the_topic_poster_avatar( $args = '' ) {
	echo gf_get_the_topic_poster_avatar( $args );
}
	function gf_get_the_topic_poster_avatar( $args = '' ) {
		global $gf_forum_topics_template;

		$defaults = array(
			'type' => 'thumb',
			'width' => false,
			'height' => false,
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'gf_get_the_topic_poster_avatar', bp_core_fetch_avatar( array( 'item_id' => $gf_forum_topics_template->topic->topic_poster, 'type' => $type, 'width' => $width, 'height' => $height ) ) );
	}

function gf_the_topic_poster_name() {
	echo gf_get_the_topic_poster_name();
}
	function gf_get_the_topic_poster_name() {
		global $gf_forum_topics_template;

		$poster_id = ( empty( $gf_forum_topics_template->topic->poster_id ) ) ? $gf_forum_topics_template->topic->topic_poster : $gf_forum_topics_template->topic->poster_id;

		if ( !$name = bp_core_get_userlink( $poster_id ) )
			return __( 'Deleted User', 'gf' );

		return apply_filters( 'gf_get_the_topic_poster_name', $name );
	}


function gf_the_topic_last_poster_name() {
	echo gf_get_the_topic_last_poster_name();
}
	function gf_get_the_topic_last_poster_name() {
		global $gf_forum_topics_template;

		if ( !$domain = bp_core_get_user_domain( $gf_forum_topics_template->topic->topic_last_poster, $gf_forum_topics_template->topic->topic_last_poster_nicename, $gf_forum_topics_template->topic->topic_last_poster_login ) )
			return __( 'Deleted User', 'gf' );

		return apply_filters( 'gf_get_the_topic_last_poster_name', bp_core_get_userlink($gf_forum_topics_template->topic->topic_last_poster) );
	}

function gf_the_topic_last_poster_avatar( $args = '' ) {
	echo gf_get_the_topic_last_poster_avatar( $args );
}
	function gf_get_the_topic_last_poster_avatar( $args = '' ) {
		global $gf_forum_topics_template;

		$defaults = array(
			'type' => 'thumb',
			'width' => false,
			'height' => false,
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'gf_get_the_topic_last_poster_avatar', bp_core_fetch_avatar( array( 'email' => $gf_forum_topics_template->topic->topic_last_poster_email, 'item_id' => $gf_forum_topics_template->topic->topic_last_poster, 'type' => $type, 'width' => $width, 'height' => $height ) ) );
	}

function gf_the_topic_start_time() {
	echo gf_get_the_topic_start_time();
}
	function gf_get_the_topic_start_time() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_start_time', $gf_forum_topics_template->topic->topic_start_time );
	}

function gf_the_topic_time() {
	echo gf_get_the_topic_time();
}
	function gf_get_the_topic_time() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_time', $gf_forum_topics_template->topic->topic_time );
	}

function gf_the_topic_forum_id() {
	echo gf_get_the_topic_forum_id();
}
	function gf_get_the_topic_forum_id() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_forum_id', $gf_forum_topics_template->topic->topic_forum_id );
	}

function gf_the_topic_status() {
	echo gf_get_the_topic_status();
}
	function gf_get_the_topic_status() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_status', $gf_forum_topics_template->topic->topic_status );
	}
/**
 *
 * @desc Is it single topic view
 * @return <bool>
 */
function gf_is_topic(){
    global $bp;
    return $bp->gf->is_topic;
}

function gf_the_topic_is_topic_open() {
	echo gf_get_the_topic_is_topic_open();
}
	function gf_get_the_topic_is_topic_open() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_is_topic_open', $gf_forum_topics_template->topic->topic_open );
	}

function gf_the_topic_last_post_id() {
	echo gf_get_the_topic_last_post_id();
}
	function gf_get_the_topic_last_post_id() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_last_post_id', $gf_forum_topics_template->topic->topic_last_post_id );
	}

function gf_the_topic_is_sticky() {
	echo gf_get_the_topic_is_sticky();
}
	function gf_get_the_topic_is_sticky() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_is_sticky', $gf_forum_topics_template->topic->topic_sticky );
	}

function gf_the_topic_total_post_count() {
	echo gf_get_the_topic_total_post_count();
}
	function gf_get_the_topic_total_post_count() {
		global $gf_forum_topics_template;

		if ( $gf_forum_topics_template->topic->topic_posts == 1 )
			return apply_filters( 'gf_get_the_topic_total_post_count', sprintf( __( '%d post', 'gf' ), $gf_forum_topics_template->topic->topic_posts ) );
		else
			return apply_filters( 'gf_get_the_topic_total_post_count', sprintf( __( '%d posts', 'gf' ), $gf_forum_topics_template->topic->topic_posts ) );
	}

function gf_the_topic_total_posts() {
	echo gf_get_the_topic_total_posts();
}
	function gf_get_the_topic_total_posts() {
		global $gf_forum_topics_template;

		return $gf_forum_topics_template->topic->topic_posts;
	}

function gf_the_topic_tag_count() {
	echo gf_get_the_topic_tag_count();
}
	function gf_get_the_topic_tag_count() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_tag_count', $gf_forum_topics_template->topic->tag_count );
	}
function gf_the_topic_tags(){
    global $gf_forum_topics_template;
    gf_list_tags(array('topic'=>$gf_forum_topics_template->topic->topic_id));
}


//get the permalink of topic
//bbpress do not provide a function to get slug from topic, so we assume it is slug
function gf_get_topic_permalink($topic_slug){
    $url=gf_get_home_url()."/topic/".$topic_slug;
     return apply_filters("gf_topic_permalink",$url);

}

function gf_the_topic_permalink() {
	echo gf_get_the_topic_permalink();
}
	function gf_get_the_topic_permalink($id=null) {
		global $gf_forum_topics_template, $bp;
                if(!$id)
                    $topic=$gf_forum_topics_template->topic;
                else
                    $topic=get_topic($id);
    	$permalink = gf_get_home_url();


		return apply_filters( 'gf_get_the_topic_permalink', $permalink . '/topic/' . $topic->topic_slug . '/' );
	}

function gf_the_topic_time_since_created() {
	echo gf_get_the_topic_time_since_created();
}
	function gf_get_the_topic_time_since_created() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_time_since_created', bp_core_time_since( strtotime( $gf_forum_topics_template->topic->topic_start_time ) ) );
	}

function gf_the_topic_latest_post_excerpt( $args = '' ) {
	echo gf_get_the_topic_latest_post_excerpt( $args );
}
	function gf_get_the_topic_latest_post_excerpt( $args = '' ) {
		global $gf_forum_topics_template;

		$defaults = array(
			'length' => 10
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$post = gf_get_post( $gf_forum_topics_template->topic->topic_last_post_id );
		$post = bp_create_excerpt( $post->post_text, $length );
		return apply_filters( 'gf_get_the_topic_latest_post_excerpt', $post );
	}

function gf_the_topic_time_since_last_post() {
	global $gf_forum_topics_template;

	echo gf_get_the_topic_time_since_last_post();
}
	function gf_get_the_topic_time_since_last_post() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_the_topic_time_since_last_post', bp_core_time_since( strtotime( $gf_forum_topics_template->topic->topic_time ) ) );
	}

function gf_the_topic_is_mine() {
	echo gf_get_the_topic_is_mine();
}
	function gf_get_the_topic_is_mine() {
		global $bp, $gf_forum_topics_template;

		return $bp->loggedin_user->id == $gf_forum_topics_template->topic->topic_poster;
	}

function gf_the_topic_admin_links( $args = '' ) {
	echo gf_get_the_topic_admin_links( $args );
}
	function gf_get_the_topic_admin_links( $args = '' ) {
		global $bp, $gf_forum_topics_template;

		$defaults = array(
			'seperator' => '|'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$links[] = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'edit', 'gf_forums_edit_topic' ) . '">' . __( 'Edit Topic', 'gf' ) . '</a>';

		if ( $bp->is_item_admin || $bp->is_item_mod || is_site_admin() ) {
			if ( 0 == (int)$gf_forum_topics_template->topic->topic_sticky )
				$links[] = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'stick', 'gf_forums_stick_topic' ) . '">' . __( 'Sticky Topic', 'gf' ) . '</a>';
			else
				$links[] = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'unstick', 'gf_forums_unstick_topic' ) . '">' . __( 'Un-stick Topic', 'gf' ) . '</a>';

			if ( 0 == (int)$gf_forum_topics_template->topic->topic_open )
				$links[] = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'open', 'gf_forums_open_topic' ) . '">' . __( 'Open Topic', 'gf' ) . '</a>';
			else
				$links[] = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'close', 'gf_forums_close_topic' ) . '">' . __( 'Close Topic', 'gf' ) . '</a>';

			$links[] = '<a class="confirm" id="topic-delete-link" href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'delete', 'gf_delete_topic' ) . '">' . __( 'Delete Topic', 'gf' ) . '</a>';
		}

		return implode( ' ' . $seperator . ' ', (array) $links );
	}

function gf_the_topic_css_class() {
	echo gf_get_the_topic_css_class();
}

	function gf_get_the_topic_css_class() {
		global $gf_forum_topics_template;

		$class = false;

		if ( $gf_forum_topics_template->current_topic % 2 == 1 )
			$class .= 'alt';

		if ( 1 == (int)$gf_forum_topics_template->topic->topic_sticky )
			$class .= ' sticky';

		if ( 0 == (int)$gf_forum_topics_template->topic->topic_open )
			$class .= ' closed';

		return trim( $class );
	}

function gf_my_forum_topics_link() {
	echo gf_get_my_forum_topics_link();
}
	function gf_get_my_forum_topics_link() {
		global $bp;

		return apply_filters( 'gf_get_my_forum_topics_link', gf_get_home_url(). '/personal/' );
	}

function gf_unreplied_forum_topics_link() {
	echo gf_get_unreplied_forum_topics_link();
}
	function gf_get_unreplied_forum_topics_link() {
		global $bp;

		return apply_filters( 'gf_get_unreplied_forum_topics_link', gf_get_home_url() . '/unreplied/' );
	}


function gf_popular_forum_topics_link() {
	echo gf_get_popular_forum_topics_link();
}
	function gf_get_popular_forum_topics_link() {
		global $bp;

		return apply_filters( 'gf_get_popular_forum_topics_link', gf_get_home_url() . '/popular/' );
	}

function gf_newest_forum_topics_link() {
	echo gf_get_newest_forum_topics_link();
}
	function gf_get_newest_forum_topics_link() {
		global $bp;

		return apply_filters( 'gf_get_newest_forum_topics_link', gf_get_home_url() . '/' );
	}
//check
function gf_forum_topic_type() {
	echo gf_get_forum_topic_type();
}
	function gf_get_forum_topic_type() {
		global $bp;

		if ( !$bp->is_directory || !$bp->current_action )
			return 'newest';


		return apply_filters( 'gf_get_forum_topic_type', $bp->current_action );
	}


function gf_forums_tag_name() {
	echo gf_get_forums_tag_name();
}
	function gf_get_forums_tag_name() {
		global $bp;

		if ( $bp->is_directory && $bp->forums->slug == $bp->current_component )
			return apply_filters( 'gf_get_forums_tag_name', $bp->action_variables[0] );
	}

function gf_forum_pagination() {
	echo gf_get_forum_pagination();
}
	function gf_get_forum_pagination() {
		global $gf_forum_topics_template;

		return apply_filters( 'gf_get_forum_pagination', $gf_forum_topics_template->pag_links );
	}

function gf_forum_pagination_count() {
	global $bp, $gf_forum_topics_template;

	$from_num = bp_core_number_format( intval( ( $gf_forum_topics_template->pag_page - 1 ) * $gf_forum_topics_template->pag_num ) + 1 );
	$to_num = bp_core_number_format( ( $from_num + ( $gf_forum_topics_template->pag_num - 1  ) > $gf_forum_topics_template->total_topic_count ) ? $gf_forum_topics_template->total_topic_count : $from_num + ( $gf_forum_topics_template->pag_num - 1 ) );
	$total = bp_core_number_format( $gf_forum_topics_template->total_topic_count );

	$pag_filter = false;
	if ( 'tags' == $gf_forum_topics_template->type && !empty( $gf_forum_topics_template->search_terms ) )
		$pag_filter = sprintf( __( ' matching tag "%s"', 'gf' ), $gf_forum_topics_template->search_terms );

	echo apply_filters( 'gf_forum_pagination_count', sprintf( __( 'Viewing topic %s to %s (%s total topics%s)', 'gf' ), $from_num, $to_num, $total, $pag_filter ) );
?>
<span class="ajax-loader"></span>
<?php
}




//other tags
function gf_is_edit_topic() {
	global $bp;

	if ( in_array( 'post', (array)$bp->action_variables ) && in_array( 'edit', (array)$bp->action_variables ) )
		return false;

	return true;
}
function gf_is_forum_topic_edit() {
	global $bp;
        $topic_slug = $bp->action_variables[0];
        $topic_id = gf_get_topic_id_from_slug( $topic_slug );
	if ( GF_SLUG== $bp->current_component &&$topic_id && 'topic' == $bp->current_action && 'edit' == $bp->action_variables[1] )
		return true;

	return false;
}

function gf_forum_topic_count_for_user( $user_id = false ) {
	echo gf_get_forum_topic_count_for_user( $user_id );
}
	function gf_get_forum_topic_count_for_user( $user_id = false ) {
		return apply_filters( 'gf_get_forum_topic_count_for_user', gf_forums_total_topic_count_for_user( $user_id ) );
	}

 function gf_get_topic_title($topic_id=null){
     global $bp;
    if(!$topic_id&&!gf_is_topic())
        return'';
    if(!$topic_id)
        $topic_id=gf_get_topic_id_from_slug ($bp->action_variables[0]);
    $topic=gf_get_topic_details($topic_id);
    return $topic->topic_title;
 }
?>