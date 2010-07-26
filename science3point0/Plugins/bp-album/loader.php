<?php
/*
Plugin Name: BuddyPress Album+
Plugin URI: http://flweb.it/buddypress-album-plus/
Description: Photo Albums for BuddyPress. Includes Posts to Wire, Member Comments, and Photo Privacy Controls. Works with current BuddyPress theme and includes Easy To Skin Templates.
Version: 0.1.7
Revision Date: March 28, 2010
Requires at least: WP 2.9.2, BP 1.2.1
Tested up to: WP 2.9.2, BP 1.2.3
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Francesco Laffi & Carl Roett
Author URI: http://flweb.it/
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_album_init() {

	/* Define a constant that will hold the current version number of the component */
	define ( 'BP_ALBUM_VERSION', '0.1.7' );
	
	require( dirname( __FILE__ ) . '/includes/bp-album-core.php' );
	
	do_action('bp_album_init');
}
add_action( 'bp_init', 'bp_album_init' );


// Moved this function to loader.php file because this is the standard place for it
// and it will get very large once we add database upgrade code to migrate plugin versions
function bp_album_install(){
	global $bp,$wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";


    $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_album (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            owner_type varchar(10) NOT NULL,
	            owner_id bigint(20) NOT NULL,
	            date_uploaded datetime NOT NULL,
	            title varchar(250) NOT NULL,
	            description longtext NOT NULL,
	            privacy tinyint(2) NOT NULL default '0',
	            pic_org_url varchar(250) NOT NULL,
	            pic_org_path varchar(250) NOT NULL,
	            pic_mid_url varchar(250) NOT NULL,
	            pic_mid_path varchar(250) NOT NULL,
	            pic_thumb_url varchar(250) NOT NULL,
	            pic_thumb_path varchar(250) NOT NULL,
	            KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/upgrade-functions.php' );

	dbDelta($sql);

	update_site_option( 'bp-album-db-version', BP_ALBUM_DB_VERSION  );

        // Write default options to the WP database if they do not exist,
        // but do not overwrite options if the user has set them. Using
        // update_site_option() because it puts data in a top-level WP
        // table so it is easy to debug.

        if (!get_site_option( 'bp_album_slug' ))
            update_site_option( 'bp_album_slug', 'album');

        if (!get_site_option( 'bp_album_max_pictures' ))
            update_site_option( 'bp_album_max_pictures', false);

        if (!get_site_option( 'bp_album_max_priv0_pictures' ))
            update_site_option( 'bp_album_max_priv0_pictures', false);

        if (!get_site_option( 'bp_album_max_priv2_pictures' ))
            update_site_option( 'bp_album_max_priv2_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv4_pictures' ))
            update_site_option( 'bp_album_max_priv4_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv6_pictures' ))
            update_site_option( 'bp_album_max_priv6_pictures', false);

        if(!get_site_option( 'bp_album_keep_original' ))
            update_site_option( 'bp_album_keep_original', true);
        
        if(!get_site_option( 'bp_album_require_description' ))
            update_site_option( 'bp_album_require_description', false);

        if(!get_site_option( 'bp_album_enable_comments' ))
            update_site_option( 'bp_album_enable_comments', true);

        if(!get_site_option( 'bp_album_enable_wire' ))
            update_site_option( 'bp_album_enable_wire', true);

        if(!get_site_option( 'bp_album_middle_size' ))
            update_site_option( 'bp_album_middle_size', 600);

        if(!get_site_option( 'bp_album_thumb_size' ))
            update_site_option( 'bp_album_thumb_size', 150);
        
        if(!get_site_option( 'bp_album_per_page' ))
            update_site_option( 'bp_album_per_page', 20 );

}

register_activation_hook( __FILE__, 'bp_album_install' );


function bp_album_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('install_plugins') )
		return;

	if (!defined('BP_VERSION') || version_compare(BP_VERSION, '1.2','<')){
		add_action('admin_notices', 'bp_album_compatibility_notices' );
		return;
	}

	if ( get_site_option( 'bp-album-db-version' ) < BP_ALBUM_DB_VERSION )
		bp_album_install();
}
add_action( 'admin_menu', 'bp_album_check_installed' );

function bp_album_compatibility_notices() {
	$message = 'BuddyPress Album+ needs at least BuddyPress 1.2 to work.';
	if (!defined('BP_VERSION')){
		$message .= ' Please install Buddypress';
	}elseif(version_compare(BP_VERSION, '1.2','<') ){
		$message .= ' Your current version is '.BP_VERSION.' please updrade.';
	}
	echo '<div class="error fade"><p>'.$message.'</p></div>';
}


function bp_album_activate() {
	bp_album_check_installed();

	do_action( 'bp_album_activate' );
}
register_activation_hook( __FILE__, 'bp_album_activate' );

function bp_album_deactivate() {
	do_action( 'bp_album_deactivate' );
}
register_deactivation_hook( __FILE__, 'bp_album_deactivate' );

?>