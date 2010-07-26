<?php
/* 
 * Tag manipulation
 * 
 */

function gf_get_the_topic_tags($topic_id=null){
    global $gf_forum_topics_template;

    if(!$topic_id)
        $topic_id=$gf_forum_topics_template->topic->topic_id;
return bb_get_topic_tags($topic_id);
}

/*topic tag support*/

function gf_list_tags( $args = null )
{
	$defaults = array(
		'tags' => false,
		'format' => 'list',
		'topic' => 0,
		'list_id' => 'tags-list'
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if ( !$topic = get_topic( get_topic_id( $topic ) ) ) {
		return false;
	}

	if ( !is_array( $tags ) ) {
		$tags = bb_get_topic_tags( $topic->topic_id );
	}

	if ( !$tags ) {
		return false;
	}

	$list_id = esc_attr( $list_id );

	$r = '';
	switch ( strtolower( $format ) ) {
		case 'table' :
			break;

		case 'list' :
		default :
			$args['format'] = 'list';
			$r .= '<ul id="' . $list_id . '" class="tags-list list:tag">' . "\n";
			foreach ( $tags as $tag ) {
				$r .= _gf_list_tag_item( $tag, $args );
			}
			$r .= '</ul>';
			break;
	}

	echo $r;
}
function gf_get_tag_link($tag){

	return apply_filters( 'gf_get_tag_link', gf_get_home_url()."/tag/".$tag->slug);

}
function _gf_list_tag_item( $tag, $args )
{
	$url = esc_url( gf_get_tag_link( $tag ) );
	$name = esc_html( bb_get_tag_name( $tag ) );
	if ( 'list' == $args['format'] ) {
		$id = 'tag-' . $tag->tag_id . '_' . $tag->user_id;
		return "\t" . '<li id="' . $id . '"' . get_alt_class( 'topic-tags' ) . '><a href="' . $url . '" rel="tag">' . $name . '</a> ' . gf_get_tag_remove_link( array( 'tag' => $tag, 'list_id' => $args['list_id'],'topic'=>$args['topic'] ) ) . '</li>' . "\n";
	}
}

function gf_get_tag_remove_link( $args = null ) {
	if ( is_scalar($args) || is_object( $args ) )
		$args = array( 'tag' => $args );
	$defaults = array( 'tag' => 0, 'topic' => 0, 'list_id' => 'tags-list' );
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	if ( is_object( $tag ) && isset( $tag->tag_id ) ); // [sic]
	elseif ( !$tag = bb_get_tag( bb_get_tag_id( $tag ) ) )
		return false;

	if ( !$topic = get_topic( get_topic_id( $topic ) ) )
		return false;

        global $bp;
	if ( !is_user_logged_in()||gf_is_user_banned($bp->loggedin_user->id) )
		return false;

	$url =gf_get_topic_permalink($topic->topic_slug) ."/tags-remove/".$tag->tag_id;
	$url = esc_url( wp_nonce_url( $url, 'remove-tag_' . $tag->tag_id . '|' . $topic->topic_id) );
	$title = esc_attr__( 'Remove this tag' );
	$list_id = esc_attr( $list_id );
	return "[<a href='$url' class='delete:$list_id:tag-{$tag->tag_id}_{$tag->user_id}' title='$title'>&times;</a>]";
}

?>