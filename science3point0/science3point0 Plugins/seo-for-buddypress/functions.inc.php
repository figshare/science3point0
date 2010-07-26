<?php

### Update General Seo page to the option table.

function bp_seo_general_page(){
       global $current_user;

	if ( isset( $_POST['update_bp_seo'] ) ) {
### Directory Blogs
      $directory_blogs = Array($_POST['directory_blogs_title'],$_POST['directory_blogs'],$_POST['directory_blogs_tags']);
       if($directory_blogs != get_option('bp_seo_directory_blogs')){
          update_option('bp_seo_directory_blogs', $directory_blogs);
          echo '<div class="updated"><p>Directory blogs meta changes saved.</p></div>'; 
       }
### Directory Activity
      $directory_activity = Array($_POST['directory_activity_title'],$_POST['directory_activity'],$_POST['directory_activity_tags']) ;
       if($directory_activity != get_option('bp_seo_directory_activity')){
          update_option('bp_seo_directory_activity', $directory_activity);
          echo '<div class="updated"><p>Directory Activity meta changes saved.</p></div>'; 
       }
### Directory Members
      $directory_members = Array($_POST['directory_members_title'],$_POST['directory_members'],$_POST['directory_members_tags']) ;
       if($directory_members != get_option('bp_seo_directory_members')){
          update_option('bp_seo_directory_members', $directory_members);
          echo '<div class="updated"><p>Directory Members meta changes saved.</p></div>'; 
       }
### Directory Groups
      $directory_groups = Array($_POST['directory_groups_title'],$_POST['directory_groups'],$_POST['directory_groups_tags']) ;
       if($directory_groups != get_option('bp_seo_directory_groups')){
          update_option('bp_seo_directory_groups', $directory_groups);
          echo '<div class="updated"><p>Directory Groups meta changes saved.</p></div>'; 
       }
### Directory Forums
      $directory_forums = Array($_POST['directory_forums_title'],$_POST['directory_forums'],$_POST['directory_forums_tags']) ;
       if($directory_forums != get_option('bp_seo_directory_forums')){
          update_option('bp_seo_directory_forums', $directory_forums);
          echo '<div class="updated"><p>Directory Forums meta changes saved.</p></div>'; 
       }
### Profile Home
      $profil = Array($_POST['profil_title'],$_POST['profil'],$_POST['profil_tags']) ;
       if($profil != get_option('bp_seo_profil')){
          update_option('bp_seo_profil', $profil);
          echo '<div class="updated"><p>Profil Home meta changes saved.</p></div>'; 
       }
### Profile Blogs
      $profil_blogs = Array($_POST['profil_blogs_title'],$_POST['profil_blogs'],$_POST['profil_blogs_tags']) ;
       if($profil_blogs != get_option('bp_seo_profil_blogs')){
          update_option('bp_seo_profil_blogs', $profil_blogs);
          echo '<div class="updated"><p>Profil Blogs meta changes saved.</p></div>'; 
       }
### Profile Blogs  recent_posts
      $profil_blogs_recent_posts = Array($_POST['profil_blogs_recent_posts_title'],$_POST['profil_blogs_recent_posts'],$_POST['profil_blogs_recent_posts_tags']) ;
       if($profil_blogs_recent_posts != get_option('bp_seo_profil_blogs_recent_posts')){
          update_option('bp_seo_profil_blogs_recent_posts', $profil_blogs_recent_posts);
          echo '<div class="updated"><p>Profil Blogs recent_posts meta changes saved.</p></div>'; 
       }
### Profile Blogs recent_commments
      $profil_blogs_recent_commments = Array($_POST['profil_blogs_recent_commments_title'],$_POST['profil_blogs_recent_commments'],$_POST['profil_blogs_recent_commments_tags']) ;
       if($profil_blogs_recent_commments != get_option('bp_seo_profil_blogs_recent_commments')){
          update_option('bp_seo_profil_blogs_recent_commments', $profil_blogs_recent_commments);
          echo '<div class="updated"><p>Profil Blogs recent_commments meta changes saved.</p></div>'; 
       }
### Profile friends
      $profil_friends = Array($_POST['profil_friends_title'],$_POST['profil_friends'],$_POST['profil_friends_tags']) ;
       if($profil_friends != get_option('bp_seo_profil_friends')){
          update_option('bp_seo_profil_friends', $profil_friends);
          echo '<div class="updated"><p>Profil friends meta changes saved.</p></div>'; 
       }
### Profile Groups
      $profil_groups = Array($_POST['profil_groups_title'],$_POST['profil_groups'],$_POST['profil_groups_tags'] );
       if($profil_groups != get_option('bp_seo_profil_groups')){
          update_option('bp_seo_profil_groups', $profil_groups);
          echo '<div class="updated"><p>Profil Groups meta changes saved.</p></div>'; 
       }
### Profile Wire
      $profil_wire = Array($_POST['profil_wire_title'],$_POST['profil_wire'],$_POST['profil_wire_tags']) ;
       if($profil_wire != get_option('bp_seo_profil_wire')){
          update_option('bp_seo_profil_wire', $profil_wire);
          echo '<div class="updated"><p>Profil Wire meta changes saved.</p></div>'; 
       }
### Profile Activity
      $profil_activity = Array($_POST['profil_activity_title'],$_POST['profil_activity'],$_POST['profil_activity_tags']) ;
       if($profil_activity != get_option('bp_seo_profil_activity')){
          update_option('bp_seo_profil_activity', $profil_activity);
          echo '<div class="updated"><p>Profil Activity meta changes saved.</p></div>'; 
       }
### Profile Activity Friends 
      $profil_activity_friends = Array($_POST['profil_activity_friends_title'],$_POST['profil_activity_friends'],$_POST['profil_activity_friends_tags']) ;
       if($profil_activity_friends != get_option('bp_seo_profil_activity_friends')){
          update_option('bp_seo_profil_activity_friends', $profil_activity_friends);
          echo '<div class="updated"><p>Profil Activity Friends  meta changes saved.</p></div>'; 
       }
### Groups Forum
      $groups_forum = Array($_POST['groups_forum_title'],$_POST['groups_forum'],$_POST['groups_forum_tags']) ;
       if($groups_forum != get_option('bp_seo_groups_forum')){
          update_option('bp_seo_groups_forum', $groups_forum);
          echo '<div class="updated"><p>Groups Forum meta changes saved.</p></div>'; 
       }
### Groups Forum Topic
      $groups_forum_topic = Array($_POST['groups_forum_topic_title'],$_POST['groups_forum_topic'],$_POST['groups_forum_topic_tags']) ;
       if($groups_forum_topic != get_option('bp_seo_groups_forum_topic')){
          update_option('bp_seo_groups_forum_topic', $groups_forum_topic);
          echo '<div class="updated"><p>Groups Forum Topic meta changes saved.</p></div>'; 
       }
### Groups Wire
      $groups_wire = Array($_POST['groups_wire_title'],$_POST['groups_wire'],$_POST['groups_wire_tags']) ;
       if($groups_wire != get_option('bp_seo_groups_wire')){
          update_option('bp_seo_groups_wire', $groups_wire);
          echo '<div class="updated"><p>Groups Wire meta changes saved.</p></div>'; 
       }
### Groups Members
      $groups_members = Array($_POST['groups_members_title'],$_POST['groups_members'],$_POST['groups_members_tags']) ;
       if($groups_members != get_option('bp_seo_groups_members')){
          update_option('bp_seo_groups_members', $groups_members);
          echo '<div class="updated"><p>Groups Members meta changes saved.</p></div>'; 
       }
### Groups Home
      $groups_home = Array($_POST['groups_home_title'],$_POST['groups_home'],$_POST['groups_home_tags']) ;
       if($groups_home != get_option('bp_seo_groups_home')){
          update_option('bp_seo_groups_home', $groups_home);
          echo '<div class="updated"><p>Groups Home meta changes saved.</p></div>'; 
       }
### MAIN BLOG START
      $main_blog_start = Array($_POST['main_blog_start_title'],$_POST['main_blog_start'],$_POST['main_blog_start_tags']) ;
       if($main_blog_start != get_option('bp_seo_main_blog_start')){
          update_option('bp_seo_main_blog_start', $main_blog_start);
          echo '<div class="updated"><p>Main Blog Start meta changes saved.</p></div>'; 
       }
### MAIN BLOG HOME
      $main_blog = Array($_POST['main_blog_title'],$_POST['main_blog'],$_POST['main_blog_tags']) ;
       if($main_blog != get_option('bp_seo_main_blog')){
          update_option('bp_seo_main_blog', $main_blog);
          echo '<div class="updated"><p>Main Blog Home meta changes saved.</p></div>'; 
       }
### MAIN BLOG ARCHIVE
      $main_blog_archiv = Array($_POST['main_blog_archiv_title'],$_POST['main_blog_archiv'],$_POST['main_blog_archiv_tags']) ;
       if($main_blog_archiv != get_option('bp_seo_main_blog_archiv')){
          update_option('bp_seo_main_blog_archiv', $main_blog_archiv);
          echo '<div class="updated"><p>Main Blog Archiv meta changes saved.</p></div>'; 
       }
### MAIN BLOG CATEGORIES
      $main_blog_cat = Array($_POST['main_blog_cat_title'],$_POST['main_blog_cat'],$_POST['main_blog_cat_tags']) ;
       if($main_blog_cat != get_option('bp_seo_main_blog_cat')){
          update_option('bp_seo_main_blog_cat', $main_blog_cat);
          echo '<div class="updated"><p>Main Blog Categories meta changes saved.</p></div>'; 
       }
### MAIN BLOG POSTS 
      $main_blog_posts = Array($_POST['main_blog_posts_title'],$_POST['main_blog_posts'],$_POST['main_blog_posts_tags']) ;
       if($main_blog_posts != get_option('bp_seo_main_blog_posts')){
          update_option('bp_seo_main_blog_posts', $main_blog_posts);
          echo '<div class="updated"><p>Main Blog Post meta changes saved.</p></div>'; 
       }
### MAIN BLOG PAGES
      $main_blog_pages = Array($_POST['main_blog_pages_title'],$_POST['main_blog_pages'],$_POST['main_blog_pages_tags']) ;
       if($main_blog_pages != get_option('bp_seo_main_blog_pages')){
          update_option('bp_seo_main_blog_pages', $main_blog_pages);
          echo '<div class="updated"><p>MAIN BLOG PAGES meta changes saved.</p></div>'; 
       }
 ### MAIN BLOG AUTHOR PAGES
      $main_blog_autor_pages = Array($_POST['main_blog_autor_pages_title'],$_POST['main_blog_autor_pages'],$_POST['main_blog_autor_pages_tags']) ;
       if($main_blog_autor_pages != get_option('bp_seo_main_blog_autor_pages')){
          update_option('bp_seo_main_blog_autor_pages', $main_blog_autor_pages);
          echo '<div class="updated"><p>MAIN BLOG AUTOR PAGES meta changes saved.</p></div>'; 
       }
### MAIN BLOG SEARCH PAGES
      $main_blog_search_pages = Array($_POST['main_blog_search_pages_title'],$_POST['main_blog_search_pages'],$_POST['main_blog_search_pages_tags']) ;
       if($main_blog_search_pages != get_option('bp_seo_main_blog_search_pages')){
          update_option('bp_seo_main_blog_search_pages', $main_blog_search_pages);
          echo '<div class="updated"><p>MAIN BLOG SEARCH PAGES meta changes saved.</p></div>'; 
       }
### MAIN BLOG 404 PAGES
      $main_blog_404_pages = Array($_POST['main_blog_404_pages_title'],$_POST['main_blog_404_pages'],$_POST['main_blog_404_pages_tags']) ;
       if($main_blog_404_pages != get_option('bp_seo_main_blog_404_pages')){
          update_option('bp_seo_main_blog_404_pages', $main_blog_404_pages);
          echo '<div class="updated"><p>MAIN BLOG 404 PAGES meta changes saved.</p></div>'; 
       }
### MAIN BLOG TAG PAGES
      $main_blog_tag_pages = Array($_POST['main_blog_tag_pages_title'],$_POST['main_blog_tag_pages'],$_POST['main_blog_tag_pages_tags']) ;
       if($main_blog_tag_pages != get_option('bp_seo_main_blog_tag_pages')){
          update_option('bp_seo_main_blog_tag_pages', $main_blog_tag_pages);
          echo '<div class="updated"><p>MAIN BLOG TAG PAGES meta changes saved.</p></div>'; 
       }
### MAIN BLOG REGISTER PAGES
      $main_blog_reg_pages = Array($_POST['main_blog_reg_pages_title'],$_POST['main_blog_reg_pages'],$_POST['main_blog_reg_pages_tags']) ;
       if($main_blog_reg_pages != get_option('bp_seo_main_blog_reg_pages')){
          update_option('bp_seo_main_blog_reg_pages', $main_blog_reg_pages);
          echo '<div class="updated"><p>MAIN BLOG REGISTER PAGES meta changes saved.</p></div>'; 
       }
### USER BLOG HOME
      $user_blog = Array($_POST['user_blog_title'],$_POST['user_blog'],$_POST['user_blog_tags']);
       if($user_blog != get_option('bp_seo_user_blog')){
          update_option('bp_seo_user_blog', $user_blog);
          echo '<div class="updated"><p>USER BLOG HOME meta changes saved.</p></div>'; 
       }
### USER BLOG ARCHIVE
      $user_blog_archiv = Array($_POST['user_blog_archiv_title'],$_POST['user_blog_archiv'],$_POST['user_blog_archiv_tags']) ;
       if($user_blog_archiv != get_option('bp_seo_user_blog_archiv')){
          update_option('bp_seo_user_blog_archiv', $user_blog_archiv);
          echo '<div class="updated"><p>USER BLOG ARCHIV meta changes saved.</p></div>'; 
       }
### USER BLOG CATEGORIES
      $user_blog_cat = Array($_POST['user_blog_cat_title'],$_POST['user_blog_cat'],$_POST['user_blog_cat_tags']) ;
       if($user_blog_cat != get_option('bp_seo_user_blog_cat')){
          update_option('bp_seo_user_blog_cat', $user_blog_cat);
          echo '<div class="updated"><p>Directory Forums meta changes saved.</p></div>'; 
       }
### USER BLOG POSTS 
      $user_blog_posts = Array($_POST['user_blog_posts_title'],$_POST['user_blog_posts'],$_POST['user_blog_posts_tags']) ;
       if($user_blog_posts != get_option('bp_seo_user_blog_posts')){
          update_option('bp_seo_user_blog_posts', $user_blog_posts);
          echo '<div class="updated"><p>USER BLOG POSTS meta changes saved.</p></div>'; 
       }
### USER BLOG PAGES
      $user_blog_pages = Array($_POST['user_blog_pages_title'],$_POST['user_blog_pages'],$_POST['user_blog_pages_tags']) ;
       if($user_blog_pages != get_option('bp_seo_user_blog_pages')){
          update_option('bp_seo_user_blog_pages', $user_blog_pages);
          echo '<div class="updated"><p>USER BLOG PAGES meta changes saved.</p></div>'; 
       }
### USER BLOG AUTHOR PAGES
      $user_blog_autor_pages = Array($_POST['user_blog_autor_pages_title'],$_POST['user_blog_autor_pages'],$_POST['user_blog_autor_pages_tags']) ;
       if($user_blog_autor_pages != get_option('bp_seo_user_blog_autor_pages')){
          update_option('bp_seo_user_blog_autor_pages', $user_blog_autor_pages);
          echo '<div class="updated"><p>USER BLOG AUTOR PAGES meta changes saved.</p></div>'; 
       }
### USER BLOG SEARCH PAGES
      $user_blog_search_pages = Array($_POST['user_blog_search_pages_title'],$_POST['user_blog_search_pages'],$_POST['user_blog_search_pages_tags']) ;
       if($user_blog_search_pages != get_option('bp_seo_user_blog_search_pages')){
          update_option('bp_seo_user_blog_search_pages', $user_blog_search_pages);
          echo '<div class="updated"><p>USER BLOG SEARCH PAGES meta changes saved.</p></div>'; 
       }
### USER BLOG 404 PAGES
      $user_blog_404_pages = Array($_POST['user_blog_404_pages_title'],$_POST['user_blog_404_pages'],$_POST['user_blog_404_pages_tags']) ;
       if($user_blog_404_pages != get_option('bp_seo_user_blog_404_pages')){
          update_option('bp_seo_user_blog_404_pages', $user_blog_404_pages);
          echo '<div class="updated"><p>USER BLOG 404 PAGES meta changes saved.</p></div>'; 
       }
### USER BLOG TAG PAGES
      $user_blog_tag_pages = Array($_POST['user_blog_tag_pages_title'],$_POST['user_blog_tag_pages'],$_POST['user_blog_tag_pages_tags']);
       if($user_blog_tag_pages != get_option('bp_seo_user_blog_tag_pages')){
          update_option('bp_seo_user_blog_tag_pages', $user_blog_tag_pages);
          echo '<div class="updated"><p>USER BLOG TAG PAGES meta changes saved.</p></div>'; 
       }   

}
  bp_seo_general();
} 
 
