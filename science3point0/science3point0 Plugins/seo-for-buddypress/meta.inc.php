<?php

### Add the meta data to the head, depends on the viewed page

function bp_seo_mu(){
	global $meta;
	
	### USER BLOG HOME
  	if (is_home()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog"));
    	return $meta[0];
  	}
	### USER BLOG ARCHIVE
  	if (is_archive() && !is_tag() && !is_category()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_archiv"));
    	return $meta[0];
  	} 
	### USER BLOG CATEGORIES
  	if (is_category()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_cat"));
    	return $meta[0];
  	}
	### USER BLOG POSTS
  	if (is_single()){
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_posts"));

    	// Overwriting with manual data if exists
    	if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
  		if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
  		if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}
  		
    	return $meta[0];
  	}
	### USER BLOG PAGES
  	if (is_page()) {
	    $meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_pages"));
	    if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
	  	if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
	  	if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}    
	    return $meta[0];
  	}
	### USER BLOG AUTHOR PAGES
	if (is_author()) {
	    $meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_autor_pages"));
	    return $meta[0];
	}
	### USER BLOG SEARCH PAGES
  	if (is_search()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_search_pages"));
    	return $meta[0];
  	}
	### USER BLOG 404 PAGES
  	if (is_404()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_404_pages"));
    	return $meta[0];
  	}
	### USER BLOG TAG PAGES
  	if (is_tag()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_user_blog_tag_pages"));
    	return $meta[0];
  	}
 	return bp_seo_get_page_title();
}

