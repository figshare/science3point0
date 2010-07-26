<?php 

### Main overview page.


function bp_seo_main_page() { 
	global $bp; 
?><div class="wrap">
  <br>
  <h2>Seo for Buddypress</h2>
  <br><br>
  <ul id="sfbmaintable" class="shadetabs">
      <li><a href="#" rel="tab1" class="selected"><?php _e ('Welcome', 'bp-seo') ?></a></li>
      <li><a href="#" rel="tab2"><?php _e ('Special Tags', 'bp-seo') ?></a></li>
      <li><a href="#" rel="tab3"><?php _e ('Help and Info', 'bp-seo') ?></a></li>
  </ul>
  <div style="border:1px solid gray; width:681px; margin-bottom: 1em; padding: 10px">
  <div id="tab1" class="tabcontent">
    <div id="tab-head">
    	<div class="sfb-entry">
	       <div class="sfb-entry-title"><?php _e ('Seo for WP/WPMU and Buddypress', 'bp-seo') ?></div>
	       <p><?php _e ('Search engine optimization for Wordpress single | Wordpress MU | Buddypress and plugins.', 'bp-seo') ?></p>
			   <h3><a href="/wp-admin/admin.php?page=bp_seo_general_page"><?php _e ('General Seo', 'bp-seo') ?></a></h3>
	       <p><?php _e ('This is the place for the general meta(title, description, keywords) optimization in a WP | WPMU | Buddypress environment. The meta options shown in this page will be generated depending on the WP you use and the activated Buddypress components.', 'bp-seo') ?></p>
			   <h3><a href="/wp-admin/admin.php?page=bp_seo_plugins"><?php _e ('Plugins Seo', 'bp-seo') ?></a></h3>
	       <p><?php _e ('If you have installed plugins to extend Buddypress, this is the place where to configure the Seo behavior of every component. First you need to select where the component is shown in the front-end. After saving, you will be able to enter all meta data depending on your selection.', 'bp-seo') ?> </p>
			   <h3><a href="wp-admin/admin.php?page=bp_seo_settings"><?php _e ('Settings', 'bp-seo') ?></a></h3>
         <p><?php _e ('Settings page for the global Seo configuration, update and delete Seo for Buddypress.', 'bp-seo') ?></p>
			   
         <b><?php _e ('If you use Seo for Buddypress, please support the development with a donation', 'bp-seo') ?></b>
<br>
<p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="NWEYBQUNE5PVY">
<input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>
</p>
      </div>
    </div>
    <div class="spacer"></div>
  </div>
  <div id="tab2" class="tabcontent">
    <div id="tab-head">
      <div class="sfb-entry">
	<div class="sfb-entry-title"><?php _e ('Special Tags', 'bp-seo') ?></div>
	<p><?php _e ('Special Tags are place holders to use in the meta data to specifay the content. Not all Tags are avalibe everywere. Use every tag with care, and check if the tag is suportet and gife you back the result you wanted.', 'bp-seo'); ?></p>
  <br>
  <div class="sfb-entry-title"><?php _e ('Special Tags for WP and WPMU', 'bp-seo') ?></div>
	<p><?php _e ('These tags can be included and will be replaced when the main/user blog pages or posts are displayed.', 'bp-seo'); ?></p>
	
	<?php
		$pos = 0;
		$wpseo = array
		(
			'date'                 => __( 'Replaced with the date of the post/page', 'bp-seo'),
			'title'                => __( 'Replaced with the title of the post/page', 'bp-seo'),
			'sitename'             => __( 'The site\'s name', 'bp-seo'),
			'excerpt'              => __( 'Replaced with the post/page excerpt', 'bp-seo'),
			'tag'                  => __( 'Replaced with the current tag/tags', 'bp-seo'),
			'category'             => __( 'Replaced with the post categories (comma separated)', 'bp-seo'),
			'category_description' => __( 'Replaced with the category description', 'bp-seo'),
			'tag_description'      => __( 'Replaced with the tag description', 'bp-seo'),
			'term_description'     => __( 'Replaced with the term description', 'bp-seo'),
			'term_title'           => __( 'Replaced with the term name', 'bp-seo'),
			'modified'             => __( 'Replaced with the post/page modified time', 'bp-seo'),		
			'id'                   => __( 'Replaced with the post/page ID', 'bp-seo'),
			'name'                 => __( 'Replaced with the post/page author\'s \'nicename\'', 'bp-seo'),
			'userid'               => __( 'Replaced with the post/page author\'s user ID', 'bp-seo'),
			'searchphrase'         => __( 'Replaced with the current search phrase', 'bp-seo'),
			'currenttime'          => __( 'Replaced with the current time', 'bp-seo'),
			'currentdate'          => __( 'Replaced with the current date', 'bp-seo'),
			'currentmonth'         => __( 'Replaced with the current month', 'bp-seo'),
			'currentyear'          => __( 'Replaced with the current year', 'bp-seo'),
			'page'                 => __( 'Replaced with the current page number (i.e. page 2 of 4)', 'bp-seo'),
			'pagetotal'            => __( 'Replaced with the current page total', 'bp-seo'),
			'pagenumber'           => __( 'Replaced with the current page number', 'bp-seo'),
			'caption'              => __( 'Attachment caption', 'bp-seo')
		);
	?>
	<table class="widefat">

		<?php foreach ($wpseo AS $tag => $text) : ?>
		<tr<?php if ($pos++ % 2 == 1) echo ' class=""' ?>>
			<th>%%<?php echo $tag; ?>%%</th>
			<td><?php echo $text; ?></td>
		</tr>
		<?php endforeach; ?>
	</table><br>
	<div class="sfb-entry-title"><?php _e ('Special Tags for Buddypress', 'bp-seo') ?></div>
	<p><?php _e ('These tags can be included and will be replaced when a Buddypress page is displayed.', 'bp-seo'); ?></p>
	
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
			'userid'                     => __( 'Replaced with the user ID', 'bp-seo'),
			'usernicename'               => __( 'Replaced with the user\'s nicename', 'bp-seo'),
			'userregistered'             => __( 'Replaced with the user registered', 'bp-seo'),
			'displayname'                => __( 'Replaced with the displayed name of the user', 'bp-seo'),
			'fullname'                   => __( 'Replaced with the full name of the user', 'bp-seo'),
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
  </div>
  <div id="tab3" class="tabcontent">
    <div id="tab-head">
      <div class="sfb-entry">
      <div class="sfb-entry-title"><?php _e ('Help and Info', 'bp-seo'); ?></div><br>
      <div><?php _e ('If you have any problems, need help or find a bug,
      <br>please have a look into the following places:', 'bp-seo'); ?></div><br>
        <a href="http://sven-lehnert.de/en/2009/04/29/buddypress-plugin-seo-for-buddypress/" target="_blank"><?php _e ('Plugin page', 'bp-seo'); ?></a><br>
        <a href="http://buddypress.org/forums/topic/seo-for-buddypress-10-beta" target="_blank"><?php _e ('Buddypress forum', 'bp-seo'); ?></a>
        <br><br>

        <div class="spacer"></div>
        <div class="sfb-entry-title"><?php _e ('What\'s coming in the next version?', 'bp-seo'); ?></div>
        <?php _e ('I\'m thinking about to add the following features into the next versions:', 'bp-seo'); ?><br>
        <?php _e ('- Nofollow control', 'bp-seo'); ?><br>
        <?php _e ('- Image-title and alt-tag management', 'bp-seo'); ?><br>
        <?php _e ('- Letter and word counter for the meta edit pages', 'bp-seo'); ?><br>
        <?php _e ('- Add meta description in the post edit screen', 'bp-seo'); ?><br><br>
        <?php _e ('If you have any suggestions, please contact me here.', 'bp-seo'); ?> <br>
        <?php _e ('I\'m always looking forward to improve "Seo for Buddypress".', 'bp-seo'); ?> <br>
      </div>
    </div>
    <div class="spacer"></div>
  </div>
  </div>
<script type="text/javascript">
var main=new ddtabcontent("sfbmaintable")
main.setpersist(true)
main.setselectedClassTarget("link") //"link" or "linkparent"
main.init()
</script>

</div>
<?php } ?>