<?php
/* 
 * Template tags for Global forum admin
 * 
 */

/*links template*/
function gf_get_admin_link(){
    $admin_link=gf_get_home_url()."/admin";
    return apply_filters("gf_admin_link", $admin_link);//_fil
}

function gf_get_manage_users_link(){
return gf_get_admin_link()."/manage-users";
}
function gf_get_forum_manage_link(){
    return gf_get_admin_link()."/manage-forums";
}

function gf_get_forum_create_link(){
    return gf_get_admin_link()."/create";
}

function gf_get_forum_edit_link($forum_id){
    return gf_get_admin_link()."/edit/".$forum_id;
}

function gf_get_forum_delete_link($forum_id){
    return gf_get_admin_link()."/delete/".$forum_id;
}

function gf_get_settings_link(){
    return gf_get_admin_link()."/settings";
}

/**** Conditionals*/
function gf_is_admin(){
  global $bp;
  if($bp->current_component==$bp->gf->slug&&$bp->current_action=="admin")
          return true;
  return false;
}
function gf_is_forum_create(){
    global $bp;
    return $bp->gf->is_forum_create;
}

function gf_is_forum_edit(){
 global $bp;
    return $bp->gf->is_forum_edit;
}
function gf_is_forum_delete(){
 global $bp;
    return $bp->gf->is_forum_delete;
}
function gf_is_forum_admin(){
     global $bp;
    return $bp->gf->is_admin_screen;
}

function gf_is_manage_forum(){
    global $bp;
    return  $bp->gf->is_manage_forums;
}
function gf_is_manage_users(){
    global $bp;
    return $bp->gf->is_manage_users;
}

function gf_is_settings(){
    global $bp;
    return $bp->gf->is_settings;
}


function gf_forum_create_form_action(){

}
function gf_forum_edit_form_action(){

}
function gf_forum_delete_form_action(){

}
function gf_current_admin_action(){
    echo gf_get_current_admin_action();
}
function gf_get_current_admin_action(){
  global $bp;
    if(gf_is_forum_admin())
        return $bp->action_variables[0];
    return '';
}

function gf_admin_links(){
 if(gf_current_user_can_admin()){
 ?>
 <div class="gf_admin_links">
    <a href="<?php echo gf_get_forum_manage_link();?>"><?php _e("Manage Forums","gf");?></a>|
    <a href="<?php echo gf_get_forum_create_link();?>"><?php _e("Create Forum","gf");?></a>|
    <a href="<?php echo gf_get_manage_users_link();?>"><?php _e("Manage Users","gf");?></a>|
    <a href="<?php echo gf_get_settings_link();?>"><?php _e("Settings","gf");?></a>
 </div>
    <?php

 }
}

/* who can do what*/
function gf_current_user_can_admin(){
    global $bp;
        return apply_filters("gf_current_user_can", gf_is_user_admin($bp->loggedin_user->id));
}

function gf_current_user_can_mod(){
    global $bp;
    if(gf_is_user_mod($bp->loggedin_user->id))
        return true;
    return false;
}
function gf_current_user_can_post(){
return gf_user_can_post();
}
function gf_current_user_can_create_topic(){
    return gf_user_can_create_topic();
}

/*for user management*/

function gf_unban_user($user_id){
    if(gf_is_user_banned($user_id))
        delete_usermeta($user_id, "gf_role");
}
function gf_ban_user($user_id){
update_usermeta($user_id, "gf_role", "banned");
}
function gf_is_user_banned($user_id){
     $user_cap=get_usermeta($user_id, "gf_role");
    if(!empty($user_cap)&&$user_cap=="banned")
        return true;
    return false;
}

function gf_is_user_mod($user_id){
 $user_cap=get_usermeta($user_id, "gf_role");
    if(!empty($user_cap)&&$user_cap=="moderator")
        return true;
    return false;

}

function gf_demote_user($user_id){
    delete_usermeta($user_id, "gf_role");
}
function gf_promote_user_to_mod($user_id){
 update_usermeta($user_id, "gf_role", "moderator");
}

function gf_promote_user_to_admin($user_id){
    update_usermeta($user_id, "gf_role", "admin");
}
function gf_is_user_admin($user_id){
    $functin_name="is_site_admin";
    if(function_exists("is_super_admin"))
        $functin_name="is_super_admin";
    
    $user_login=bp_core_get_username($user_id); //$bp->loggedin_user->user_login;
    $is_admin=call_user_func($functin_name, $user_login) ;//is_site_admin($user_login);
    $user_cap=get_usermeta($user_id, "gf_role");
    if(!$is_admin&&!empty($user_cap)&&$user_cap=="admin")
        $is_admin= true;
    return apply_filters("gf_is_user_admin", $is_admin,$user_id);
}

function gf_get_user_ban_link($user_id){
 return gf_get_manage_users_link()."/ban/".$user_id;
}
function gf_get_user_unban_link($user_id){
    return gf_get_manage_users_link()."/unban/".$user_id;
}
function gf_get_user_demote_link($user_id){
    return gf_get_manage_users_link()."/demote/".$user_id;
}
function gf_get_user_promote_mod_link($user_id){

 return gf_get_manage_users_link()."/promote-mod/".$user_id;
}
function gf_get_user_promote_admin_link($user_id){

 return gf_get_manage_users_link()."/promote-admin/".$user_id;
}
function gf_user_can_create_topic($user_id=null){
if(!is_user_logged_in())
    $can_create=false;
else{global $bp;
     if(!$user_id)
         $user_id=$bp->loggedin_user->id;
    $can_create=!gf_is_user_banned($user_id);
}
return apply_filters("gf_user_can_create_topic", $can_create,$user_id);
}

function gf_user_can_post($user_id=null){
 if(!is_user_logged_in())
     $can_post=false;
 else{global $bp;
     if(!$user_id)
         $user_id=$bp->loggedin_user->id;
     $can_post=!gf_is_user_banned($user_id);
 }
     return apply_filters("gf_user_can_post",$can_post,$user_id);

}
 ?>