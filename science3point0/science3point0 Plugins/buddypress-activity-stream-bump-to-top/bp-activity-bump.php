<?php

function bp_activity_bump_comment_posted( $comment_id, $params ) {
	global $bp, $wpdb;

	extract( $params, EXTR_SKIP );

	$activity_parent = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

	//make sure we have something
	if ( !$activity_parent = $activity_parent['activities'][0] )
		return;

	//Come and see the violence inherent in the system. Help! Help! I'm being repressed!
	if ( bp_activity_bump_denied_activity_type_check( $activity_parent->type ) )
		return;

	//be nice and save the date_recorded
	if ( !bp_activity_get_meta( $activity_id, 'bp_activity_bump_date_recorded') )
		bp_activity_update_meta( $activity_id, 'bp_activity_bump_date_recorded', $activity_parent->date_recorded );

	$activity = new BP_Activity_Activity( $activity_id );
	$activity->date_recorded = gmdate( "Y-m-d H:i:s" );
	if ( !$activity->save() )
		return false;
}
add_action( 'bp_activity_comment_posted', 'bp_activity_bump_comment_posted', 1, 2 );


function bp_activity_bump_time_since( $content, $activity ) {
	global $bp;

	if ( !$bumpdate = bp_activity_get_meta( $activity->id, 'bp_activity_bump_date_recorded') )
		return $content;
	
	$content = '<span class="time-since">' . sprintf( __( '&nbsp; updated %s ago', 'bp-activity-bump' ), bp_core_time_since( $activity->date_recorded ) ) . '</span>';

	return apply_filters( 'bp_activity_bump_time_since', '<span class="time-since time-created">' . sprintf( __( '&nbsp; %s ago', 'buddypress' ), bp_core_time_since( $bumpdate ) ) . '</span> &middot; ' . $content, $activity->date_recorded, $bumpdate, $content );
	
}
add_filter( 'bp_activity_time_since', 'bp_activity_bump_time_since', 1, 2 );


function bp_activity_bump_denied_activity_type_check( $type ) {

	$types = (array) maybe_unserialize( get_option( 'bp_activity_bump_denied_activity_types') );

	return in_array( $type, apply_filters( 'bp_activity_bump_denied_activity_types', $types ) );
}

?>