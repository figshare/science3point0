<?php

//loader
function gf_loader(){
if ( !defined('BB_PATH' ) )
	require ( GF_PLUGIN_DIR . 'includes/bbpress-bridge.php' );

//error_reporting(E_ALL);

include_once (GF_PLUGIN_DIR."includes/forum-business-functions.php");
include_once (GF_PLUGIN_DIR."includes/topic-business-functions.php");
include_once (GF_PLUGIN_DIR."includes/posts-business-functions.php");
include_once (GF_PLUGIN_DIR."includes/admin-business-functions.php");
include_once (GF_PLUGIN_DIR."includes/stats.php");
include_once (GF_PLUGIN_DIR."includes/tags.php");
include_once (GF_PLUGIN_DIR."includes/filters.php");
include_once (GF_PLUGIN_DIR."includes/feed.php");
include_once (GF_PLUGIN_DIR."includes/favourites.php");
include_once (GF_PLUGIN_DIR."forums-template-tags.php");
include_once (GF_PLUGIN_DIR."general-template-tags.php");
include_once (GF_PLUGIN_DIR."topic-template-tags.php");
include_once (GF_PLUGIN_DIR."posts-template-tags.php");
include_once (GF_PLUGIN_DIR."admin-template-tags.php");

require_once ( GF_PLUGIN_DIR . 'includes/cssjs.php' );

}

//forum filters
/*setup*/

add_action("bp_init","gf_loader");//bbpress loader

/**
 * @desc Localization support
 * Put your files into
 * global-forum/languages/gf-your_local.mo
 */
function gf_load_textdomain() {
        $locale = apply_filters( 'gf_load_textdomain_get_locale', get_locale() );
	// if load .mo file
	if ( !empty( $locale ) ) {
		$mofile_default = sprintf( '%s/languages/%s-%s.mo', GF_PLUGIN_DIR, GF_PLUGIN_NAME, $locale );
		$mofile = apply_filters( 'gf_load_textdomain_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( GF_PLUGIN_NAME, $mofile );
		}
	}
}
add_action ( 'bp_init', 'gf_load_textdomain', 2 );

function gf_forums_setup() {
	global $bp;

        /* For internal identification */
	$bp->gf->id = 'gf';
	$bp->gf->image_base = BP_PLUGIN_URL . '/bp-forums/images';//let BP keep the things
	$bp->gf->bbconfig = $bp->site_options['bb-config-location'];
	$bp->gf->slug = GF_SLUG;
        /* Register this in the active components array */
	$bp->active_components[$bp->gf->slug] = $bp->gf->id;
	do_action( 'bp_gf_forums_setup' );
}
add_action( 'bp_init', 'gf_forums_setup',6 );




