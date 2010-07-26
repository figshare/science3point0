<?php
/* 
 * Topic Posts Template/forum posts template tags, taken from bp-forums, original work by @apeatling, modified by @sbrajesh;
 * 
 */

class BP_GF_Topic_Posts_Template {
	var $current_post = -1;
	var $post_count;
	var $posts;
	var $post;

	var $topic_id;
	var $topic;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_post_count;

	var $single_post = false;

	var $sort_by;
	var $order;

	function BP_GF_Topic_Posts_Template( $topic_id, $per_page, $max ) {
		global $bp, $current_user, $gf_forum_topics_template;

		$this->pag_page = isset( $_REQUEST['topic_page'] ) ? intval( $_REQUEST['topic_page'] ) : 1;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;

		$this->topic_id = $topic_id;
		$gf_forum_topics_template->topic = (object) gf_get_topic_details( $this->topic_id );
              
		$this->posts = gf_get_topic_posts( array( 'topic_id' => $this->topic_id, 'page' => $this->pag_page, 'per_page' => $this->pag_num ) );

		if ( !$this->posts ) {
			$this->post_count = 0;
			$this->total_post_count = 0;
		} else {
			if ( !$max || $max >= (int) $gf_forum_topics_template->topic->topic_posts )
				$this->total_post_count = (int) $gf_forum_topics_template->topic->topic_posts;
			else
				$this->total_post_count = (int)$max;

			if ( $max ) {
				if ( $max >= count($this->posts) )
					$this->post_count = count( $this->posts );
				else
					$this->post_count = (int)$max;
			} else {
				$this->post_count = count( $this->posts );
			}
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'topic_page' => '%#%', 'num' => $this->pag_num ) ),
			'format' => '',
			'total' => ceil($this->total_post_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
		$this->pag->total_pages = ceil($this->total_post_count / $this->pag_num);
               
	}

	function has_posts() {
		if ( $this->post_count )
			return true;

		return false;
	}

	function next_post() {
		$this->current_post++;
		$this->post = $this->posts[$this->current_post];

		return $this->post;
	}

	function rewind_posts() {
		$this->current_post = -1;
		if ( $this->post_count > 0 ) {
			$this->post = $this->posts[0];
		}
	}