### update plugins configuration and Seo pages to the option table.
function bp_seo_plugins_page(){

### update Buddypress components and plugins configuration page
  if ( isset( $_POST['update_bp_seo_plugins_setup'] ) ) {
  	$plugin_counter = $_POST['plugin_counter'];
  	$bp_seo_plugins = array(); 
  	    
    foreach($plugin_counter as $plugin){
    $bp_seo_plugins[$_POST['plugin_name_'.$plugin]] = array ( 'slug' => $_POST['plugin_slug_'.$plugin],
                                                              'directory'   => $_POST['plugin_directory_'.$plugin],
                                                              'plugin_extends'    =>  $_POST['plugin_extends_'.$plugin],
                                                              'plugin_use' => $_POST['plugin_use_'.$plugin]); 
    }
         
    if($bp_seo_plugins != get_option('$bp_seo_plugins')){
      update_option('bp_seo_plugins', $bp_seo_plugins);
      echo '<div class="updated"><p>Buddypress Components and Plugin Setup Options saved.</p></div>'; 
    } 
  }

### update Buddypress components and plugins Seo pages
  if ( isset( $_POST['update_bp_seo_plugins_meta'] ) ) {
    $main_comp_slugs = $_POST['main_comp_slug'];
    $sub_comp_slugs = $_POST['sub_comp_slug'];
    $i = 0;
    foreach($sub_comp_slugs as $sub_slug){
      
      $bp_seo_tmp = array();
      $bp_seo_extends_tmp = array();
      $bp_seo_use_tmp = array();
      
      $title = $main_comp_slugs[$i].'_'.$sub_slug.'_title';
      if(isset($_POST[$title])){
        $bp_seo_tmp[0] = $_POST[$title];
      }
      $desc = $main_comp_slugs[$i].'_'.$sub_slug.'_desc';
      if(isset($_POST[$desc])){
        $bp_seo_tmp[1] .= $_POST[$desc];      
      }     
      $tag = $main_comp_slugs[$i].'_'.$sub_slug.'_tags';
      if(isset($_POST[$tag])){
        $bp_seo_tmp[2] .= $_POST[$tag];
      }
      update_option('bp_seo_'.$main_comp_slugs[$i].'_'.$sub_slug, $bp_seo_tmp);
      
      $title_extends = $main_comp_slugs[$i].'_'.$sub_slug.'_title_extends';
      if(isset($_POST[$title_extends])){
        $bp_seo_extends_tmp[0] .= $_POST[$title_extends];
      }
      $desc_extends = $main_comp_slugs[$i].'_'.$sub_slug.'_desc_extends';
      if(isset($_POST[$desc_extends])){
        $bp_seo_extends_tmp[1] .= $_POST[$desc_extends];  
      }     
      $tag_extends = $main_comp_slugs[$i].'_'.$sub_slug.'_tags_extends';
      if(isset($_POST[$tag_extends])){
        $bp_seo_extends_tmp[2] .= $_POST[$tag_extends];
      }
      update_option('bp_seo_'.$main_comp_slugs[$i].'_'.$sub_slug.'_extends', $bp_seo_extends_tmp);
      
      $title_use = $main_comp_slugs[$i].'_'.$sub_slug.'_title_use';
      if(isset($_POST[$title_use])){
        $bp_seo_use_tmp[0] .= $_POST[$title_use];
      }
      $desc_use = $main_comp_slugs[$i].'_'.$sub_slug.'_desc_use';
      if(isset($_POST[$desc_use])){
        $bp_seo_use_tmp[1] .= $_POST[$desc_use];   
      }     
      $tag_use = $main_comp_slugs[$i].'_'.$sub_slug.'_tags_use';
      if(isset($_POST[$tag_use])){
        $bp_seo_use_tmp[2] .= $_POST[$tag_use];
      }
      update_option('bp_seo_'.$main_comp_slugs[$i].'_'.$sub_slug.'_use', $bp_seo_use_tmp);
      $i++;
    }
          echo '<div class="updated"><p>Meta changes saved.</p></div>';     
  }
	bp_seo_plugins();
}
 
  /**
   * meta data entry template
   *
   * @param Array $lable (Title, title name, description name, tags name, component slug, sub component slug)
   * @param Array $meta (value for title, value for description, value for tags)	 
   * @return template
   */
