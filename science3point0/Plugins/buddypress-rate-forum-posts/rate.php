<?php
require_once '../../../wp-config.php';
require_once '../buddypress/bp-loader.php';
require_once $bp->site_options[ 'bb-config-location' ];
require_once 'rate.inc.php';

$bb_posts_table = $bb_table_prefix . 'posts';
$bb_meta_table = $bb_table_prefix . 'meta';

// this for testing; to see results of rate.php in the browser
// ie. {your-domain}/wp-content/plugins/buddypress-rate-forum-posts/rate.php?post_id=1&direction=pos&rater=1&test=1
//if ( $_GET['test'] )
//	$_POST = $_GET;

// a rating click happens - return a new value to js function rfp_rate_js()
if( $post_id = $_POST['post_id'] ) {
	$direction = $_POST['direction'];
	$rater = $_POST['rater'];
	
	$user_rated = rfp_get_user_rated_post( $rater, $post_id, $direction );
	
	if ( !$user_rated ) {
		$meta_value = rfp_update_post_rating( $post_id, $direction );
				
		if ( $meta_value > 0 ) echo '+' . $meta_value;
		elseif ( $meta_value < 0 ) echo '-' . $meta_value;
		else echo '0';
		
		echo '|Thank you';
		rfp_update_user_rating_history( $rater, $post_id, $direction );
		rfp_update_post_author_karma( $post_id, $direction );
		
	} else {
		echo rfp_get_post_rating_signed( $post_id ) . '|' . $user_rated;
	}
}


// alter the look of the page by instering classes depending on rating. called from rating.js
// returns an array of post_id => class name for all posts in the topic
// change the values below in rate.inc.php
//THIS CODE IS DEPRECEIATED IN BP VERSION 1.2.4 AND POST RATING PLUGIN VERSION 1.4 see bp-rate-forum-posts.php for new code
if ( $topic = $_GET['topic_id'] ) {
	global $forum_template;
	$topic_posts = bp_forums_get_topic_posts( array( 'topic_id' => $topic,  'page' => $forum_template->pag_page, 'per_page' => $forum_template->pag_num ) );

	foreach ( $topic_posts as $post ) {
		$post_id = $post->post_id;
		$post_rating = rfp_get_post_rating( $post_id );
		if ( $post_rating == NULL ) 
			continue;
		else if ( $post_rating >= $rfp->superboost && $rfp->superboost != 0 ) 
			$id_ratings[$post_id] = 'rfp-superboost';
		elseif ( $post_rating >= $rfp->boost && $rfp->boost != 0 ) 
			$id_ratings[$post_id] = 'rfp-boost';
		elseif ( $post_rating <= $rfp->hide  && $rfp->hide != 0 ) 
			$id_ratings[$post_id] = 'rfp-hide';
		elseif ( $post_rating <= $rfp->diminish && $rfp->diminish != 0 ) 
			$id_ratings[$post_id] = 'rfp-diminish';
	}

	echo json_encode( $id_ratings );
}

?>