### adds the meta tags to the wp_head
function bp_seo(){
  	global $bp, $meta;
  	  
  	$bp_seo_components = get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_plugins");
  	$current_component = $bp->current_component;
  	$current_action = $bp->current_action;
  	$directory = $bp->is_directory;

  	### MAIN BLOG START
  	if (is_home()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_start"));
    	return $meta[0];
  	}
  	if ( bp_is_activity_front_page()){
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog"));
    	return $meta[0];
  	}
  	### MAIN BLOG ARCHIVE
  	if (is_archive() && !is_tag() && !is_category()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_archiv"));
    	return $meta[0];
  	} 
  	### MAIN BLOG CATEGORIES
  	if (is_category()  && !$directory){
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_cat"));
    	return $meta[0];
  	}
  	### MAIN BLOG POSTS
  	if (is_single()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_posts"));
    	
    	// Overwriting with manual data if exists
    	if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
  		if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
  		if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}
    	return $meta[0];
  	}
  	
  	### MAIN BLOG PAGES
  	if (bp_is_blog_page() && !is_search() && !is_404() && !is_author() && !is_tag() && !is_single() && !bp_is_front_page()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_pages"));
    	if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
  		if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
  		if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}
    	return $meta[0];
  	}
  	### MAIN BLOG AUTHOR PAGES
  	if (is_author()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_autor_pages"));
    	return $meta[0];
  	}
  	### MAIN BLOG SEARCH PAGES
  	if (is_search()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_search_pages"));
    	return $meta[0];
  	}
  	
  	### MAIN BLOG 404 PAGES
  	if (is_404()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_404_pages"));
    	return $meta[0];
  	}
  	### MAIN BLOG TAG PAGES
  	if (is_tag()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_tag_pages"));
    	return $meta[0];
  	}
  	### MAIN BLOG REGISTER PAGES
  	if (bp_is_register_page()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_reg_pages"));
    	return $meta[0];
  	}

  	###
  	### GROUP PAGES
  	###
  
  	### ALL GROUP PAGES 
  	if(bp_is_groups_component()){
  		
    	if (bp_is_group_home()){
	    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_groups_home"));
	    	return $meta[0];
    	} 
    	
    	if(bp_is_groups_component() && bp_is_single_item()){
      			if(bp_is_group_forum() && !bp_is_group_forum_topic()){
        			$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_groups_forum"));
        			return $meta[0];
        		}
      			if(bp_is_group_forum_topic()){
         			$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_groups_forum_topic"));
          			return $meta[0];
      			}
      			if ( function_exists('bp_is_group_wire') ){
        			if(bp_is_group_wire()){
	         			$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_groups_wire"));
	          			return $meta[0];
        			}
      			} 
      		if(bp_is_group_members()){
        		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_groups_members"));
        		return $meta[0];
      		}
    	}
    	
    	if($bp_seo_components != '' ){
      		foreach ($bp_seo_components as $bp_seo_component => $value ){
        		if ($current_action == $value[slug]) {
	          		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_".$bp_seo_component."_".$current_component."_extends"));
	          		return $meta[0];
        		}
      		}
    	}
	}
  
	###
  	### PROFILE PAGES
  	###

  	### PROFILE HOME
  	if((bp_is_profile_component()|| bp_is_activity_component() || bp_is_blogs_component() || bp_is_friends_component()) && !$directory && !bp_is_create_blog()){
    	
  		if(bp_is_user_profile()){
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_profil"));
      		return $meta[0];
    	}
    	
    	### PROFILE FRIENDS
    	if (bp_is_user_friends() && !$directory){
     	 	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_profil_friends"));
    		return $meta[0];
    	}
    	### PROFILE ACTIVITY USER
    	if (bp_is_activity_component() && !$directory) {
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_profil_activity"));
      		return $meta[0];
    	}
    	### PROFILE ACTIVITY FRIENDS
    	if (bp_is_user_friends_activity()){
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_activity_friends"));
      		return $meta[0];
    	}
    	### PROFILE BLOGS
    	if (bp_is_user_blogs() && !$directory ) {
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_profil_blogs"));
      		return $meta[0];
    	}
    	### PROFILE BLOGS RECENT POSTS
    	if (bp_is_user_recent_posts()) {
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_blogs_recent_posts"));
      		return $meta[0];
    	}
    	### PROFILE BLOGS RECENT COMMENTS
    	if (bp_is_user_recent_commments()) {
      		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_blogs_recent_commments"));
      		return $meta[0];
    	}   
  	}
  	
    ### PROFILE GROUPS
    if (bp_is_user_groups()  && !bp_is_single_item() && !$directory){
      	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_profil_groups"));
      	return $meta[0];
    } 

  	###
  	### DIRECTORIES
  	###

  	### DIRECTORY BLOGS
  	if ($directory &&  $current_component == BP_BLOGS_SLUG) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_directory_blogs"));
    	return $meta[0];
  	}
  	### DIRECTORY ACTIVITY
  	if ($directory &&  $current_component == BP_ACTIVITY_SLUG) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_directory_activity"));
    	return $meta[0];
  	}
  	### DIRECTORY GROUPS
  	if ($directory &&  $current_component == BP_GROUPS_SLUG) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_directory_groups"));
    	return $meta[0];
  	}
  	### DIRECTORY FORUMS
  	if ($directory &&  $current_component == BP_FORUMS_SLUG) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_directory_forums"));
    	return $meta[0];
  	}
  	### DIRECTORY MEMBERS    
  	if ($directory &&  $current_component == BP_MEMBERS_SLUG) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_directory_members"));
    	return $meta[0];
  	}
  	if($bp_seo_components != '' ){
  		
    	foreach ($bp_seo_components as $bp_seo_component => $value ){
      		if ($directory &&  $current_component == $value[slug]) {
        		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_".$current_component."_directory"));
        		return $meta[0];
      		}
      		if (!$directory &&  $current_component == $value[slug]){
        		if ($bp_seo_component == $current_component && bp_is_single_item()) {
          		$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_".$current_component.'_'.$current_component.'_extends'));
          		return $meta[0];
        		}
        		if($bp_seo_component == $current_component  && !bp_is_single_item())  {
          			$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_".$current_component.'_profile_extends'));
          			return $meta[0];
        		}
      		}
    	}
  	}
 	return bp_seo_get_page_title();
}

