<?php

### loads the meta data variables for the option page and creates the option page

function bp_seo_general(){
global $bp;
$bp_seo_default_wp = Array('%%title%% | %%sitename%%','%%excerpt%% %%category_description%% %%tag_description%% %%term_description%%','%%tag%%, %%category%%');
$bp_seo_default_bp = Array('%%componentname%% | %%sitename%%','%%componentdescription%%','%%componentname%%');
$bp_seo_default_dirctory = Array('%%sitename%%','','');
$bp_seo_default_profile = Array('%%displayname%% | %%sitename%%','','%%usernicename%%,%%fullname%%');
$bp_seo_default_group_forum = Array('%%componentname%% %%forumtopictitle%% | %%sitename%% ','%%forumtopictext%%','%%forumtopicpostername%%');

//************ get the meta values for the option page
	if ( get_option('bp_seo_directory_blogs') != "" )	{	$directory_blogs = get_option('bp_seo_directory_blogs'); } else {$directory_blogs = $bp_seo_default_dirctory; }
	if ( get_option('bp_seo_directory_activity') != "" )	{	$directory_activity = get_option('bp_seo_directory_activity'); }  else {$directory_activity = $bp_seo_default_dirctory; }
  if ( get_option('bp_seo_directory_members') != "" )	{	$directory_members = get_option('bp_seo_directory_members'); } else {$directory_members = $bp_seo_default_dirctory; }
  if ( get_option('bp_seo_directory_groups') != "" )	{	$directory_groups = get_option('bp_seo_directory_groups'); } else {$directory_groups = $bp_seo_default_dirctory; }
  if ( get_option('bp_seo_directory_forums') != "" )	{	$directory_forums = get_option('bp_seo_directory_forums'); } else {$directory_forums = $bp_seo_default_dirctory; }
  if ( get_option('bp_seo_profil') != "" )	{	$profil = get_option('bp_seo_profil'); } else {$profil = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_blogs') != "" )	{	$profil_blogs = get_option('bp_seo_profil_blogs'); } else {$profil_blogs = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_blogs_recent_posts') != "" )	{	$profil_blogs_recent_posts = get_option('bp_seo_profil_blogs_recent_posts'); } else {$profil_blogs_recent_posts = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_blogs_recent_commments') != "" )	{	$profil_blogs_recent_commments = get_option('bp_seo_profil_blogs_recent_commments'); } else {$profil_blogs_recent_commments = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_wire') != "" )	{	$profil_wire = get_option('bp_seo_profil_wire'); } else {$profil_wire = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_activity') != "" )	{	$profil_activity = get_option('bp_seo_profil_activity'); } else {$profil_activity = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_activity_friends') != "" )	{	$profil_activity_friends = get_option('bp_seo_profil_activity_friends'); } else {$profil_activity_friends = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_friends') != "" )	{	$profil_friends = get_option('bp_seo_profil_friends'); } else {$profil_friends = $bp_seo_default_profile; }
  if ( get_option('bp_seo_profil_groups') != "" )	{	$profil_groups = get_option('bp_seo_profil_groups'); } else {$profil_groups = $bp_seo_default_profile; }
  if ( get_option('bp_seo_groups_home') != "" )	{	$groups_home = get_option('bp_seo_groups_home'); } else {$groups_home = $bp_seo_default_bp; }
  if ( get_option('bp_seo_groups_forum') != "" )	{	$groups_forum = get_option('bp_seo_groups_forum'); } else {$groups_forum = $bp_seo_default_group_forum; }
  if ( get_option('bp_seo_groups_forum_topic') != "" )	{	$groups_forum_topic = get_option('bp_seo_groups_forum_topic'); } else {$groups_forum_topic = $bp_seo_default_group_forum; }
  if ( get_option('bp_seo_groups_wire') != "" )	{	$groups_wire = get_option('bp_seo_groups_wire'); } else {$groups_wire = $bp_seo_default_bp; }
  if ( get_option('bp_seo_groups_members') != "" )	{	$groups_members = get_option('bp_seo_groups_members'); } else {$groups_members = $bp_seo_default_bp; }
  if ( get_option('bp_seo_groups_home') != "" )	{	$groups_home = get_option('bp_seo_groups_home'); } else {$groups_home = $bp_seo_default_bp; }
  if ( get_option('bp_seo_main_blog_start') != "" )	{	$main_blog_start = get_option('bp_seo_main_blog_start'); } else {$main_blog_start = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog') != "" )	{	$main_blog = get_option('bp_seo_main_blog'); } else {$main_blog = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_archiv') != "" )	{	$main_blog_archiv = get_option('bp_seo_main_blog_archiv'); } else {$main_blog_archiv = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_cat') != "" )	{	$main_blog_cat = get_option('bp_seo_main_blog_cat'); } else {$main_blog_cat = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_posts') != "" )	{	$main_blog_posts = get_option('bp_seo_main_blog_posts'); } else {$main_blog_posts = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_pages') != "" )	{	$main_blog_pages = get_option('bp_seo_main_blog_pages'); } else {$main_blog_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_search_pages') != "" )	{	$main_blog_search_pages = get_option('bp_seo_main_blog_search_pages'); } else {$main_blog_search_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_404_pages') != "" )	{	$main_blog_404_pages = get_option('bp_seo_main_blog_404_pages'); } else {$main_blog_404_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_tag_pages') != "" )	{	$main_blog_tag_pages = get_option('bp_seo_main_blog_tag_pages'); } else {$main_blog_tag_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_reg_pages') != "" )	{	$main_blog_reg_pages = get_option('bp_seo_main_blog_reg_pages'); } else {$main_blog_reg_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_main_blog_autor_pages') != "" )	{	$main_blog_autor_pages = get_option('bp_seo_main_blog_autor_pages'); } else {$main_blog_autor_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog') != "" )	{	$user_blog = get_option('bp_seo_user_blog'); } else {$user_blog = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_archiv') != "" )	{	$user_blog_archiv = get_option('bp_seo_user_blog_archiv'); } else {$user_blog_archiv = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_cat') != "" )	{	$user_blog_cat = get_option('bp_seo_user_blog_cat'); } else {$user_blog_cat = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_posts') != "" )	{	$user_blog_posts = get_option('bp_seo_user_blog_posts'); } else {$user_blog_cat = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_pages') != "" )	{	$user_blog_pages = get_option('bp_seo_user_blog_pages'); } else {$user_blog_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_search_pages') != "" )	{	$user_blog_search_pages = get_option('bp_seo_user_blog_search_pages'); } else {$user_blog_search_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_404_pages') != "" )	{	$user_blog_404_pages = get_option('bp_seo_user_blog_404_pages'); } else {$user_blog_404_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_tag_pages') != "" )	{	$user_blog_tag_pages = get_option('bp_seo_user_blog_tag_pages'); } else {$user_blog_tag_pages = $bp_seo_default_wp; }
  if ( get_option('bp_seo_user_blog_autor_pages') != "" )	{	$user_blog_autor_pages = get_option('bp_seo_user_blog_autor_pages'); } else {$user_blog_autor_pages = $bp_seo_default_wp; }
