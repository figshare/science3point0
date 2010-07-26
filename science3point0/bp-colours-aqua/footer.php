		</div> <!-- #container -->
		<div id="footerreal" style="background-color:#FFF; color:#09C; padding-top:6px;">
            <div>
<script type="text/javascript">
  var uservoiceOptions = {
    key: 'science2point0',
    host: 'science2point0.uservoice.com', 
    forum: '67015',
    lang: 'en',
    showTab: false
  };
  function _loadUserVoice() {
    var s = document.createElement('script');
    s.src = ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js";
    document.getElementsByTagName('head')[0].appendChild(s);
  }
  _loadSuper = window.onload;
  window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };
</script>
            </div>
             
                 <div style="text-align:center; padding-left:20px; float:left; width:200px;">
                     <div style="padding-left:11px;">
                        <img src="<?php echo bloginfo('template_url'); ?>/fist.jpg" />
                     </div>
                    <a href="http://www.science2point0.com/help/" style="color:#09C;"><img src="/wp-content/helpfooter.jpg" /></a><br />
                       <div style="padding-left:10px">
                            <a href="http://creativecommons.org/licenses/by/3.0/" style="color:#09C;">
                            <img src="<?php echo bloginfo('template_url'); ?>/scientif.png" style="padding-bottom:6px; padding-top:6px;" />
                            </a><br />
                      
             </div>
                    <div style="padding-left:10px">
                        <a href="#" onclick="UserVoice.Popin.show(uservoiceOptions); return false;">
                            <img style="padding-bottom:10px;" src="<?php echo bloginfo('template_url'); ?>/feedback.png" />
                        </a>
                    </div>
            </div>   

             	<div style="float:left; width:200px;text-align:center; padding-left:75px">
                	
                     <div style="text-align:left;"> <span style="font-size:14px; font-weight:bold;padding-bottom:6px;">Pages</span>
 			<ul style="display: inline;list-style-type: none;">
				<li<?php if ( bp_is_front_page() ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'buddypress' ) ?>"><?php _e( 'Home', 'buddypress' ) ?></a>
				</li>

				<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
					<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'buddypress' ) ?>"><?php _e( 'Activity', 'buddypress' ) ?></a>
					</li>
				<?php endif; ?>

				<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'buddypress' ) ?>"><?php _e( 'Members', 'buddypress' ) ?></a>
				</li>

				<?php if ( bp_is_active( 'groups' ) ) : ?>
					<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'buddypress' ) ?>"><?php _e( 'Groups', 'buddypress' ) ?></a>
					</li>

					<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
						<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'buddypress' ) ?>"><?php _e( 'Forums', 'buddypress' ) ?></a>
						</li>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
					<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'buddypress' ) ?>"><?php _e( 'Blogs', 'buddypress' ) ?></a>
					</li>
				<?php endif; ?>

				<?php wp_list_pages( 'title_li=&depth=1&exclude=' . bp_dtheme_page_on_front() ); ?>

				<?php do_action( 'bp_nav_items' ); ?>
			</ul><!-- #nav -->                    
                     </div>

</div>
<div style="text-align:left; padding-left:20px; float:left; width:180px;"><span style="font-size:14px; font-weight:bold;padding-bottom:6px;">Blog Categories<br/></span>
<ul style="display: inline;list-style-type: none;">
                    <a href="http://www.science2point0.com/blog/categoryblogs/" style="color:#09C;">Blogs</a><br />
<a href="http://www.science2point0.com/blog/category/science2-0commentary//" style="color:#09C;">Comment</a><br />
<a href="http://www.science2point0.com/blog/category/development-of-site/" style="color:#09C;">Development of site</a><br />
<a href="http://www.science2point0.com/blog/Category/events/" style="color:#09C;">Events</a><br />
<a href="http://www.science2point0.com/blog/Category/software/" style="color:#09C;">Software</a><br />
<a href="http://www.science2point0.com/blog/Category/talks" style="color:#09C;">Talks</a><br />
<a href="http://www.science2point0.com/blog/Category/videos" style="color:#09C;">Videos</a><br />
<a href="http://www.science2point0.com/blog/Category/websites" style="color:#09C;">Websites</a><br />

</div>


                             	<div style="float:left; width:160px;text-align:center; padding-left:20px;">
                	
                     <div style="text-align:center;"><span style="font-size:14px; font-weight:bold;padding-bottom:6px;">Connect with us <br/></span>
                     <div style="padding-left:1px;">
 <a href="http://twitter.com/science2point0"><img src="/wp-content/twitter.jpg" /></a></div>
                	 <a href="<?php echo bloginfo('rss2_url'); ?>" style="color:#09C; text-decoration:none;"><img src="/wp-content/rssboy.jpg" style="padding-bottom:8px; padding-top:8px;" /></a>              	 
                </div></div>
                <div style="clear:both;"></div>         

        </div> 
		

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<?php wp_footer(); ?>

	</body>

</html>