function bp_seo_entry($lable,$meta){
			$tmp  = '<div class="sfb-entry">';
			$tmp .= '<div class="sfb-entry-title">'.strtoupper($lable[0]).'</div>';
			$tmp .= '<div class="sfb-entry-left">';
			$tmp .= '            <div class="sfb-item-title"><label for="prefix">Title: </label></div>';
			$tmp .= '  				  <div class="sfb-item-desc"><label for="prefix">Description: </label></div>';
			$tmp .= '  				<div class="sfb-item-tag"><label for="prefix">Keywords/Tags: </label></div>';
			$tmp .= '    		</div>';
			$tmp .= '				<div class="sfb-entry-right">';
      		$tmp .= '<INPUT TYPE="hidden" NAME="main_comp_slug[]"  VALUE="'.$lable[4].'">'; 
			$tmp .= '<INPUT TYPE="hidden" NAME="sub_comp_slug[]"  VALUE="'.$lable[5].'">'; 
			$tmp .= '  				<input name="'.$lable[1].'" type="text" style="width: 80%;" id="'.$lable[1].'" value="'.$meta[0].'"/><br>';
			$tmp .= '  				<textarea name="'.$lable[2].'" type="text" id="'.$lable[2].'"  rows="5" style="width: 80%;"/>'.$meta[1].'</textarea><br>';
 			$tmp .= '     		<input name="'.$lable[3].'" type="text" style="width: 80%;" id="'.$lable[3].'" value="'.$meta[2].'"/>';
			$tmp .= '				</div>';
			$tmp .= '			</div>';
			$tmp .= '			<div class="spacer"></div>	';
			return $tmp;
} 

