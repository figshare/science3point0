<?php
/*
Plugin Name: Seo for Buddypress
Plugin URI: http://sven-lehnert.de
Description: Seo for Wordpress, Wordpress MU and Buddypress
Author: Sven Lehnert, Sven Wagener
Author URI: http://sven-lehnert.de
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 1.2.4 beta
Text Domain: bp_seo
Site Wide Only: false
*/
//
// Released under the GPL license
// http://www.gnu.org/licenses/gpl.txt
//
// This is an add-on for WordPress Single, MU and Buddypress
// http://wordpress.org/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

global $blog_id, $meta;

## Admin pages
include("functions.inc.php");
include("meta.inc.php");
include("special-tags.inc.php");

## Admin pages
include("admin/general.inc.php");
include("admin/plugins.inc.php");
include("admin/main.inc.php");
include("admin/single-metabox.php");
include("admin/settings.inc.php");

function bp_seo_init() {
	global $blog_id;
	
	if(defined('SITE_ID_CURRENT_SITE')){
	    if($blog_id != SITE_ID_CURRENT_SITE){
	      	add_action('wp_head', 'bp_seo_meta',1);
	      	add_filter('wp_title', 'bp_seo_mu',0);
	    } else {
	      	add_action('wp_head', 'bp_seo_meta',1);
	      	add_filter('bp_page_title', 'bp_seo',0);
	    }
	} else {
   	add_action('wp_head', 'bp_seo_meta',1);
	 	add_filter('bp_page_title', 'bp_seo',0);
  }
	
  	add_action('admin_head', 'sfb_css');  
  	add_action('admin_menu', 'bp_seo_admin_menu');
  	add_action('edit_form_advanced', 'seo4all_metabox');
  	add_action('edit_page_form', 'seo4all_metabox');
  	add_action('save_post','post_seo4all_title');
  	add_action('save_post','post_seo4all_description');
  	add_action('save_post','post_seo4all_keywords');
  }

  if(defined('BP_VERSION')){
	 add_action('bp_init','bp_seo_init');	
  } else {
  	if(defined('SITE_ID_CURRENT_SITE')){
    	if($blog_id != SITE_ID_CURRENT_SITE){
      	add_action('wp_head','bp_seo_meta',1);
      	add_filter('wp_title','bp_seo_mu',0);
    	}else{
      	add_action('wp_head','bp_seo_meta',1);
      	add_filter('wp_title','wp_seo',0);
    	}
    } else {
      	add_action('wp_head','bp_seo_meta',1);
      	add_filter('wp_title','wp_seo',0);
    }
  	add_action('admin_head','sfb_css');  
  	add_action('admin_menu','bp_seo_admin_menu');
  	add_action('edit_form_advanced','seo4all_metabox');
  	add_action('edit_page_form','seo4all_metabox');
  	add_action('save_post','post_seo4all_title');
  	add_action('save_post','post_seo4all_description');
  	add_action('save_post','post_seo4all_keywords');
}

$plugin_dir = basename(dirname(__FILE__))."/lang/";
load_plugin_textdomain( 'seo-for-buddypress', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

### creates the menu item
function bp_seo_admin_menu() {
	global $blog_id;
	if(!current_user_can('level_10')){ 
		return false;
	} else {
  	if(defined('SITE_ID_CURRENT_SITE')){	
  		if($blog_id != SITE_ID_CURRENT_SITE){
    		return false;
   		}
   	}
  }
  	
	if(defined('BP_VERSION')){
		add_submenu_page( 'bp-general-settings', __( 'Seo for Buddypress', 'bp-seo'), '<strong>' . __( 'Seo for Buddypress', 'bp-seo' ) . '</strong>', 'manage_options', 'bp_seo_main_page', 'bp_seo_main_page' );
		add_submenu_page( 'bp-general-settings', __( 'General Seo', 'bp-seo'), '<span class="bp-seo-admin-menue">&middot; ' . __( 'General Seo', 'buddypress' ) . '</span>', 'manage_options', 'bp_seo_general_page', 'bp_seo_general_page' );
		add_submenu_page( 'bp-general-settings', __( 'Plugins Seo', 'bp-seo'), '<span class="bp-seo-admin-menue">&middot; ' . __( 'Plugins Seo', 'buddypress' ) . '</span>', 'manage_options', 'bp_seo_plugins', 'bp_seo_plugins_page' );
		add_submenu_page( 'bp-general-settings', __( 'Settings', 'bp-seo'), '<span class="bp-seo-admin-menue">&middot; ' . __( 'Settings', 'buddypress' ) . '</span>', 'manage_options', 'bp_seo_settings', 'bp_seo_settings' );
	} else {
		add_menu_page(__('SEO'),__('SEO'), 'manage_options', 'seomenue', 'bp_seo_main_page');
		add_submenu_page( 'seomenue', __( 'General Seo', 'seo-for-buddypress'),__( 'General Seo', 'buddypress' ), 'manage_options', 'bp_seo_general_page', 'bp_seo_general_page' );
		add_submenu_page( 'seomenue', __( 'Settings', 'seo-for-buddypress'), __( 'Settings', 'buddypress' ), 'manage_options', 'bp_seo_settings', 'bp_seo_settings' );
	
	}
}

### add css and js for the option page
function sfb_css() {
	echo '
	   <link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/seo-for-buddypress/css/tabcontent.css" type="text/css" media="screen" />
	   <script type="text/javascript" src="' . get_option('siteurl') . '/wp-content/plugins/seo-for-buddypress/js/tabcontent.js">
      /***********************************************
      * Tab Content script v2.2- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
      * This notice MUST stay intact for legal use
      * Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
      ***********************************************/
      
    </script>

	';
}