?>
	<div class="wrap">
	<!--
	<?php
  	foreach ( (array)$bp as $key => $value ) {
		echo '<pre>';
		echo '<strong>' . $key . ': </strong><br />';
		print_r( $value );
		echo '</pre>';
	}
	?>-->
	<br>
  <h2><?php _e ('Seo for Buddypress: General Seo', 'bp-seo') ?></h2>
  <br>
  <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
  <p><div class="submit"><input type="submit" name="update_bp_seo" value="<?php _e('Update General Seo', 'update_bp_seo') ?>"  style="font-weight:bold;" /></div></p>	
  <ul id="sfbgeneraltable" class="shadetabs">
  <?php if ( defined( 'BP_VERSION' ) ){?>
  <?php	if(in_array('blogs',$bp->active_components) || in_array('groups',$bp->active_components) || in_array('activity',$bp->active_components) || in_array('members',$bp->active_components) || in_array('forums',$bp->active_components)){?>
    <li><a href="#" rel="tab1" class="selected"><?php _e ('Directories', 'bp-seo') ?></a></li>
  <?php }?>
    <li><a href="#" rel="tab2">Profile</a></li> 
  <?php	if(in_array('groups',$bp->active_components)){?>
    <li><a href="#" rel="tab3"><?php _e ('Groups', 'bp-seo') ?></a></li>
  <?php }?>
  <?php } ?>
  <li><a href="#" rel="tab4"><?php _e ('Main Blog', 'bp-seo') ?></a></li>
  <?php if(defined( 'SITE_ID_CURRENT_SITE' )){?>
  <li><a href="#" rel="tab5"><?php _e ('User Blogs', 'bp-seo') ?></a></li>
  <?php } ?>
  </ul>
  <div style="border:1px solid gray; width:681px; margin-bottom: 1em; padding: 10px">
  <div id="tab1" class="tabcontent">
    <div id="tab-head">
    	<div class="sfb-entry">

				<?php _e ('A directory view is the start-page of a component, the overview.
<br>
Most special tags will not work at the directory view, because they make no sense here.<br>
The %%component...%% tags are just for sub-pages of the directories.<br>
<br>
The directory views are the main-pages and that\'s why it\'s important to give every directory a unique title and description.
Write something about every directory on your site so that others will know what kind of directory they will find.
<br><br>
Examples: in the groups directory, write what kind of groups I will find on your social network.
If you are in the members directory, write what kind of members I will find. Are your members musicians, or do they have the same hobbies? repair cars ...?
Give a detailed explanation.

.', 'bp-seo') ?>
			</div>
    </div>
    <div class="spacer"></div>
      <!-- ************ DIRECTORY BLOGS 	-->
      <?php	if(in_array('blogs',$bp->active_components)){?>  
        <?php $lable= array("DIRECTORY BLOGS","directory_blogs_title","directory_blogs","directory_blogs_tags");  ?>
        <?php echo bp_seo_entry($lable,$directory_blogs);?>            
      <?php } ?>
      <!-- ************ DIRECTORY ACTIVITY 	-->
      <?php	if(in_array('activity',$bp->active_components)){?>  
        <?php $lable= array("DIRECTORY ACTIVITY","directory_activity_title","directory_activity","directory_activity_tags");  ?>
        <?php echo bp_seo_entry($lable,$directory_activity);?>
      <?php } ?>
      <!-- ************ DIRECTORY MEMBERS 	-->
      <?php	if(in_array('friends',$bp->active_components)){?>  
        <?php $lable= array("DIRECTORY MEMBERS","directory_members_title","directory_members","directory_members_tags");  ?>
        <?php echo bp_seo_entry($lable,$directory_members);?>   
      <?php } ?>
      <!-- ************ DIRECTORY GROUPS 	-->
      <?php	if(in_array('groups',$bp->active_components)){?>   
        <?php $lable= array("DIRECTORY GROUPS","directory_groups_title","directory_groups","directory_groups_tags");  ?>
        <?php echo bp_seo_entry($lable,$directory_groups);?>   
      <?php }?> 
      <!-- ************ DIRECTORY FORUMS 	-->
      <?php	if(in_array('forums',$bp->active_components)){?>  
        <?php $lable= array("DIRECTORY FORUMS","directory_forums_title","directory_forums","directory_forums_tags");  ?>
        <?php echo bp_seo_entry($lable,$directory_forums); ?>   
      <?php } ?>
   </div>
  <div id="tab2" class="tabcontent">
      <div id="tab-head">
    	<div class="sfb-entry">
				<div class="sfb-entry-title">Special Tags</div>
        <?php
      		$pos = 0;
      		$buddyseo = array
      		(
      			'sitename'          => __( 'The site\'s name', 'bp-seo'),
      			'currentcomponent'  => __( 'Replaced with current component', 'bp-seo'),
  		      'currentaction'     => __( 'Replaced with current action', 'bp-seo'),
      			'usernicename'      => __( 'Replaced with the user\'s nicename', 'bp-seo'),
      			'userregistered'    => __( 'Replaced with the user registered', 'bp-seo'),
      			'displayname'       => __( 'Replaced with the displayed name of the user', 'bp-seo'),
      			'fullname'          => __( 'Replaced with the full name of the user', 'bp-seo'),
      		
      		);
      	?>
      	<table class="widefat">
      
      		<?php foreach ($buddyseo AS $tag => $text) : ?>
      		<tr<?php if ($pos++ % 2 == 1) echo ' class=""' ?>>
      			<th>%%<?php echo $tag; ?>%%</th>
      			<td><?php echo $text; ?></td>
      		</tr>
      		<?php endforeach; ?>
      	</table>
			</div>
    </div>
    <div class="spacer"></div>
      <!-- ************ PROFILE HOME 	-->
      <?php $lable= array("PROFILE HOME","profil_title","profil","profil_tags");  ?>
  		<?php echo bp_seo_entry($lable,$profil); ?>
      <!-- ************ PROFILE BLOGS -->
      <?php	if(in_array('blogs',$bp->active_components)){?>
        <?php $lable= array("PROFILE BLOGS","profil_blogs_title","profil_blogs","profil_blogs_tags");  ?>
  		  <?php echo bp_seo_entry($lable,$profil_blogs); ?>
      <!-- ************ PROFILE BLOGS RECENT POSTS -->
        <?php $lable= array("PROFILE BLOGS RECENT POSTS","profil_blogs_recent_posts_title","profil_blogs_recent_posts","profil_blogs_recent_posts_tags");  ?>
  		  <?php echo bp_seo_entry($lable,$profil_blogs_recent_posts); ?>
      <!-- ************ PROFILE BLOGS -->
        <?php $lable= array("PROFILE BLOGS RECENT COMMENTS","profil_blogs_recent_commments_title","profil_blogs_recent_commments","profil_blogs_recent_commments_tags");  ?>
  		  <?php echo bp_seo_entry($lable,$profil_blogs_recent_commments); ?>
  		<?php } ?>
    	<!-- ************ PROFILE ACTIVITY  -->
    	<?php	if(in_array('activity',$bp->active_components)){?>
        <?php $lable= array("PROFILE ACTIVITY","profil_activity_title","profil_activity","profil_activity_tags");  ?>
		    <?php echo bp_seo_entry($lable,$profil_activity); ?>
		    <?php	if(in_array('friends',$bp->active_components)){?> 
		      <!-- ************ PROFILE ACTIVITY FRIENDS -->
          <?php $lable= array("PROFILE ACTIVITY FRIENDS","profil_activity_friends_title","profil_activity_friends","profil_activity_friends_tags");  ?>
		      <?php echo bp_seo_entry($lable,$profil_activity_friends); ?>
		    <?php } ?>
      <?php } ?>
      <!-- ************ PROFILE FRIENDS  -->
      <?php	if(in_array('friends',$bp->active_components)){?> 
        <?php $lable= array("PROFILE FRIENDS","profil_friends_title","profil_friends","profil_friends_tags");  ?>
        <?php echo bp_seo_entry($lable,$profil_friends); ?>
      <?php } ?>
      <!-- ************ PROFILE GROUPS  -->
      <?php	if(in_array('groups',$bp->active_components)){?>  
        <?php $lable= array("PROFILE GROUPS","profil_groups_title","profil_groups","profil_groups_tags");  ?>
        <?php echo bp_seo_entry($lable,$profil_groups); ?>
      <?php } ?>
      <!-- ************ PROFILE WIRE  -->
      <?php	if(in_array('wire',$bp->active_components)){?>
        <?php $lable= array("PROFILE WIRE","profil_wire_title","profil_wire","profil_wire_tags");  ?>
     	  <?php echo bp_seo_entry($lable,$profil_wire); ?>
     	<?php } ?>
  </div>
  <div id="tab3" class="tabcontent">
        <div id="tab-head">
    	<div class="sfb-entry">
				<div class="sfb-entry-title"><?php _e ('Special Tags', 'bp-seo') ?></div>
	<?php
		$pos = 0;
		$buddyseo = array
		(
			'sitename'                   => __( 'The site\'s name', 'bp-seo'),
			'currentcomponent'           => __( 'Replaced with current component', 'bp-seo'),
			'currentaction'              => __( 'Replaced with current action', 'bp-seo'),
			'componentname'              => __( 'Replaced with component name', 'bp-seo'),
			'componentid'                => __( 'Replaced with the component ID', 'bp-seo'),
			'componentdescription'       => __( 'Replaced with component description', 'bp-seo'),
			'componentstatus'            => __( 'Replaced with the component status', 'bp-seo'),
			'componentdatecreated'       => __( 'Replaced with the component date created', 'bp-seo'),
			'componentadmins'            => __( 'Replaced with the component admins', 'bp-seo'),
			'componenttotalmembercount'  => __( 'Replaced with the component total member-count', 'bp-seo'),
			'componentlastactivity'      => __( 'Replaced with the component last activity', 'bp-seo'),
			'forumtopictitle'           => __( 'Replaced with current forum topic title', 'bp-seo'),
			'forumtopicpostername'           => __( 'Replaced with current forum topic poster name', 'bp-seo'),
			'forumtopiclastpostername'           => __( 'Replaced with current forum topic last poster name', 'bp-seo'),
			'forumtopicstarttime'           => __( 'Replaced with current forum topic start time', 'bp-seo'),
			'forumtopictime'           => __( 'Replaced with current forum topic time', 'bp-seo'),
			'forumtopictext'           => __( 'Replaced with current forum topic text', 'bp-seo'),	
		);
	?>
	<table class="widefat">

		<?php foreach ($buddyseo AS $tag => $text) : ?>
		<tr<?php if ($pos++ % 2 == 1) echo ' class=""' ?>>
			<th>%%<?php echo $tag; ?>%%</th>
			<td><?php echo $text; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
			</div>
    </div>
    <div class="spacer"></div>
      <!-- ************ GROUPS HOME  -->
      <?php $lable= array("GROUPS HOME","groups_home_title","groups_home","groups_home_tags");  ?>
   		<?php echo bp_seo_entry($lable,$groups_home); ?>
      <!-- ************ GROUPS WIRE  -->
      <?php	if(in_array('wire',$bp->active_components)){?>
        <?php $lable= array("GROUPS WIRE","groups_wire_title","groups_wire","groups_wire_tags");  ?>
   		  <?php echo bp_seo_entry($lable,$groups_wire); ?>
   		<?php } ?>
      <!-- ************ GROUPS FORUM  -->
      <?php	if(in_array('forums',$bp->active_components)){?>
        <?php $lable= array("GROUPS FORUM","groups_forum_title","groups_forum","groups_forum_tags");  ?>
        <?php echo bp_seo_entry($lable,$groups_forum); ?>
        <!-- ************ GROUPS FORUM TOPIC  -->
        <?php $lable= array("GROUPS FORUM TOPIC","groups_forum_topic_title","groups_forum_topic","groups_forum_topic_tags");  ?>
        <?php echo bp_seo_entry($lable,$groups_forum_topic); ?>
      <?php } ?>
      <?php	if(in_array('friends',$bp->active_components)){?>
        <!-- ************ GROUPS MEMBERS  -->
        <?php $lable= array("GROUPS MEMBERS","groups_members_title","groups_members","groups_members_tags");  ?>
        <?php echo bp_seo_entry($lable,$groups_members); ?>
      <?php } ?>  
  </div>
  <div id="tab4" class="tabcontent">
     <div id="tab-head">
    	<div class="sfb-entry">
				<div class="sfb-entry-title"><?php _e ('Special Tags', 'bp-seo') ?></div>
    	<?php
    		$pos = 0;
    		$buddyseo = array
    		(
    			'sitename'                   => __( 'The site\'s name', 'bp-seo'),
    			'currenttime'          => __( 'Replaced with the current time', 'bp-seo'),
    			'currentdate'          => __( 'Replaced with the current date', 'bp-seo'),
    			'currentmonth'         => __( 'Replaced with the current month', 'bp-seo'),
    			'currentyear'          => __( 'Replaced with the current year', 'bp-seo'),
    		);
    	?>
    	<table class="widefat">
    
    		<?php foreach ($buddyseo AS $tag => $text) : ?>
    		<tr<?php if ($pos++ % 2 == 1) echo ' class=""' ?>>
    			<th>%%<?php echo $tag; ?>%%</th>
    			<td><?php echo $text; ?></td>
    		</tr>
    		<?php endforeach; ?>
    	</table>
			</div>
    </div>
    <div class="spacer"></div>
      <!-- ************ MAIN BLOG HOME  -->
      <?php $lable= array("MAIN BLOG HOME","main_blog_start_title","main_blog_start","main_blog_start_tags");  ?>
     	<?php echo bp_seo_entry($lable,$main_blog_start); ?>
     	
      <?php if(defined( 'BP_VERSION' )){?>  
     	<!-- ************ MAIN BLOG ACTIVITY HOME -->
      <?php $lable= array("MAIN BLOG ACTIVITY HOME","main_blog_title","main_blog","main_blog_tags");  ?>
     	<?php echo bp_seo_entry($lable,$main_blog); ?>
      <?php } ?>
       <div id="tab-head">
      	<div class="sfb-entry">
  				<div class="sfb-entry-title"><?php _e ('Special Tags for WP and WPMU', 'bp-seo') ?></div>
          <?php _e ('For a list of all available tags go ', 'bp-seo') ?><a href="/wp-admin/admin.php?page=bp_seo_main_page" target="_blank"><?php _e ('here', 'bp-seo') ?></a>.
  			</div>
      </div>
      <div class="spacer"></div>
      <!-- ************ MAIN BLOG ARCHIVE -->
      <?php $lable= array("MAIN BLOG ARCHIVE","main_blog_archiv_title","main_blog_archiv","main_blog_archiv_tags");  ?>
    	<?php echo bp_seo_entry($lable,$main_blog_archiv); ?>
      <!-- ************ MAIN BLOG CATEGORIES -->
      <?php $lable= array("MAIN BLOG CATEGORIES","main_blog_cat_title","main_blog_cat","main_blog_cat_tags");  ?>
    	<?php echo bp_seo_entry($lable,$main_blog_cat); ?>
  		<!-- ************ MAIN BLOG POSTS -->
      <?php $lable= array("MAIN BLOG POSTS","main_blog_posts_title","main_blog_posts","main_blog_posts_tags");  ?>
  		<?php echo bp_seo_entry($lable,$main_blog_posts); ?>

      <!-- ************ MAIN BLOG PAGES -->
      <?php $lable= array("MAIN BLOG PAGES","main_blog_pages_title","main_blog_pages","main_blog_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$main_blog_pages); ?>
      <!-- ************ MAIN BLOG AUTHOR PAGES-->
      <?php $lable= array("MAIN BLOG AUTHOR PAGES","main_blog_autor_pages_title","main_blog_autor_pages","main_blog_autor_pages_tags");  ?>
  	  <?php echo bp_seo_entry($lable,$main_blog_autor_pages); ?>
  		<!-- ************ MAIN BLOG SEARCH PAGES-->
      <?php $lable= array("MAIN BLOG SEARCH PAGES","main_blog_search_pages_title","main_blog_search_pages","main_blog_search_pages_tags");  ?>
  	  <?php echo bp_seo_entry($lable,$main_blog_search_pages); ?>
  		<!-- ************ MAIN BLOG 404 PAGES -->
      <?php $lable= array("MAIN BLOG 404 PAGES","main_blog_404_pages_title","main_blog_404_pages","main_blog_404_pages_tags");  ?>
  	  <?php echo bp_seo_entry($lable,$main_blog_404_pages); ?>
  		<!-- ************ MAIN BLOG TAG PAGES-->
      <?php $lable= array("MAIN BLOG TAG PAGES","main_blog_tag_pages_title","main_blog_tag_pages","main_blog_tag_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$main_blog_tag_pages); ?>
   	  <!-- ************ MAIN BLOG REGISTER PAGES  -->
      <?php $lable= array("MAIN BLOG REGISTER PAGES","main_blog_reg_pages_title","main_blog_reg_pages","main_blog_reg_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$main_blog_reg_pages); ?>
  </div>
  <div id="tab5" class="tabcontent">
    <div id="tab-head">
    	<div class="sfb-entry">
    		<div class="sfb-entry-title"><?php _e ('Info', 'bp-seo') ?></div>
    		<?php _e ('You need to activate the plugin sitewide to run user blogs.', 'bp-seo') ?>
    		<div class="spacer"></div>
			  <div class="sfb-entry-title"><br><?php _e ('Special Tags for WP and WPMU', 'bp-seo') ?></div>
          <br><?php _e ('For a list of all available tags go ', 'bp-seo') ?><a href="/wp-admin/admin.php?page=bp_seo_main_page" target="_blank"><?php _e ('here', 'bp-seo') ?></a>.
  			</div>
   		</div>
    <div class="spacer"></div>
      <!-- ************ USER BLOG HOME  -->
      <?php $lable= array("USER BLOG HOME","user_blog_title","user_blog","user_blog_tags");  ?>
     	<?php echo bp_seo_entry($lable,$user_blog); ?>
  		<!-- ************ USER BLOG ARCHIVE -->
      <?php $lable= array("USER BLOG ARCHIVE","user_blog_archiv_title","user_blog_archiv","user_blog_archiv_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_archiv); ?>
  	  <!-- ************ USER BLOG CATEGORIES -->
      <?php $lable= array("USER BLOG CATEGORIES","user_blog_cat_title","user_blog_cat","user_blog_cat_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_cat); ?>
      <!-- ************ USER BLOG POSTS -->
      <?php $lable= array("USER BLOG POSTS","user_blog_posts_title","user_blog_posts","user_blog_posts_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_posts); ?>
  		<!-- ************ USER BLOG PAGES -->
      <?php $lable= array("USER BLOG PAGES","user_blog_pages_title","user_blog_pages","user_blog_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_pages); ?>
  		<!-- ************ USER BLOG AUTHOR PAGES-->
      <?php $lable= array("USER BLOG AUTHOR PAGES","user_blog_autor_pages_title","user_blog_autor_pages","user_blog_autor_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_autor_pages); ?>
  		<!-- ************ USER BLOG SEARCH PAGES-->
      <?php $lable= array("USER BLOG SEARCH PAGES","user_blog_search_pages_title","user_blog_search_pages","user_blog_search_pages_tags");  ?>
      <?php echo bp_seo_entry($lable,$user_blog_search_pages); ?>
      <!-- ************ USER BLOG 404 PAGES -->
      <?php $lable= array("USER BLOG 404 PAGES","user_blog_404_pages_title","user_blog_404_pages","user_blog_404_pages_tags");  ?>
  		<?php echo bp_seo_entry($lable,$user_blog_404_pages); ?>
  		<!-- ************ USER BLOG TAG PAGES-->
      <?php $lable= array("USER BLOG TAG PAGES","user_blog_tag_pages_title","user_blog_tag_pages","user_blog_tag_pages_tags");  ?>
  	  <?php echo bp_seo_entry($lable,$user_blog_tag_pages); ?>
    </div>
</div>

    <script type="text/javascript">

var general=new ddtabcontent("sfbgeneraltable")
general.setpersist(true)
general.setselectedClassTarget("link") //"link" or "linkparent"
general.init()

</script>
    </fieldset>
  </form>   
</div>
<?php } ?>