<?php
/*
Plugin Name: One Quick Post
Plugin URI: http://dev.pellicule.org/?page_id=19
Description: Allows the users to write posts from the frontend.  Guest posting can be enabled; and it's compatible with BuddyPress.
Version: 0.2.0-alpha
Revision Date: July 20, 2010
Requires at least: Wordpress 3
Tested up to: Wordpress 3
License: (GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: G.Breant
Author URI: http://dev.pellicule.org
Site Wide Only: true
*/

//TO DO : default options

/* Define a slug constant that will be used to view this components pages (http://example.org/SLUG) */
if ( !defined( 'ONEQUICKPOST_SLUG' ) )
	define ( 'ONEQUICKPOST_SLUG', __( 'quick-post', 'oqp-slugs' ));
define ( 'ONEQUICKPOST_IS_INSTALLED', 1 );
define ( 'ONEQUICKPOST_VERSION', '0.2.0' );
define ( 'ONEQUICKPOST_PLUGIN_NAME', 'one-quick-post' );
define ( 'ONEQUICKPOST_PLUGIN_DIR', dirname( __FILE__ ) );
define ( 'ONEQUICKPOST_PLUGIN_URL', WP_PLUGIN_URL . '/' . ONEQUICKPOST_PLUGIN_NAME );

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function bp_oqp_init() {
	
	require ONEQUICKPOST_PLUGIN_DIR.'/buddypress/includes/admin-settings-bp.php';
	
	$options = get_option('oqp_options');
	
	if ($options['buddypress']) //load it only if option checked
		require ONEQUICKPOST_PLUGIN_DIR.'/buddypress/one-quick-post-bp-core.php';
}
if ( defined( 'BP_VERSION' ) || did_action( 'bp_init' ) )
	bp_oqp_init();
else
	add_action( 'bp_init', 'bp_oqp_init' );

/* If you have code that does not need BuddyPress to run, then add it here. */
require ONEQUICKPOST_PLUGIN_DIR.'/includes/one-quick-post-core.php';




?>