<?php
/* 
 * Forums template tags
 */
/* Most of the code here is taken from bbpress core files and sometimes slightly modified to fit the need*/
/**
 *
 * @global <type> $bp
 * @global <type> $gf_forums_loop
 * @global <type> $gf_current_forum
 * @param <type> $args
 * @return <type>
 * @desc The loop for global forums
 */
function gf_has_forums($args=''){
    global $bp,$gf_forums_loop,$gf_current_forum;
     $fid=$bp->gf->current_forum->forum_id?$bp->gf->current_forum->forum_id:gf_get_root_forum_id();
    // hierarchical not used here.  Sent to bb_get_forums for proper ordering.
    $default= array('child_of'=>$fid,
                    'hierarchical' => true,
                    'type' => $default_type,
                    'walker' => 'BB_Walker_Blank');
    
        $args = wp_parse_args( $args, $default);
        $levels = array( '', '' );
        if ( in_array($args['type'], array('list', 'ul')) )
		$levels = array( '<ul>', '</ul>' );

           $forums = bb_get_forums( $args );

            if ( !class_exists($args['walker']) )
                	$args['walker'] = 'BB_Walker_Blank';

	if ( $gf_forums_loop = BB_Loop::start( $forums, $args['walker'] ) ) {
		$gf_forums_loop->preserve( array('forum', 'forum_id') );
		$gf_forums_loop->walker->db_fields = array( 'id' => 'forum_id', 'parent' => 'forum_parent' );
		list($gf_forums_loop->walker->start_lvl, $gf_forums_loop->walker->end_lvl) = $levels;
		return $gf_forums_loop->elements;
	}
	$false = false;
	return $false;
}
/**
 *
 * @global <type> $gf_forums_loop
 * @global <type> $gf_current_forum
 * @return <type>
 */
function gf_forum(){
global $gf_forums_loop, $gf_current_forum;

    if ( !is_object($gf_forums_loop) || !is_a($gf_forums_loop, 'BB_Loop') )
		return false;
	if ( !is_array($gf_forums_loop->elements) )
		return false;

	if ( $gf_forums_loop->step() ) {
		$gf_current_forum = $gf_forums_loop->elements[key($gf_forums_loop->elements)]; // Globalize the current forum object
	} else {
		$gf_forums_loop->reinstate();
		return $gf_forums_loop = null; // All done?  Kill the object and exit the loop.
	}
	return $gf_forums_loop->walker->depth;
}
//forum object
//[forum_id] => 1
//[forum_name] => Default Forum
//[forum_slug] => default-forum
//[forum_desc] =>
//[forum_parent] => 0
//[forum_order] => 1
// [topics] => 0
// [posts] => 0

/**
 *
 * @global <type> $gf_current_forum
 * @desc return the class names for proper indentation of forums/sub forums
 */
function gf_forum_class(){
        global $gf_current_forum;
        echo gf_get_forum_class($gf_current_forum->forum_id);

}
    function gf_get_forum_class($forum_id){
        $args=array('id'=>$forum_id);
        return apply_filters( 'bb_forum_class', get_alt_class( 'forum',gf_get_forum_class_names( $args ) ), $args );

    }
    function gf_get_forum_class_names($args){
          if ( is_numeric( $args ) ) { // Not used
                        $args = array( 'id' => $args );
                } elseif ( $args && is_string( $args ) && false === strpos( $args, '=' ) ) {
                        $args = array( 'class' => $args );
                }
                $defaults = array( 'id' => 0, 'key' => 'forum', 'class' => '', 'output' => 'string' );
                $args = wp_parse_args( $args, $defaults );

                $classes = array();
                if ( $args['class'] ) {
                        $classes[] = $args['class'];
                }

                global $gf_forums_loop;
                if ( is_object( $gf_forums_loop ) && is_a( $gf_forums_loop, 'BB_Loop' ) ) {
                        $classes = array_merge( $classes, $gf_forums_loop->classes( 'array' ) );
                }

                if ( $args['output'] === 'string' ) {
                        $classes = join( ' ', $classes );
                }

                return apply_filters( 'bb_get_forum_class', $classes, $args );

}
/**
 *
 * @global <type> $gf_current_forum
 * @desc prints forum name
 */
function gf_forum_name(){
    global $gf_current_forum;

   echo gf_get_forum_name( $gf_current_forum->forum_id);
       

   }

   function gf_get_forum_name($forum_id){
        $forum = bb_get_forum( get_forum_id($forum_id ) );
	return apply_filters( 'gf_get_forum_name', $forum->forum_name, $forum->forum_id );
   }
function gf_get_forum_slug($forum_id){
      $forum = bb_get_forum( get_forum_id($forum_id ) );
	return apply_filters( 'gf_get_forum_slug', $forum->forum_slug, $forum->forum_id );
}
/**
 *
 * @global <type> $gf_current_forum
 * @desc prints forum description
 */
function gf_forum_description(){
    global $gf_current_forum;
     echo gf_get_forum_description($gf_current_forum->forum_id);

   }
/**
 *
 * @param <type> $forum_id
 * @return <type>
 * @desc return the current forum description
 */
    function gf_get_forum_description( $forum_id = 0 ) {
            $forum = bb_get_forum( get_forum_id( $forum_id ) );
            return apply_filters( 'get_forum_description', $forum->forum_desc, $forum->forum_id );
    }

function gf_forum_pad($pad){

    global $gf_forums_loop;
	if ( !is_object($gf_forums_loop) || !is_a($gf_forums_loop, 'BB_Loop') )
		return false;

	echo $gf_forums_loop->pad( $pad, $offset );
}

function gf_forum_permalink(){
   global $gf_current_forum;
   if(!$gf_current_forum&&gf_get_current_topic()){
    //get the forum from topic
     $topic=gf_get_current_topic();
   $forum_id=$topic->forum_id;

   }
   else
       $forum_id=$gf_current_forum->forum_id;
        echo gf_get_forum_permalink($forum_id);
}
/*
*/

/** dealing withthe link of forums*/
/**
 * @desc  the home of forum e.g site.com/myforum
 * @return <type>
 */
function gf_get_home_url(){
   return apply_filters("gf_home_url",bp_get_root_domain()."/".GF_SLUG);
}

/**
 *
 * @param <type> $forum_id
 * @return <type>
 */
function gf_get_forum_permalink($forum_id){
   $forum=bb_get_forum($forum_id);
    if($parent=get_forum_parent($forum_id))
          return gf_get_forum_permalink($parent)."/".$forum->forum_slug;


   return gf_get_home_url()."/".$forum->forum_slug;
}
/*
 * Get forum id from forum slug
 */
function gf_get_forum_id_from_slug($slug){
    do_action( 'bbpress_init' );
        return bb_get_id_from_slug( 'forum', $slug );
}

/*stats Stats Stats*/
function gf_get_forum_topic_count($forum_id){
    return get_forum_topics( $forum_id );
}
function gf_get_forum_posts_count($forum_id){
    return get_forum_posts($forum_id);
}

?>