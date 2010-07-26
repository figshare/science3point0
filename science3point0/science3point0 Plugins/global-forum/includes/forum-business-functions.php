<?php
/* 
 * Forum Business functions
 * 
 */
/**
 * @desc get the forum object from forum id
 * @param <int>forum_id
 * @return $forum object
 */
function gf_get_forum( $forum_id ) {
	do_action( 'bbpress_init' );
	return bb_get_forum( $forum_id );
}

/**
 *
 * @global <type> $bp
 * @return <type>
 * Get current forum id
 */
function gf_get_current_forum_id(){
global $bp;
    return $bp->gf->current_forum->forum_id;

}
function gf_get_excluded_forums(){
 //return the list of group forum ids;
  global $bp,$wpdb;
        $to_exclude='';
 if(bp_is_active("groups")){
          $to_exclude=$wpdb->get_col($wpdb->prepare("select meta_value as forums from {$bp->groups->table_name_groupmeta} where meta_key='forum_id'"));
         }
      return apply_filters("gf_excluded_forums",$to_exclude);
}


?>