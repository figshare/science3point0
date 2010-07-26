<?php get_header() ?>

	<div id="content">
            <?php do_action("template_notices");?>
		<div class="padder">
                    <h3><?php _e( 'Forums', 'gf' ) ?></h3>
			<?php gf_forums_search_form();?>
			<br class="clear" />
			<div class="forum-header-links">
				<ul>
                                    <li <?php if ( gf_is_front()&&!gf_is_view('favorites')&&!gf_is_my_topics()) : ?> class="selected"<?php endif; ?> ><a href="<?php echo gf_get_home_url() ?>"><?php _e("Forums","gf");?></a></li>

					<?php if ( is_user_logged_in() && gf_current_user_can_admin()) : ?>
						<li <?php if(gf_is_admin()):?> class="selected" <?php endif;?> ><a href="<?php echo gf_get_admin_link(); ?>"><?php _e("Admin","gf"); ?></a></li>
					<?php endif; ?>
                                         <?php if ( is_user_logged_in() ) : ?>
                                                <li <?php if(gf_is_my_topics()):?> class="selected" <?php endif;?> ><a href="<?php echo gf_get_my_topics_link(); ?>"><?php _e("My Topics","gf"); ?></a></li>
					<?php endif; ?>
                                                <?php if ( is_user_logged_in() ) : ?>
                                                <li <?php if(gf_is_view("favorites")):?> class="selected" <?php endif;?> ><a href="<?php echo gf_get_my_favorites_link(); ?>"><?php _e("My Favorites","gf"); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
			<br class="clear" />
				
			<div id="forums-content" class="forums">
				<?php 
			if(gf_is_forum_admin())
				locate_template(array("gf/admin/home.php"),true);
			else if(gf_is_forum_topic_edit())
				locate_template(array("gf/topic-edit.php"),true);
			else if(gf_is_topic())
				locate_template(array("gf/topic.php"),true);
                        else if(gf_is_tag())
                            locate_template(array("gf/tag.php"),true);
                        else if(gf_is_search())
                            locate_template(array("gf/search.php"),true);
			else if(gf_is_view())
                            locate_template(array("gf/view.php"),true);
                        else if(gf_is_my_topics())
                            locate_template(array("gf/my-topics.php"),true);
                        else
                            locate_template(array("gf/forum.php"),true);
                        ?>
			</div>

			<?php do_action( 'gf_directory_forums_content' ) ?>

			

			<?php do_action( 'gf_after_directory_forums_content' ) ?>

		

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>