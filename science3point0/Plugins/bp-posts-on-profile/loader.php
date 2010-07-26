<?php
/*
Plugin Name: BP Posts on Profile
Plugin URI: http://nxsn.com/my-projects/bp-posts-on-profile/
Description: Adds 'Posts' link to member's profile page, and shows member's blog posts on that page.
Version: 1.0
Tested up to: 1.2.3
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Huseyin Berberoglu
Author URI: http://nxsn.com
Site Wide Only: true
*/

function bp_postsonprofile_init() {
	require( dirname( __FILE__ ) . '/includes/bp-postsonprofile-core.php' );
}
add_action( 'bp_init', 'bp_postsonprofile_init' );

?>