### Update and delete Seo for Buddypress.
function bp_seo_settings_page(){
	
### Delete Seo for Buddypress.
if(isset($_POST['bp-metadesc-length'])){
  	update_option('bp_seo_metadesc_length',$_POST['bp_seo_metadesc_length']);  		  
}

if(isset($_POST['bp-seo-update-version'])){ 

$metaupdate = Array(); 
//************ DIRECTORY BLOGS 	
	if ( get_option('rr_bp_seo_directory_blogs_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_directory_blogs_title');
  	$metaupdate[1] = get_option('rr_bp_seo_directory_blogs');	
  	$metaupdate[2] = get_option('rr_bp_seo_directory_blogs_tags');
    update_option('bp_seo_directory_blogs', $metaupdate);
    $metaupdate = '';
	}
//************ DIRECTORY MEMBERS 	
	if ( get_option('rr_bp_seo_directory_members_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_directory_members_title');
  	$metaupdate[1] = get_option('rr_bp_seo_directory_members');	
  	$metaupdate[2] = get_option('rr_bp_seo_directory_members_tags');
    update_option('bp_seo_directory_members', $metaupdate);
    $metaupdate = '';
	}
//************ DIRECTORY GROUPS 	
	if ( get_option('rr_bp_seo_directory_groups_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_directory_groups_title');
  	$metaupdate[1] = get_option('rr_bp_seo_directory_groups');	
  	$metaupdate[2] = get_option('rr_bp_seo_directory_groups_tags');
    update_option('bp_seo_directory_groups', $metaupdate);
    $metaupdate = '';
	}
	//************ DIRECTORY FORUMS 	
	if ( get_option('rr_bp_seo_directory_forums_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_directory_forums_title');
  	$metaupdate[1] = get_option('rr_bp_seo_directory_forums');	
  	$metaupdate[2] = get_option('rr_bp_seo_directory_forums_tags');
    update_option('bp_seo_directory_forums', $metaupdate);
    $metaupdate = '';
	}
//************ DIRECTORY EVENTS 	
	if ( get_option('rr_bp_seo_directory_events_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_directory_events_title');
  	$metaupdate[1] = get_option('rr_bp_seo_directory_events');	
  	$metaupdate[2] = get_option('rr_bp_seo_directory_events_tags');
    update_option('bp_seo_directory_events', $metaupdate);
    $metaupdate = '';
	}
//************ PROFILE HOME	
	if ( get_option('rr_bp_seo_profil_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_profil_title');
  	$metaupdate[1] = get_option('rr_bp_seo_profil');	
  	$metaupdate[2] = get_option('rr_bp_seo_profil_tags');
    update_option('bp_seo_profil', $metaupdate);
    $metaupdate = '';
	}
//************PROFILE BLOGS 	
	if ( get_option('rr_bp_seo_blogs_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_blogs_title');
  	$metaupdate[1] = get_option('rr_bp_seo_blogs');	
  	$metaupdate[2] = get_option('rr_bp_seo_blogs_tags');
    update_option('bp_seo_profil_blogs', $metaupdate);
    $metaupdate = '';
	}
//************PROFILE BLOGS RECENT POSTS	
	if ( get_option('rr_bp_seo_blogs_recent_posts_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_blogs_recent_posts_title');
  	$metaupdate[1] = get_option('rr_bp_seo_blogs_recent_posts');	
  	$metaupdate[2] = get_option('rr_bp_seo_blogs_recent_posts_tags');
    update_option('bp_seo_profil_blogs_recent_posts', $metaupdate);
    $metaupdate = '';
	}
//************PROFILE BLOGS RECENT COMMENTS
	if ( get_option('rr_bp_seo_blogs_recent_commments_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_blogs_recent_commments_title');
  	$metaupdate[1] = get_option('rr_bp_seo_blogs_recent_commments');	
  	$metaupdate[2] = get_option('rr_bp_seo_blogs_recent_commments_tags');
    update_option('bp_seo_profil_blogs_recent_commments', $metaupdate);
    $metaupdate = '';
	}

//************PROFILE activity 	
	if ( get_option('rr_bp_seo_activity_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_activity_title');
  	$metaupdate[1] = get_option('rr_bp_seo_activity');	
  	$metaupdate[2] = get_option('rr_bp_seo_activity_tags');
    update_option('bp_seo_profil_activity', $metaupdate);
    $metaupdate = '';
	}

//************PROFILE activity friends
	if ( get_option('rr_bp_seo_activity_friends_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_activity_friends_title');
  	$metaupdate[1] = get_option('rr_bp_seo_activity_friends');	
  	$metaupdate[2] = get_option('rr_bp_seo_activity_friends_tags');
    update_option('bp_seo_profil_activity_friends', $metaupdate);
    $metaupdate = '';
	}

//************PROFILE friends 		
	if ( get_option('rr_bp_seo_friends_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_friends_title');
  	$metaupdate[1] = get_option('rr_bp_seo_friends');	
  	$metaupdate[2] = get_option('rr_bp_seo_friends_tags');
    update_option('bp_seo_profil_friends', $metaupdate);
    $metaupdate = '';
	}
//************PROFILE groups 		
	if ( get_option('rr_bp_seo_groups_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_groups_title');
  	$metaupdate[1] = get_option('rr_bp_seo_groups');	
  	$metaupdate[2] = get_option('rr_bp_seo_groups_tags');
    update_option('bp_seo_profil_groups', $metaupdate);
    $metaupdate = '';
	}
//************ groups  _forum		
	if ( get_option('rr_bp_seo_groups_forum_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_groups_forum_title');
  	$metaupdate[1] = get_option('rr_bp_seo_groups_forum');	
  	$metaupdate[2] = get_option('rr_bp_seo_groups_forum_tags');
    update_option('bp_seo_groups_forum', $metaupdate);
    $metaupdate = '';
	}
//************ groups  _forum_topic		
	if ( get_option('rr_bp_seo_groups_forum_topic_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_groups_forum_topic_title');
  	$metaupdate[1] = get_option('rr_bp_seo_groups_forum_topic');	
  	$metaupdate[2] = get_option('rr_bp_seo_groups_forum_topic_tags');
    update_option('bp_seo_groups_forum_topic', $metaupdate);
    $metaupdate = '';
	}
//************ groups _members 		
	if ( get_option('rr_bp_seo_groups_members_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_groups_members_title');
  	$metaupdate[1] = get_option('rr_bp_seo_groups_members');	
  	$metaupdate[2] = get_option('rr_bp_seo_groups_members_tags');
    update_option('bp_seo_groups_members', $metaupdate);
    $metaupdate = '';
	}
//************ groups 	_home	
	if ( get_option('rr_bp_seo_groups_home_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_groups_home_title');
  	$metaupdate[1] = get_option('rr_bp_seo_groups_home');	
  	$metaupdate[2] = get_option('rr_bp_seo_groups_home_tags');
    update_option('bp_seo_groups_home', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG START
	if ( get_option('rr_bp_seo_main_blog_start_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_start_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_start');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_start_tags');
    update_option('bp_seo_main_blog_start', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG HOME
	if ( get_option('rr_bp_seo_main_blog_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_tags');
    update_option('bp_seo_main_blog', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG ARCHIVE
	if ( get_option('rr_bp_seo_main_blog_archiv_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_archiv_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_archiv');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_archiv_tags');
    update_option('bp_seo_main_blog_archiv', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG CATEGORIES
	if ( get_option('rr_bp_seo_main_blog_cat_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_cat_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_cat');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_cat_tags');
    update_option('bp_seo_main_blog_cat', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG POSTS 
	if ( get_option('rr_bp_seo_main_blog_posts_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_posts_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_posts');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_posts_tags');
    update_option('bp_seo_main_blog_posts', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG PAGES
	if ( get_option('rr_bp_seo_main_blog_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_pages_tags');
    update_option('bp_seo_main_blog_pages', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG AUTHOR PAGES
	if ( get_option('rr_bp_seo_main_blog_autor_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_autor_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_autor_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_autor_pages_tags');
    update_option('bp_seo_main_blog_autor_pages', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG SEARCH PAGES
	if ( get_option('rr_bp_seo_main_blog_search_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_search_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_search_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_search_pages_tags');
    update_option('bp_seo_main_blog_search_pages', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG 404 PAGES
	if ( get_option('rr_bp_seo_main_blog_404_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_404_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_404_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_404_pages_tags');
    update_option('bp_seo_main_blog_404_pages', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG TAG PAGES
	if ( get_option('rr_bp_seo_main_blog_tag_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_tag_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_tag_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_tag_pages_tags');
    update_option('bp_seo_main_blog_tag_pages', $metaupdate);
    $metaupdate = '';
	}
//************ MAIN BLOG REGISTER PAGES
	if ( get_option('rr_bp_seo_main_blog_reg_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_main_blog_reg_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_main_blog_reg_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_main_blog_reg_pages_tags');
    update_option('bp_seo_main_blog_reg_pages', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG HOME
	if ( get_option('rr_bp_seo_user_blog_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_tags');
    update_option('bp_seo_user_blog', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG ARCHIVE
	if ( get_option('rr_bp_seo_user_blog_archiv_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_archiv_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_archiv');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_archiv_tags');
    update_option('bp_seo_user_blog_archiv', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG CATEGORIES
	if ( get_option('rr_bp_seo_user_blog_cat_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_cat_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_cat');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_cat_tags');
    update_option('bp_seo_user_blog_cat', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG POSTS 
	if ( get_option('rr_bp_seo_user_blog_posts_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_posts_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_posts');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_posts_tags');
    update_option('bp_seo_user_blog_posts', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG PAGES
	if ( get_option('rr_bp_seo_user_blog_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_pages_tags');
    update_option('bp_seo_user_blog_pages', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG AUTHOR PAGES
	if ( get_option('rr_bp_seo_user_blog_autor_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_autor_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_autor_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_autor_pages_tags');
    update_option('bp_seo_user_blog_autor_pages', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG SEARCH PAGES
	if ( get_option('rr_bp_seo_user_blog_search_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_search_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_search_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_search_pages_tags');
    update_option('bp_seo_user_blog_search_pages', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG 404 PAGES
	if ( get_option('rr_bp_seo_user_blog_404_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_404_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_404_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_404_pages_tags');
    update_option('bp_seo_user_blog_404_pages', $metaupdate);
    $metaupdate = '';
	}
//************ USER BLOG TAG PAGES
	if ( get_option('rr_bp_seo_user_blog_tag_pages_title') != "" )	{
    $metaupdate[0] = get_option('rr_bp_seo_user_blog_tag_pages_title');
  	$metaupdate[1] = get_option('rr_bp_seo_user_blog_tag_pages');	
  	$metaupdate[2] = get_option('rr_bp_seo_user_blog_tag_pages_tags');
    update_option('bp_seo_user_blog_tag_pages', $metaupdate);
    $metaupdate = '';
	}

// flickr & youtube & medien
       delete_option('rr_bp_seo_medien_name');
       delete_option('rr_bp_seo_medien_title');
       delete_option('rr_bp_seo_medien');
       delete_option('rr_bp_seo_medien_tags'); 
       delete_option('rr_bp_seo_youtube_title');
       delete_option('rr_bp_seo_youtube');
       delete_option('rr_bp_seo_youtube_tags');
       delete_option('rr_bp_seo_flickr_title');
       delete_option('rr_bp_seo_flickr');
       delete_option('rr_bp_seo_flickr_tags');

// directory views groups, members, blog, events
       delete_option('rr_bp_seo_directory_blogs_title');
       delete_option('rr_bp_seo_directory_blogs');
       delete_option('rr_bp_seo_directory_blogs_tags');
       delete_option('rr_bp_seo_directory_groups_title');
       delete_option('rr_bp_seo_directory_groups');
       delete_option('rr_bp_seo_directory_groups_tags');
       delete_option('rr_bp_seo_directory_forums_title');
       delete_option('rr_bp_seo_directory_forums');
       delete_option('rr_bp_seo_directory_forums_tags');
       delete_option('rr_bp_seo_directory_members_name');
       delete_option('rr_bp_seo_directory_members_title');
       delete_option('rr_bp_seo_directory_members');
       delete_option('rr_bp_seo_directory_members_tags');
       delete_option('rr_bp_seo_directory_events_title');
       delete_option('rr_bp_seo_directory_events');
       delete_option('rr_bp_seo_directory_events_tags');
// goups
       delete_option('rr_bp_seo_groups_forum_title');
       delete_option('rr_bp_seo_groups_forum');
       delete_option('rr_bp_seo_groups_forum_tags');
       delete_option('rr_bp_seo_groups_forum_topic_title');
       delete_option('rr_bp_seo_groups_forum_topic');
       delete_option('rr_bp_seo_groups_forum_topic_tags');
       delete_option('rr_bp_seo_groups_wire_title');
       delete_option('rr_bp_seo_groups_wire');
       delete_option('rr_bp_seo_groups_wire_tags');
       delete_option('rr_bp_seo_groups_members_title');
       delete_option('rr_bp_seo_groups_members');
       delete_option('rr_bp_seo_groups_members_tags');
       delete_option('rr_bp_seo_groups_home_title');
       delete_option('rr_bp_seo_groups_home');
       delete_option('rr_bp_seo_groups_home_tags');
// events
       delete_option('rr_bp_seo_events_forum_title');
       delete_option('rr_bp_seo_events_forum');
       delete_option('rr_bp_seo_events_forum_tags');
       delete_option('rr_bp_seo_events_wire_title');
       delete_option('rr_bp_seo_events_wire');
       delete_option('rr_bp_seo_events_wire_tags');
       delete_option('rr_bp_seo_events_members_title');
       delete_option('rr_bp_seo_events_members');
       delete_option('rr_bp_seo_events_members_tags');
       delete_option('rr_bp_seo_events_home_title');
       delete_option('rr_bp_seo_events_home');
       delete_option('rr_bp_seo_events_home_tags');
       delete_option('rr_bp_seo_events_profile_title');
       delete_option('rr_bp_seo_events_profile');
       delete_option('rr_bp_seo_events_profile_tags');            
// profile
       delete_option('rr_bp_seo_profil_title');
       delete_option('rr_bp_seo_profil');
       delete_option('rr_bp_seo_profil_tags');
       delete_option('rr_bp_seo_blogs_title');
       delete_option('rr_bp_seo_blogs');
       delete_option('rr_bp_seo_blogs_tags');
       delete_option('rr_bp_seo_blogs_recent_posts_title');
       delete_option('rr_bp_seo_blogs_recent_posts');
       delete_option('rr_bp_seo_blogs_recent_posts_tags');
       delete_option('rr_bp_seo_blogs_recent_commments_title');
       delete_option('rr_bp_seo_blogs_recent_commments');
       delete_option('rr_bp_seo_blogs_recent_commments_tags');
       delete_option('rr_bp_seo_friends_title');
       delete_option('rr_bp_seo_friends');
       delete_option('rr_bp_seo_friends_tags');
       delete_option('rr_bp_seo_groups_title');
       delete_option('rr_bp_seo_groups');
       delete_option('rr_bp_seo_groups_tags');
       delete_option('rr_bp_seo_wire_title');
       delete_option('rr_bp_seo_wire');
       delete_option('rr_bp_seo_wire_tags');
       delete_option('rr_bp_seo_album_title');
       delete_option('rr_bp_seo_album');
       delete_option('rr_bp_seo_album_tags');
       delete_option('rr_bp_seo_activity_title');
       delete_option('rr_bp_seo_activity');
       delete_option('rr_bp_seo_activity_tags');
       delete_option('rr_bp_seo_activity_friends_title');
       delete_option('rr_bp_seo_activity_friends');
       delete_option('rr_bp_seo_activity_friends_tags');
// your page
       delete_option('rr_bp_seo_page1_name');
       delete_option('rr_bp_seo_page1_title');
       delete_option('rr_bp_seo_page1');
       delete_option('rr_bp_seo_page1_tags');
       delete_option('rr_bp_seo_page_name');
       delete_option('rr_bp_seo_page_title');
       delete_option('rr_bp_seo_page');
       delete_option('rr_bp_seo_page_tags');
       delete_option('rr_bp_seo_page2_name');
       delete_option('rr_bp_seo_page2_title');
       delete_option('rr_bp_seo_page2');
       delete_option('rr_bp_seo_page2_tags');
// MAIN BLOG
       delete_option('rr_bp_seo_main_blog_start_title');
       delete_option('rr_bp_seo_main_blog_start');
       delete_option('rr_bp_seo_main_blog_start_tags');
       delete_option('rr_bp_seo_main_blog_title');
       delete_option('rr_bp_seo_main_blog');
       delete_option('rr_bp_seo_main_blog_tags');
       delete_option('rr_bp_seo_main_blog_archiv_title');
       delete_option('rr_bp_seo_main_blog_archiv');
       delete_option('rr_bp_seo_main_blog_archiv_tags');
       delete_option('rr_bp_seo_main_blog_cat_title');
       delete_option('rr_bp_seo_main_blog_cat');
       delete_option('rr_bp_seo_main_blog_cat_tags');
       delete_option('rr_bp_seo_main_blog_posts_title');
       delete_option('rr_bp_seo_main_blog_posts');
       delete_option('rr_bp_seo_main_blog_posts_tags');
       delete_option('rr_bp_seo_main_blog_pages_title');
       delete_option('rr_bp_seo_main_blog_pages');
       delete_option('rr_bp_seo_main_blog_pages_tags');
       delete_option('rr_bp_seo_main_blog_autor_pages_title');
       delete_option('rr_bp_seo_main_blog_autor_pages');
       delete_option('rr_bp_seo_main_blog_autor_pages_tags');
       delete_option('rr_bp_seo_main_blog_search_pages_title');
       delete_option('rr_bp_seo_main_blog_search_pages');
       delete_option('rr_bp_seo_main_blog_search_pages_tags');
       delete_option('rr_bp_seo_main_blog_404_pages_title');
       delete_option('rr_bp_seo_main_blog_404_pages');
       delete_option('rr_bp_seo_main_blog_404_pages_tags');
       delete_option('rr_bp_seo_main_blog_tag_pages_title');
       delete_option('rr_bp_seo_main_blog_tag_pages');
       delete_option('rr_bp_seo_main_blog_tag_pages_tags');
       delete_option('rr_bp_seo_main_blog_reg_pages_title');
       delete_option('rr_bp_seo_main_blog_reg_pages');
       delete_option('rr_bp_seo_main_blog_reg_pages_tags');
// USER BLOG
       delete_option('rr_bp_seo_user_blog_title');
       delete_option('rr_bp_seo_user_blog');
       delete_option('rr_bp_seo_user_blog_tags');
       delete_option('rr_bp_seo_user_blog_archiv_title');
       delete_option('rr_bp_seo_user_blog_archiv');
       delete_option('rr_bp_seo_user_blog_archiv_tags');
       delete_option('rr_bp_seo_user_blog_cat_title');
       delete_option('rr_bp_seo_user_blog_cat');
       delete_option('rr_bp_seo_user_blog_cat_tags');
       delete_option('rr_bp_seo_user_blog_posts_title');
       delete_option('rr_bp_seo_user_blog_posts');
       delete_option('rr_bp_seo_user_blog_posts_tags');
       delete_option('rr_bp_seo_user_blog_pages_title');
       delete_option('rr_bp_seo_user_blog_pages');
       delete_option('rr_bp_seo_user_blog_pages_tags');
       delete_option('rr_bp_seo_user_blog_autor_pages_title');
       delete_option('rr_bp_seo_user_blog_autor_pages');
       delete_option('rr_bp_seo_user_blog_autor_pages_tags');
       delete_option('rr_bp_seo_user_blog_search_pages_title');
       delete_option('rr_bp_seo_user_blog_search_pages');
       delete_option('rr_bp_seo_user_blog_search_pages_tags');
       delete_option('rr_bp_seo_user_blog_404_pages_title');
       delete_option('rr_bp_seo_user_blog_404_pages');
       delete_option('rr_bp_seo_user_blog_404_pages_tags');
       delete_option('rr_bp_seo_user_blog_tag_pages_title');
       delete_option('rr_bp_seo_user_blog_tag_pages');
       delete_option('rr_bp_seo_user_blog_tag_pages_tags');

       update_option('bp_seo_version', '1.0');
       
       ?> <div class="updated"><p>Seo for Buddypress update successful.</p></div> <?php
}

### Delete Seo for Buddypress.
if(isset($_POST['bp-seo-remove'])){
  global $wpdb;

  $options = $wpdb->get_results("SELECT * FROM wp_".SITE_ID_CURRENT_SITE."_options ORDER BY option_name");
    foreach((array) $options as $option) :
    	$disabled = '';
    	$option->option_name = esc_attr($option->option_name);
    	if(substr($option->option_name, 0, 6)=='bp_seo') {
        delete_option($option->option_name);     
      }
      if(substr($option->option_name, 0, 9)=='rr_bp_seo') {
        delete_option($option->option_name);     
      }
    endforeach;

       ?> <div class="updated"><p>Seo for Buddypress filds in option table successful deleted.</p></div> <?php
    }
}

function get_seo4all_title(){
	global $post;
	$title=get_post_meta($post->ID,"_title");
	$found=false;
	
	// If is there is no data, getting data from wpseo
	if($title[0]==""){
		$title=get_post_meta($post->ID,"_wpseo_edit_title");
		$found=true;
	}
	// If is there is no data, getting data from all in one seopack
	if($title[0]==""){
		$title=get_post_meta($post->ID,"_aioseop_title");
		$found=true;
	}
	/*
	if($found){
		if(curPageURL()!=false){
			$title[0].=" - ".__('Page')." ".curPageURL();
		}
	}
	*/
	return $title[0];
}
function get_seo4all_description(){
	global $post;
	$description=get_post_meta($post->ID,"_description");
	
	// If is there is no data, getting data from wpseo
	if($description[0]==""){
		$description=get_post_meta($post->ID,"_wpseo_edit_description");
	}
	// If is there is no data, getting data from all in one seopack
	if($description[0]==""){
		$description=get_post_meta($post->ID,"_aioseop_description");
	}
	return $description[0];
}
function get_seo4all_keywords(){
	global $post;
	$keywords=get_post_meta($post->ID,"_keywords");
	
	// If is there is no data, getting data from wpseo
	if($keywords[0]==""){
		$keywords=get_post_meta($post->ID,"_wpseo_edit_keywords");
	}
	// If is there is no data, getting data from all in one seopack
	if($keywords[0]==""){
		$keywords=get_post_meta($post->ID,"_aioseop_keywords");
	}
	return $keywords[0];
}
function post_seo4all_title($id){
	if (isset($_POST['seo4all_title']) === true) {
		update_post_meta($id,"_title",$_POST["seo4all_title"]);
	}
}
function post_seo4all_description($id){
	if (isset($_POST['seo4all_description']) === true) {
		update_post_meta($id,"_description",$_POST["seo4all_description"]);
	}
}
function post_seo4all_keywords($id){
	if (isset($_POST['seo4all_keywords']) === true) {
		update_post_meta($id,"_keywords",$_POST["seo4all_keywords"]);
	}
}
if(!function_exists('get_blog_option')){
	function get_blog_option( $blog_id, $option_name, $default = false ) {
	  	return get_option( $option_name, $default );
 	}
}

if(!function_exists('add_blog_option')){
 	function add_blog_option( $blog_id, $option_name, $option_value ) {
  		return add_option( $option_name, $option_value );
 	}
}
if(!function_exists('update_blog_option')){
 	function update_blog_option( $blog_id, $option_name, $option_value ) {
  		return update_option( $option_name, $option_value );
 	}
}

/*if(!function_exists('curPageURL')){
	function curPageURL() {
		$pageURL = 'http';
		//check what if its secure or not
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		
		//add the protocol
		$pageURL .= "://";
		//check what port we are on
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		//cut off everything on the URL except the last 3 characters
		$urlEnd = substr($pageURL, -3);
		//strip off the two forward shashes

		$page = str_replace("/", "", $urlEnd);
		//return just the number
		if(is_numeric($page)){
			return $page;
		}else{
			return false;			
		}
	}
}  */
?>