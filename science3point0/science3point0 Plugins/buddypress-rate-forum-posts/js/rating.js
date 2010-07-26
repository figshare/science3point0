// rate posts jquery, called from the user rating click
function rfp_rate_js( post_id, direction, rater ) {
	if ( post_id != '' ) {
		jQuery( '#rfp-rate-'+post_id+' .counter' ).text( '...' );
		
		jQuery.post( blogUrl + "/wp-content/plugins/buddypress-rate-forum-posts/rate.php",
			{ post_id: post_id, direction: direction, rater: rater },
			function( data ){	
				datasplit = data.split( '|' );
				jQuery( '#rfp-rate-'+post_id+' .counter' ).text( datasplit[0] ); // the new (or old) rating
				jQuery( '#rfp-rate-'+post_id+' i' ).show().text( datasplit[1] ).animate({opacity:1},2000).fadeOut('slow'); //status message
		});
	}
}


// if a post is hidden, add a 'click to show' link
jQuery(document).ready( function() {
	jQuery( '.rfp-hide' ).append( '<div class="rfp-show">Click to show this hidden item</div>' ).click( function() {
		jQuery( this ).removeClass( 'rfp-hide' );
		jQuery( '.rfp-show', this ).hide();	  // using a nice way to select children of this
	});
});


// alter posts based on ratings
// this could be done in php, however the required hooks did not exist - and this way it works with more themes
// uses json to fetch an associative array of 
//THIS CODE IS DEPRECEIATED IN BP VERSION 1.2.4 AND POST RATING PLUGIN VERSION 1.4
jQuery(document).ready( function() {

	// skip this if the buddypress version is 1.2.4
	if ( typeof(rfp_alter_posts_legacy) != "undefined" ) {

		// fetch array of all topic posts in the form post_id => css_class_name
		jQuery.getJSON( blogUrl + "/wp-content/plugins/buddypress-rate-forum-posts/rate.php", { topic_id: topic_id }, function( json ){
			
			if (json) { // make sure its not zero
	
				// cycle through each post
				jQuery.each( json, function( post_id, rfp_class ){	
		
					jQuery( '#post-'+post_id ).addClass( rfp_class ); // apply the css class
					
					// if hidden, show a link to display content. if link clicked, show posts and hide link.
					//if ( rfp_class == 'rfp-hide' ) { 
					//	jQuery( '#post-'+post_id ).append( '<div class="rfp-show">Click to show this hidden post</div>' ).click( function() {
					//		jQuery( this ).removeClass( rfp_class );
					//		jQuery( '#post-'+post_id+' .rfp-show' ).hide();	
					//	});
					//}
		
					
				});
			}
	
		});
	 
	}
});