	function user_posts() {
		if ( $this->current_post + 1 < $this->post_count ) {
			return true;
		} elseif ( $this->current_post + 1 == $this->post_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_posts();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_post() {
		global $post;

		$this->in_the_loop = true;
		$this->post = $this->next_post();
		$this->post = (object)$this->post;

		if ( $this->current_post == 0 ) // loop has just started
			do_action('loop_start');
	}
}

function gf_has_topic_posts( $args = '' ) {
	global $gf_topic_posts_template, $bp;

	$defaults = array(
		'topic_id' => false,
		'per_page' => 15,
		'max' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( !$topic_id && $bp->current_component == $bp->gf->slug && 'topic' == $bp->current_action  )
		$topic_id = gf_get_topic_id_from_slug( $bp->action_variables[0] );

	if ( is_numeric( $topic_id ) )
		$gf_topic_posts_template = new BP_GF_Topic_Posts_Template( $topic_id, $per_page, $max );
	else
		return false;

	return apply_filters( 'gf_has_topic_posts', $gf_topic_posts_template->has_posts(), &$gf_topic_posts_template );
}

function gf_topic_posts() {
	global $gf_topic_posts_template;
	return $gf_topic_posts_template->user_posts();
}

function gf_the_topic_post() {
	global $gf_topic_posts_template;
	return $gf_topic_posts_template->the_post();
}

function gf_the_topic_post_id() {
	echo gf_get_the_topic_post_id();
}
	function gf_get_the_topic_post_id() {
		global $gf_topic_posts_template;

		return apply_filters( 'gf_get_the_topic_post_id', $gf_topic_posts_template->post->post_id );
	}

function gf_the_topic_post_content() {
	echo gf_get_the_topic_post_content();
}
	function gf_get_the_topic_post_content($post=null) {
		global $gf_topic_posts_template;
                if(!$post)
                    $post=$gf_topic_posts_template->post;
                return apply_filters( 'gf_get_the_topic_post_content', stripslashes( $post->post_text ),$post->post_id );
	}

function gf_the_topic_post_poster_avatar( $args = '' ) {
	echo gf_get_the_topic_post_poster_avatar( $args );
}
	function gf_get_the_topic_post_poster_avatar( $args = '' ) {
		global $gf_topic_posts_template;

		$defaults = array(
			'type' => 'thumb',
			'width' => 20,
			'height' => 20,
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		return apply_filters( 'gf_get_the_topic_post_poster_avatar', bp_core_fetch_avatar( array( 'item_id' => $gf_topic_posts_template->post->poster_id, 'type' => $type, 'width' => $width, 'height' => $height ) ) );
	}

function gf_the_topic_post_poster_name() {
	echo gf_get_the_topic_post_poster_name();
}
	function gf_get_the_topic_post_poster_name() {
		global $gf_topic_posts_template;

		if ( !$link = bp_core_get_user_domain( $gf_topic_posts_template->post->poster_id, $gf_topic_posts_template->post->poster_nicename, $gf_topic_posts_template->post->poster_login ) )
			return __( 'Deleted User', 'gf' );

		return apply_filters( 'gf_get_the_topic_post_poster_name', '<a href="' . $link . '" title="' . $gf_topic_posts_template->post->poster_name . '">' . $gf_topic_posts_template->post->poster_name . '</a>' );
	}

function gf_the_topic_post_poster_link() {
	echo gf_get_the_topic_post_poster_link();
}
	function gf_get_the_topic_post_poster_link($post=null) {
		global $gf_topic_posts_template;
                if(!$post)
                    $post=$gf_topic_posts_template->post;
		return apply_filters( 'gf_the_topic_post_poster_link', bp_core_get_user_domain( $post->poster_id, $post->poster_nicename, $post->poster_login ) );
	}

function gf_the_topic_post_time_since() {
	echo gf_get_the_topic_post_time_since();
}
	function gf_get_the_topic_post_time_since($post=null) {
		global $gf_topic_posts_template;
                if(!$post)
                    $post=$gf_topic_posts_template->post;

		return apply_filters( 'gf_get_the_topic_post_time_since', bp_core_time_since( strtotime( $post->post_time ) ) );
	}

function gf_the_topic_post_is_mine() {
	echo gf_the_topic_post_is_mine();
}
	function gf_get_the_topic_post_is_mine($post=null) {
		global $bp, $gf_topic_posts_template;
                if(!$post)
                    $post=$gf_topic_posts_template->post;
		return $bp->loggedin_user->id == $post->poster_id;
	}

function gf_the_topic_post_admin_links( $args = '' ) {
	echo gf_get_the_topic_post_admin_links( $args );
}
	function gf_get_the_topic_post_admin_links( $args = '' ) {
		global $gf_topic_posts_template;

		// Never show for the first post in a topic.
		if ( 0 == $gf_topic_posts_template->current_post && $gf_topic_posts_template->pag_page == 1 )
			return;

		$defaults = array(
			'seperator' => '|'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( $_SERVER['QUERY_STRING'] )
			$query_vars = '?' . $_SERVER['QUERY_STRING'];

		$links  = '<a href="' . wp_nonce_url( gf_get_the_topic_permalink() . $gf_topic_posts_template->post->id . 'edit/post/' . $gf_topic_posts_template->post->post_id . '/' . $query_vars, 'gf_forums_edit_post' ) . '">' . __( 'Edit', 'gf' ) . '</a> ' . $seperator . ' ';
		$links .= '<a class="confirm" id="post-delete-link" href="' . wp_nonce_url( gf_get_the_topic_permalink() . 'delete/post/' . $gf_topic_posts_template->post->post_id, 'gf_delete_post' ) . '">' . __( 'Delete', 'gf' ) . '</a> | ';

		return $links;
	}

function gf_the_topic_post_edit_text() {
	echo gf_get_the_topic_post_edit_text();
}
	function gf_get_the_topic_post_edit_text() {
		global $bp;

		$post = gf_get_post( $bp->action_variables[3] );
		return attribute_escape( $post->post_text );
	}

function gf_the_topic_pagination() {
	echo gf_get_the_topic_pagination();
}
	function gf_get_the_topic_pagination() {
		global $gf_topic_posts_template;

		return apply_filters( 'gf_get_the_topic_pagination', $gf_topic_posts_template->pag_links );
	}

function gf_the_topic_pagination_count() {
	global $bp, $gf_topic_posts_template;

	$from_num = intval( ( $gf_topic_posts_template->pag_page - 1 ) * $gf_topic_posts_template->pag_num ) + 1;
	$to_num = ( $from_num + ( $gf_topic_posts_template->pag_num - 1  ) > $gf_topic_posts_template->total_post_count ) ? $gf_topic_posts_template->total_post_count : $from_num + ( $gf_topic_posts_template->pag_num - 1 );

	echo apply_filters( 'gf_the_topic_pagination_count', sprintf( __( 'Viewing post %d to %d (%d total posts)', 'gf' ), $from_num, $to_num, $gf_topic_posts_template->total_post_count ) );
?>
	<span class="ajax-loader"></span>
<?php
}

function gf_the_topic_is_last_page() {
	echo gf_get_the_topic_is_last_page();
}
	function gf_get_the_topic_is_last_page() {
		global $gf_topic_posts_template;

		return apply_filters( 'gf_get_the_topic_is_last_page', $gf_topic_posts_template->pag_page == $gf_topic_posts_template->pag->total_pages );
	}


        

function gf_is_post_edit(){
    global $bp;
    return $bp->gf->is_post_edit;
}

function gf_get_the_post_permalink($post=null){
    
    global $gf_topic_posts_template;
    if(!$post)
        $post=$gf_topic_posts_template->post;
   
    $permalink=gf_get_the_topic_permalink($post->topic_id);
    $post_permalink=$permalink."#post-".$post->post_id;
    return $post_permalink;
}
?>