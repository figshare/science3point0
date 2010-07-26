<?php

function oqp_notification_edit_message($post,$blog_id=false) {
	$message = sprintf(__('To edit the post visit: %s.','oqp'),oqp_post_get_edit_link($post->ID,false,$blog_id));
	
	if (($post->post_status!='published') && (!oqp_user_can_for_blog('edit_published_posts',$post->post_author,$oqp_blog_id)))
		$message.='  '.__('You will not be able to edit it once published','oqp');
		
	return $message;
}

function oqp_notification_delete_message($post,$blog_id=false) {
	$message = sprintf(__('To delete it visit: %s.','oqp'),oqp_post_get_delete_link($post->ID,false,$blog_id));
	
	if (($post->post_status!='published') && (!oqp_user_can_for_blog('delete_published_posts',$post->post_author,$oqp_blog_id)))
		$message.='  '.__('You will not be able to delete it once published','oqp');
		
	return $message;
}


function oqp_notification_post_pending($post) {
	global $blog_id;

	$user_info = get_userdata($post->post_author);
	
	//TO FIX
	//POSTING ON ANOTHER BLOG : 
	//we don't get the right options as we don't get them from the good blog (we have switched).
	
	$options = get_option('oqp_options');

	// Set up and send the message
	$to = apply_filters( 'oqp_notification_post_pending_to', $user_info->user_email,$post );
	
	if (!$to) return false;
	
	if ((!$options['send_mails']) && (!oqp_user_is_dummy($post->post_author))) return false;

	$blog_name = get_bloginfo( 'name' );

	//TO FIX CHECK BLOG NAME
	$subject = '[' . $blog_name . '] ' . sprintf( __( 'Your post: "%s" is now awaiting moderation', 'oqp' ), $post->post_title );

	$message = sprintf( __(
	'Your post "%1s" has been saved on our website %2s and is now awaiting moderation.

	%3s
	%4s

	---------------------
	', 'oqp' ), $post->post_title, $blog_name,oqp_notification_edit_message($post,$blog_id),oqp_notification_delete_message($post,$blog_id));
	
	/* Send the message */
	$subject = apply_filters( 'oqp_notification_post_pending_subject', $subject, &$post );
	$message = apply_filters( 'oqp_notification_post_pending_message', $message, &$post);

	wp_mail( $to, $subject, $message );

}


function oqp_notification_post_approved($post) {
	global $blog_id;

	$user_info = get_userdata($post->post_author);
	
	//TO FIX
	//POSTING ON ANOTHER BLOG : 
	//we don't get the right options as we don't get them from the good blog (we have switched).
	
	$options = get_option('oqp_options');

	// Set up and send the message
	$to = apply_filters( 'oqp_notification_post_approved_to', $user_info->user_email,$post );
	
	if (!$to) return false;
	
	//if ((!$options['send_mails']) && (!oqp_user_is_dummy($post->post_author))) return false;
	
	$blog_name = get_bloginfo( 'name' );
	$post_link = oqp_post_get_link($post->ID);

	//TO FIX CHECK BLOG NAME
	$subject = '[' . $blog_name . '] ' . sprintf( __( 'Your post: "%s" has been published', 'oqp' ), $post->post_title );

	$message = sprintf( __(
	'Your post "%1s" has been approved and is now published on our website %2s.

	To view the post visit: %3s
	%4s
	%5s

	---------------------
	', 'oqp' ), $post->post_title, $blog_name, $post_link,oqp_notification_edit_message($post,$blog_id),oqp_notification_delete_message($post,$blog_id));
	
	/* Send the message */
	$subject = apply_filters( 'oqp_notification_post_approved_subject', $subject, &$post );
	$message = apply_filters( 'oqp_notification_post_approved_message', $message, &$post);

	wp_mail( $to, $subject, $message );

}

function oqp_notification_post_deleted($post) {

	$user_info = get_userdata($post->post_author);
	
	//TO FIX
	//POSTING ON ANOTHER BLOG : 
	//we don't get the right options as we don't get them from the good blog (we have switched).
	
	$options = get_option('oqp_options');

	// Set up and send the message
	$to = apply_filters( 'oqp_notification_post_deleted_to', $user_info->user_email,$post );
	
	if (!$to) return false;
	if ((!$options['send_mails']) && (!oqp_user_is_dummy($post->post_author))) return false;

	$blog_name = get_bloginfo( 'name' );

	//TO FIX CHECK BLOG NAME
	$subject = '[' . $blog_name . '] ' . sprintf( __( 'Your post: "%s" has been deleted', 'oqp' ), $post->post_title );

	$message = sprintf( __(
	'Your post "%1s" has been trashed on our website %2s.

	---------------------
	', 'oqp' ), $post->post_title, $blog_name);
	
	/* Send the message */
	$subject = apply_filters( 'oqp_notification_post_deleted_subject', $subject, &$post );
	$message = apply_filters( 'oqp_notification_post_deleted_message', $message, &$post);

	wp_mail( $to, $subject, $message );

}



add_filter('oqp_notification_post_pending_to','oqp_notification_mail_to',10,2);
add_filter('oqp_notification_post_approved_to','oqp_notification_mail_to',10,2);
add_filter('oqp_notification_post_deleted_to','oqp_notification_mail_to',10,2);

//if the poster is a guest; filter unique link
function oqp_post_link($link,$post) {
	
	if (!oqp_user_is_dummy($post->post_author)) return $link;
	
	//TO FIX
	$post_key = get_post_meta($post->ID,'oqp_guest_key', true);
	return $post->guid.'?key='.$post_key;
}

add_filter('oqp_post_link','oqp_post_link',10,2);

?>