/* most important function, handles forum home/new forum/admin/subforum/single/forum/forum topics*/
function gf_forums_directory_forums_setup() {
	global $bp;
        //make bp-awarae
        if(!function_exists("bp_core_get_user_domain"))
          return;
        //find the view and init the forums
      
	if ( $bp->current_component == $bp->gf->slug ) {
             do_action( 'bbpress_init' );
		
		if ( !gf_forums_is_installed_correctly() ) {
			bp_core_add_message( __( 'The forums component has not been set up yet.', 'gf' ), 'error' );
			bp_core_redirect( bp_get_root_domain() );
		}

                $current_action=$bp->current_action;
               
                if(!empty($current_action)){
                  
                    if($current_action=="topic"&&!empty($bp->action_variables)){
                          //topic/post management
                            $bp->gf->is_topic=true;
                            //check the topic id and foum id what ever is required
              
                            $topic_slug = $bp->action_variables[0];
                            $topic_id = gf_get_topic_id_from_slug( $topic_slug );
                         
                         if ( $topic_slug && $topic_id ) {
                                /* Posting a reply to a topic */
                                if ( !$bp->action_variables[1] && isset( $_POST['submit_reply'] ) ) {
                                        /* Check the nonce */
                                    if(!gf_user_can_post($bp->loggedin_user->id)){
                                    bp_core_add_message(__("Sorry, but you don't have the rights to post on forum!","gf"),"error");
                                    }
                                    else{
                                    //we are here means user canb post
                                        check_admin_referer( 'gf_forums_new_reply' );
                                         

                                        if ( !$post_id =gf_new_forum_post( $_POST['reply_text'], $topic_id, $_GET['topic_page'] ) )
                                                bp_core_add_message( __( 'There was an error when replying to that topic', 'gf'), 'error' );
                                        else
                                                bp_core_add_message( __( 'Your reply was posted successfully', 'gf') );
                                    }
                                        if ( $_SERVER['QUERY_STRING'] )
                                                $query_vars = '?' . $_SERVER['QUERY_STRING'];
                                         //need to correct
                                        bp_core_redirect( gf_get_the_topic_permalink($topic_id)  . $query_vars . '#post-' . $post_id );
                                }

			/* making a topic Sticky  */
			else if ( 'stick' == $bp->action_variables[1] && ( gf_current_user_can_admin()||gf_current_user_can_mod() ) ) {
				/* Check the nonce */
				check_admin_referer( 'gf_forums_stick_topic' );

				if ( !gf_sticky_topic( array( 'topic_id' => $topic_id ) ) )
					bp_core_add_message( __( 'There was an error when making that topic a sticky', 'gf' ), 'error' );
				else
					bp_core_add_message( __( 'The topic was made sticky successfully', 'gf' ) );

				do_action( 'gf_stick_forum_topic', $topic_id );
				bp_core_redirect( wp_get_referer() );
			}

			/* Un-Sticky a topic */
			else if ( 'unstick' == $bp->action_variables[1] && ( gf_current_user_can_admin()||gf_current_user_can_mod()) ) {
				/* Check the nonce */
				check_admin_referer( 'gf_forums_unstick_topic' );

				if ( !gf_sticky_topic( array( 'topic_id' => $topic_id, 'mode' => 'unstick' ) ) )
					bp_core_add_message( __( 'There was an error when unsticking that topic', 'gf'), 'error' );
				else
					bp_core_add_message( __( 'The topic was unstuck successfully', 'gf') );

				do_action( 'gf_unstick_forum_topic', $topic_id );
				bp_core_redirect( wp_get_referer() );
			}

			/* Close a topic */
			else if ( 'close' == $bp->action_variables[1] && (gf_current_user_can_admin()||gf_current_user_can_mod() ) ) {
				/* Check the nonce */
				check_admin_referer( 'gf_forums_close_topic' );

				if ( !gf_openclose_topic( array( 'topic_id' => $topic_id ) ) )
					bp_core_add_message( __( 'There was an error when closing that topic', 'gf'), 'error' );
				else
					bp_core_add_message( __( 'The topic was closed successfully', 'gf') );

				do_action( 'gf_close_forum_topic', $topic_id );
				bp_core_redirect( wp_get_referer() );
			}

			/* Open a topic */
			else if ( 'open' == $bp->action_variables[1] && (gf_current_user_can_admin()||gf_current_user_can_mod() ) ) {
				/* Check the nonce */
				check_admin_referer( 'gf_forums_open_topic' );

				if ( !gf_openclose_topic( array( 'topic_id' => $topic_id, 'mode' => 'open' ) ) )
					bp_core_add_message( __( 'There was an error when opening that topic', 'gf'), 'error' );
				else
					bp_core_add_message( __( 'The topic was opened successfully', 'gf') );

				do_action( 'gf_open_forum_topic', $topic_id );
				bp_core_redirect( wp_get_referer() );
			}
                        else if('fav' == $bp->action_variables[1] && empty( $bp->action_variables[2] ) ){
                           check_admin_referer( 'fav-'.$topic_id );
                           if(gf_add_user_favorite($bp->loggedin_user->id, $topic_id))
                                 bp_core_add_message( __( 'Topic added successfully to your favourites.', 'gf'));
                           else
                           bp_core_add_message( __( 'There was an error when adding topic to your favourite.', 'gf'), 'error' );
                           do_action( 'gf_add_topic_favourite', $topic_id );
                            bp_core_redirect( wp_get_referer() );
                            
                        }
                        else if('unfav' == $bp->action_variables[1] && empty( $bp->action_variables[2] ) ){
                             check_admin_referer( 'unfav-'.$topic_id );
                           if(gf_remove_user_favorite($bp->loggedin_user->id, $topic_id))
                                 bp_core_add_message( __( 'Topic removed successfully from your favourites.', 'gf'));
                           else
                           bp_core_add_message( __( 'There was an error when removing topic from your favourite.', 'gf'), 'error' );
                           do_action( 'gf_remove_topic_favourite', $topic_id );
                            bp_core_redirect( wp_get_referer() );
                        }

			/* Delete a topic */
			else if ( 'delete' == $bp->action_variables[1] && empty( $bp->action_variables[2] ) ) {
				/* Fetch the topic */
				$topic = gf_get_topic_details( $topic_id );

				/* Check the logged in user can delete this topic */
				if ( !gf_current_user_can_admin()&&!gf_current_user_can_mod()&& (int)$bp->loggedin_user->id != (int)$topic->topic_poster )
					bp_core_redirect( wp_get_referer() );

				/* Check the nonce */
				check_admin_referer( 'gf_delete_topic' );

				if ( !gf_delete_forum_topic( $topic_id ) )
					bp_core_add_message( __( 'There was an error deleting the topic', 'gf'), 'error' );
				else
					bp_core_add_message( __( 'The topic was deleted successfully', 'gf') );

				do_action( 'gf_delete_forum_topic', $topic_id );
				bp_core_redirect(gf_get_parent_forum_permalink($topic_slug) );
			}

			/* Editing a topic */
			else if ( 'edit' == $bp->action_variables[1] && empty( $bp->action_variables[2] ) ) {
				/* Fetch the topic */
				$topic =gf_get_topic_details( $topic_id );

				/* Check the logged in user can edit this topic */
				if ( !gf_current_user_can_admin()&&!gf_current_user_can_mod() && (int)$bp->loggedin_user->id != (int)$topic->topic_poster )
					bp_core_redirect( wp_get_referer() );

				if ( isset( $_POST['save_changes'] ) ) {
					/* Check the nonce */
					check_admin_referer( 'gf_forums_edit_topic' );

					if ( !gf_update_forum_topic( $topic_id, $_POST['topic_title'], $_POST['topic_text'] ) )
						bp_core_add_message( __( 'There was an error when editing that topic', 'gf'), 'error' );
					else
						bp_core_add_message( __( 'The topic was edited successfully', 'gf') );

					do_action( 'gf_edit_forum_topic', $topic_id );
					bp_core_redirect( gf_get_topic_permalink( $topic_slug ));
				}

				bp_core_load_template( apply_filters( 'gf_template_forum_topic_edit', 'gf/home' ) );
			}
                        else if('tags-remove'==$bp->action_variables[1]&&!empty($bp->action_variables[2])){
                            //remove tag
                            $topic =gf_get_topic_details( $topic_id );
                            $tag_id=intval($bp->action_variables[2]);
                            check_admin_referer( 'remove-tag_' . $tag_id . '|' . $topic_id );
                            do_action( 'gf_forum_topic_tag_delete', $topic_id );
                            $tag    =  bb_get_tag ( $tag_id );
                                      
                            
                            if ( !$tag || !$topic )
                                bp_core_add_message(__('Invalid tag or topic.','gf'));

                            else if ( false !== gf_remove_topic_tag( $tag_id, $topic_id ) ) 
                               bp_core_add_message(sprintf(__("Tags \"%s\" removed successfully.","gf"),$tag->name));
                           else
                                bp_core_add_message(__("Something did not go well. Please try again.","gf"));
                            bp_core_redirect( gf_get_topic_permalink( $topic_slug ));
                        }
                        else if('add-tags'==$bp->action_variables[1]){
                            //add tags to topic
                             $topic = gf_get_topic_details( $topic_id );
                            if ( !is_user_logged_in()||gf_is_user_banned($bp->loggedin_user->id) )
                                bp_core_add_message(__('You may not to be logged in or you are banned in the forum.','gf'));
                            else{
                             $tag      =       @$_POST['tag'];
                             $tag      =       stripslashes( $tag );

                             check_admin_referer( 'add-tag_' . $topic_id );

                           
                            
                            if ( gf_add_topic_tags( $topic_id, $tag ) )
                                   bp_core_add_message(__("Tag added Successfully!",'bp'));
                            else
                                   bp_core_add_message(__('The tag was not added.  Either the tag name was invalid or the topic is closed.','gf'));
                           
                            }
                            bp_core_redirect( gf_get_topic_permalink( $topic_slug ));
                        }
			/* Delete a post */
			else if ( 'delete' == $bp->action_variables[1] && $post_id = $bp->action_variables[3] ) {
				/* Fetch the post */
				$post = gf_get_post( $post_id );

				/* Check the logged in user can edit this topic */
				if (!gf_current_user_can_admin()&&!gf_current_user_can_mod() && (int)$bp->loggedin_user->id != (int)$post->poster_id )
					bp_core_redirect( wp_get_referer() );

				/* Check the nonce */
				check_admin_referer( 'gf_delete_post' );

				if ( !gf_delete_forum_post( $post_id, $topic_id ) )
					bp_core_add_message( __( 'There was an error deleting that post', 'gf'), 'error' );
				else
					bp_core_add_message( __( 'The post was deleted successfully', 'gf') );

				do_action( 'gf_delete_forum_post', $post_id );
				bp_core_redirect( wp_get_referer() );
			}

			/* Editing a post */
			else if ( 'edit' == $bp->action_variables[1] && $post_id = $bp->action_variables[3] ) {
				/* Fetch the post */
                            $bp->gf->is_post_edit=true;
				$post = gf_get_post( $bp->action_variables[3] );

				/* Check the logged in user can edit this topic */
				if (!gf_current_user_can_admin()&&!gf_current_user_can_mod() && (int)$bp->loggedin_user->id != (int)$post->poster_id )
					bp_core_redirect( wp_get_referer() );

				if ( isset( $_POST['save_changes'] ) ) {
					/* Check the nonce */
					check_admin_referer( 'gf_forums_edit_post' );

					if ( !$post_id = gf_update_forum_post( $post_id, $_POST['post_text'], $topic_id, $_GET['topic_page'] ) )
						bp_core_add_message( __( 'There was an error when editing that post', 'gf'), 'error' );
					else
						bp_core_add_message( __( 'The post was edited successfully', 'gf') );

					if ( $_SERVER['QUERY_STRING'] )
						$query_vars = '?' . $_SERVER['QUERY_STRING'];

					do_action( 'gf_edit_forum_post', $post_id );
					bp_core_redirect( gf_get_topic_permalink( $topic_slug ) . '/' . $query_vars . '#post-' . $post_id );
				}

				bp_core_load_template( apply_filters( 'gf_template_forum_topic_post_edit', 'gf/home' ) );
			}

			/* Standard topic display */
			else {
				bp_core_load_template( apply_filters( 'gf_template_forum_topic', 'gf/home' ) );
			}

		}

        }//end of topic/post management
        else if($current_action=="view"){
            
         if(!is_user_logged_in()||empty($bp->action_variables[0])){
              bp_core_add_message(__("Illigal Access","gf"),"error");
              bp_core_redirect(gf_get_home_url());
          }
          $bp->gf->is_view=true;


            
        }
       else if($current_action=="admin"){
           if(!gf_current_user_can_admin()&&!gf_current_user_can_mod()){
                 bp_core_add_message(__("There ain't no cheatin!","gf"),"error");
                 bp_core_redirect(gf_get_home_url());
           }
              $bp->gf->is_admin_screen=true;

              if(!empty($bp->action_variables[0])){
                $action=$bp->action_variables[0];

                if($action=="create"){//create new forum
                     $bp->gf->is_forum_create=true;
                     //check if the form was submitted
                     if(isset ($_POST["save-forum"])){
                         check_admin_referer("gf_create_forum");
                         $post_array=$_POST;

                         if(empty($_POST["forum_parent"]))
                             $post_array["forum_parent"]=gf_get_root_forum_id();
                             if(false !== bb_new_forum( $post_array ))
                               bp_core_add_message(__("Forum Created succesfully","gf"));
                             else
                            bp_core_add_message(__("There was a problem creating forum. Please try again","gf"),"error");
                            
                         
                     }
                }
               
               else if($action=="delete"){//forum delete
                   $bp->gf->is_forum_delete=true;
                   if(!empty($bp->action_variables[1]))
                    $bp->gf->current_forum->forum_id=intval($bp->action_variables[1]);//which forum is to be deleted
                   if(isset($_POST["submit"])){
                        check_admin_referer( 'delete-forums' );
                        $forum_id = (int) $_POST['forum_id'];
                        $move_topics_forum = (int) $_POST['move_topics_forum'];

                        if ( !gf_current_user_can_admin() )
                                    $msg=array("code"=>false,"msg"=>__("You don't have the authority to kill off the forums."));

                            if ( isset($_POST['move_topics']) && $_POST['move_topics'] != 'delete' )
                                    bb_move_forum_topics( $forum_id, $move_topics_forum );

                            if ( !bb_delete_forum( $forum_id ) )
                                    $msg=array("code"=>false,"msg"=> __('Error occured while trying to delete forum') );

                            if(!empty($msg)&&$msg['code']==false){
                                bp_core_add_message($msg['msg'], "error");
                                bp_core_redirect(gf_get_forum_edit_link($forum_id));
                            }
                            bp_core_add_message("Forum deleted successfully.");
                            bp_core_redirect(gf_get_forum_manage_link());
                   }
               }
              else if($action=="edit"){//forum edit
                  $bp->gf->is_forum_edit=true;
                  
                  if(!empty($bp->action_variables[1]))
                    $bp->gf->current_forum->forum_id=intval($bp->action_variables[1]);
                   if(isset ($_POST["save-forum"])){
                         check_admin_referer("gf_edit_forum");
                         $post_array=$_POST;
                         $post_array["forum_id"]=$bp->gf->current_forum->forum_id;
                         if(empty($_POST["forum_parent"]))
                             $post_array["forum_parent"]=gf_get_root_forum_id();
                             if(false !==  bb_update_forum( $post_array ))
                               bp_core_add_message(__("Forum Updated succesfully","gf"));
                             else
                            bp_core_add_message(__("There was a problem in Updating forum. Please try again","gf"),"error");


                     }
                  

                  //edit forum
              }
              else if($action=="manage-forums"){
                  //show list of forums and show also the create form
                $bp->gf->is_manage_forums=true;

              }
              else if($action=="settings"){
                  //show settings
                  if(!empty($_POST["save_settings"])){
                      check_admin_referer("gf-settings");
                 
                  //save settings
                  $def=gf_get_settings();
                  $def["enable_activity"]=$_POST["enable_activity"];
                 
                  gf_update_settings($def);
                  bp_core_add_message(__("Settings Updated!","gf"));
                  }
                    $bp->gf->is_settings=true;
              }
              else if($action=="manage-users"){
                  //show manage usrs here
                  $bp->gf->is_manage_users=true;

                  //check for the actions like banning etc
                  if(!empty($bp->action_variables[1])){
                    $user_action=$bp->action_variables[1];
                    $user_id=intval($bp->action_variables[2]);
                    //check for the action and current user cap
                    //also we are leaving nonce here
                    if(empty($user_id)||gf_is_user_admin($user_id)){
                        //we can not perform any action
                        bp_core_add_message(__("The action requested can not be performed","gf"),"error");
                        bp_core_redirect(gf_get_manage_users_link());
                    }
                   //if we are here, let us consider the action
                    if($user_action=="ban")
                        gf_ban_user($user_id);
                    if($user_action=="unban")
                        gf_unban_user($user_id);
                    else if($user_action=="promote-mod")
                        gf_promote_user_to_mod($user_id);
                    else if($user_action=="promote-admin")
                        gf_promote_user_to_admin($user_id);
                    else if($user_action=="demote"&&!gf_is_user_admin($user_id))
                        gf_demote_user($user_id);
                    bp_core_add_message("success");
                    bp_core_redirect(gf_get_manage_users_link());
                  }

                 }
                        
            }
       }//end of admin section //for forum listing etc
       else if($current_action=="personal"){
          if(!is_user_logged_in()){
              bp_core_add_message(__("Illigal Access","gf"),"error");
              bp_core_redirect(gf_get_home_url());
          }
         //user is logged in, so let us chek what he wants
          $action=$bp->action_variables[0];
          $bp->gf->is_my_topics=true;
       }
       
       else if($current_action=="rss"){
       
           gf_generate_feed();
       }
          else{//front end

              $forum_id=null;
              //we are viewing forum, let us find out parent/child forum
              if(empty($bp->action_variables)){
                  $forum_id=gf_get_forum_id_from_slug($current_action);
                }
              else{
                  $av=$bp->action_variables;
                      //we need the last variable and
                  $current_forum=array_pop($av);
                  $forum_id=gf_get_forum_id_from_slug( $current_forum);
                       }

                  $bp->gf->current_forum=bb_get_forum($forum_id);
    
              }
        //current action is not empty but the action is neither topic nor admin, it must be a forum
        //check for action variables, as the current forum, else the $current_action is current_forum
             
                   
                }
          else{//forum home
          $bp->gf->is_home=true;
                   
                    }
                    
                
	do_action( 'bbpress_init' );
        global $bp;
	/* Check to see if the user has posted a new topic from the forums page. */
	if ( isset( $_POST['submit_topic'] ) && function_exists( 'gf_create_topic' )&&gf_user_can_create_topic($bp->loggedin_user->id) ) {
	// Check the nonce ///
          
	 check_admin_referer( 'gf_create_topic' );

	if ( $bp->gf->current_forum = bb_get_forum(  $_POST['forum_id']  ) ) {
	          $forum_id=$bp->gf->current_forum->forum_id;
                  if ( !$topic = gf_new_forum_topic( $_POST['topic_title'], $_POST['topic_text'], $_POST['topic_tags'], $forum_id ) )
			bp_core_add_message( __( 'There was an error when creating the topic', 'gf'), 'error' );
		   else
			bp_core_add_message( __( 'The topic was created successfully', 'gf') );

			bp_core_redirect( gf_get_topic_permalink( $topic->topic_slug) );
				
            }
       
        }//end of new topic

    do_action( 'gf_forums_directory_forums_setup' );
    if(gf_is_feed())
        bp_core_load_template( apply_filters( 'bp_gf_forums_template_directory_forums_setup', 'gf/rss'));
    else
        bp_core_load_template( apply_filters( 'bp_gf_forums_template_directory_forums_setup', 'gf/home' ) );

    

}
}
add_action( 'wp', 'gf_forums_directory_forums_setup',6 );



/**
 * Whether bbpress is installed or not
 * @global <type> $bp
 * @return <bool>
 */
function gf_forums_is_installed_correctly() {
	global $bp;

	if ( file_exists( $bp->gf->bbconfig ) )
		return true;

	return false;
}
//MISC functions
function gf_forums_filter_caps( $allcaps ) {
	global $bp, $wp_roles, $bb_table_prefix;

	$bb_cap = get_usermeta( $bp->loggedin_user->id, $bb_table_prefix . 'capabilities' );

	if ( empty( $bb_cap ) )
		return $allcaps;

	$bb_cap = array_keys($bb_cap);
	$bb_cap = $wp_roles->get_role( $bb_cap[0] );
	$bb_cap = $bb_cap->capabilities;

	return array_merge( (array) $allcaps, (array) $bb_cap );
}
add_filter( 'user_has_cap', 'gf_forums_filter_caps' );

?>