### Standard Wordpress
function wp_seo(){
	global $meta;
	
	### USER BLOG HOME
  	if (is_front_page()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_start"));
    	return $meta[0];
  	}
	### USER BLOG ARCHIVE
  	if (is_archive() && !is_tag() && !is_category()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_archiv"));
    	return $meta[0];
  	} 
	### USER BLOG CATEGORIES
  	if (is_category()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_cat"));
    	return $meta[0];
  	}
	### USER BLOG POSTS
  	if (is_single()) {
	    $meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_posts"));
	    if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
	  	if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
	  	if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}
	    return $meta[0];
  	}
	### USER BLOG PAGES
  	if (is_page()) {
	    $meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_pages"));
	    if(get_seo4all_title()!=""){$meta[0]=get_seo4all_title();}
	  	if(get_seo4all_description()!=""){$meta[1]=get_seo4all_description();}
	  	if(get_seo4all_keywords()!=""){$meta[2]=get_seo4all_keywords();}    
	    return $meta[0];
  	}
	### USER BLOG AUTHOR PAGES
  	if (is_author()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_autor_pages"));
    	return $meta[0];
  	}
	### USER BLOG SEARCH PAGES
  	if (is_search()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_search_pages"));
    	return $meta[0];
  	}
	### USER BLOG 404 PAGES
  	if (is_404()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_404_pages"));
    	return $meta[0];
  	}
	### USER BLOG TAG PAGES
  	if (is_tag()) {
    	$meta = stripmetatags(get_blog_option(SITE_ID_CURRENT_SITE,"bp_seo_main_blog_tag_pages"));
    	return $meta[0];
  	}
 	return get_the_title();
}

function bp_seo_get_page_title() {
	global $bp, $post, $wp_query, $current_blog;

	if (is_front_page() || !bp_current_component() || ( is_home() && bp_is_page( 'home' ))){
		$title= __('Home','buddypress');

	}elseif(bp_is_blog_page()){
		if (is_single()){
			$title=__('Blog &#124; '.$post->post_title,'buddypress');
		}else if(is_category()) {
			$title=__('Blog &#124; Categories &#124; '.ucwords($wp_query->query_vars['category_name']),'buddypress');
		}else if(is_tag()) {
			$title=__('Blog &#124; Tags &#124; '.ucwords( $wp_query->query_vars['tag'] ),'buddypress');
		}else if(is_page()){
			$title=$post->post_title;
		}else
			$title=__('Blog','buddypress');

	}elseif(!empty($bp->displayed_user->fullname)){
 		$title=strip_tags( $bp->displayed_user->fullname.' &#124; '.ucwords($bp->current_component));

	}elseif($bp->is_single_item){
		$title=ucwords($bp->current_component).' &#124; '.$bp->bp_options_title.' &#124; '.$bp->bp_options_nav[$bp->current_component][$bp->current_action]['name'];

	}elseif($bp->is_directory){
		if (!$bp->current_component)
			$title=sprintf(__('%s Directory','buddypress'),ucwords(BP_MEMBERS_SLUG));
		else
			$title=sprintf(__('%s Directory','buddypress'),ucwords($bp->current_component));

	}elseif(bp_is_register_page()){
		$title= __( 'Create an Account','buddypress');

	}elseif(bp_is_activation_page()){
		$title=__( 'Activate your Account','buddypress');

	}elseif(bp_is_group_create()){
		$title=__( 'Create a Group','buddypress');

	}elseif(bp_is_create_blog()){
		$title=__( 'Create a Blog','buddypress');
	}

	if(defined('BP_ENABLE_MULTIBLOG')){
		$blog_title=get_blog_option($current_blog->blog_id,'blogname');
	}else {
		$blog_title=get_blog_option(BP_ROOT_BLOG,'blogname');
	}

	return $blog_title.' &#124; '.esc_attr($title);
}

function stripmetatags($meta){
  	global $post; 

  	if(!is_array($meta)){
  		$meta[0] = bp_seo_get_page_title();
  	}
  	$newmeta = Array();
  	$i=0;
  	foreach($meta as $data){
   		$newmeta[$i] = SFB_Special_Tags::replace ($data, $post);
   		$i++;
  	} 
  	return $newmeta; 
}


function bp_seo_meta(){
	global $meta;
	$metadesc_length=get_option('bp_seo_metadesc_length');
	if($metadesc_length!=0 && $metadesc_length>0){
		$meta[1]=strip_tags(substr($meta[1],0,$metadesc_length));
	}
	if($metadesc_length==""){
		$meta[1]=strip_tags(substr($meta[1],0,180));
	}
	$meta[1]=preg_replace("/\r|\n/s", "", $meta[1]);
	
?>
    <meta name="description" content="<?php echo $meta[1] ?>" />
    <meta name="keywords" content="<?php echo $meta[2] ?>" />
<?php } ?>