<?php

//those are duplicated from BP

function bp_core_add_message( $message, $type = false ) {
	global $bp;

	if ( !$type )
		$type = 'success';

	/* Send the values to the cookie for page reload display */
	@setcookie( 'bp-message', $message, time()+60*60*24, COOKIEPATH );
	@setcookie( 'bp-message-type', $type, time()+60*60*24, COOKIEPATH );

	/***
	 * Send the values to the $bp global so we can still output messages
	 * without a page reload
	 */
	$bp->template_message = $message;
	$bp->template_message_type = $type;

}

function bp_core_setup_message() {
	global $bp;
	
	if ( empty( $bp->template_message ) )
		$bp->template_message = $_COOKIE['bp-message'];

	if ( empty( $bp->template_message_type ) )
		$bp->template_message_type = $_COOKIE['bp-message-type'];


	add_action( 'template_notices', 'bp_core_render_message' );

}
add_action( 'wp', 'bp_core_setup_message', 2 );

function bp_core_render_message() {
	global $bp;

	
	if ( $bp->template_message ) {
		$type = ( 'success' == $bp->template_message_type ) ? 'updated' : 'error';
	?>
		<div id="message" class="<?php echo $type; ?>">
			<p><?php echo stripslashes( attribute_escape( $bp->template_message ) ); ?></p>
		</div>
	<?php
		do_action( 'bp_core_render_message' );
	}
	
	@setcookie( 'bp-message', false, time() - 1000, COOKIEPATH );
	@setcookie( 'bp-message-type', false, time() - 1000, COOKIEPATH );
	
}

function bp_core_redirect( $location, $status = 302 ) {
	global $bp_no_status_set;

	// Make sure we don't call status_header() in bp_core_do_catch_uri()
    // as this conflicts with wp_redirect()
	$bp_no_status_set = true;

	wp_redirect( $location, $status );
	die;
}

?>