<?php
/*
 * Plugin Name: Global Forum
 * Description: Create global forums with buddypress/bbpress
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh
 * Plugin URI: http://buddydev.com/buddypress/global-forums-plugin-for-buddypress
 * License: GPL
 * Version:1.0.1
 * Network: true
 * Date Updated: 14th July 2010
 */

$global_forum_dir =str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
define("GF_DIR_NAME",$global_forum_dir);//the directory name of availability Checker
define("GF_PLUGIN_DIR",WP_PLUGIN_DIR."/".GF_DIR_NAME);
define("GF_PLUGIN_URL",WP_PLUGIN_URL."/".GF_DIR_NAME);
define("GF_PLUGIN_NAME","gf");//for loading text domains
if(!defined("GF_SLUG"))
    define("GF_SLUG","siteforums");
if(!defined("GF_LINK_TITLE"))
   define("GF_LINK_TITLE","Global Forums");

include_once (GF_PLUGIN_DIR."gf-loader.php");
//on activation
function gf_install(){

//check if the parent forum exists, if not create one
$gf_id=gf_get_root_forum_id();//get the root forum id
if(empty($gf_id)&&gf_forums_is_installed_correctly()){
    //check if the forum is setup correctly or not, if not,add a notice message
    do_action( 'bbpress_init' );
    $forum_id= bb_new_forum(array("forum_name"=>__("Global Forum","gf")));
    update_site_option("global_forums",$forum_id);
}

}
add_action("bp_init","gf_install",100);

/*setup the root component for accesing via sitename.com/globalforum*/
function bp_gf_forums_setup_root_component() {
	/* Register 'siteforums' as a root component */
	bp_core_add_root_component(GF_SLUG );
}
add_action( 'bp_setup_root_components', 'bp_gf_forums_setup_root_component',2 );

/**
 * @desc get the root forum id for global forum
 * @return <int> root forum id
 */
function gf_get_root_forum_id(){
     return apply_filters("gf_get_root_forum_id",get_site_option("global_forums"));
}
function gf_is_mycomponent(){
    global $bp;
    if($bp->current_component==$bp->gf->slug)
            return true;
    return false;
}
//undo group mis behaving
add_action("init","gf_remove_group_forum_trouble");
function gf_remove_group_forum_trouble(){/*fix global forum topic appearing on group forums topic list*/
if(gf_is_mycomponent())
    remove_filter( 'bbpress_init', 'groups_add_forum_privacy_sql' ); //remove the group causing issue with the forum listing

}
?>