<?php if ( gf_has_topic_posts() ) : ?>
	
<div class="bcomb"><?php echo gf_get_forum_bread_crumb();?></div>
		<div class="pagination no-ajax">
                   
                
			<div id="post-count" class="pag-count">
				<?php gf_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag">
				<?php gf_the_topic_pagination() ?>
			</div>
                        <br class="clear" />
		</div>

		<div id="topic-meta">
			<h3><?php gf_the_topic_title() ?> (<?php gf_the_topic_total_post_count() ?>)</h3>
                        <?php if(gf_get_the_topic_tag_count())  gf_the_topic_tags();
                         gf_show_add_tag_form();
                                                ?>
			<?php if ( gf_current_user_can_admin() || gf_current_user_can_mod() || gf_get_the_topic_is_mine() ) : ?>
				<div class="admin-links"><?php gf_the_topic_admin_links() ?> | <?php echo gf_get_add_remove_fav_link()?></div>
			<?php endif; ?>
		</div>

		<ul id="topic-post-list" class="item-list">
			<?php while ( gf_topic_posts() ) : gf_the_topic_post(); ?>

				<li id="post-<?php gf_the_topic_post_id() ?>">
					<div class="poster-meta">
						<a href="<?php gf_the_topic_post_poster_link() ?>">
							<?php gf_the_topic_post_poster_avatar( 'width=40&height=40' ) ?>
						</a>
						<?php echo sprintf( __( '%s said %s ago:', 'gf' ), gf_get_the_topic_post_poster_name(), gf_get_the_topic_post_time_since() ) ?>
					</div>

					<div class="post-content">
						<?php gf_the_topic_post_content() ?>
					</div>

					<div class="admin-links">
						<?php if ( gf_current_user_can_admin() || gf_current_user_can_mod() || gf_get_the_topic_post_is_mine() ) : ?>
							<?php gf_the_topic_post_admin_links() ?>
						<?php endif; ?>
						<a href="#post-<?php gf_the_topic_post_id() ?>" title="<?php _e( 'Permanent link to this post', 'gf' ) ?>">#</a>
					</div>
				</li>

			<?php endwhile; ?>
		</ul>

		<div class="pagination no-ajax">

			<div id="post-count" class="pag-count">
				<?php gf_the_topic_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag">
				<?php gf_the_topic_pagination() ?>
			</div>

		</div>
<form action="<?php gf_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form">
		<?php if ( is_user_logged_in()&&gf_current_user_can_post()  ) : ?>

			<?php if ( gf_get_the_topic_is_last_page() ) : ?>

				<?php if ( gf_get_the_topic_is_topic_open() ) : ?>

					<div id="post-topic-reply">
						<p id="post-reply"></p>

						

						

						<h4><?php _e( 'Add a reply:', 'gf' ) ?></h4>

						<textarea name="reply_text" id="reply_text"></textarea>

						<div class="submit">
							<input type="submit" name="submit_reply" id="submit" value="<?php _e( 'Post Reply', 'gf' ) ?>" />
						</div>

						<?php do_action( 'gf_forum_new_reply_after' ) ?>

						<?php wp_nonce_field( 'gf_forums_new_reply' ) ?>
					</div>

				<?php else : ?>

					<div id="message" class="info">
						<p><?php _e( 'This topic is closed, replies are no longer accepted.', 'gf' ) ?></p>
					</div>

				<?php endif; ?>

			<?php endif; ?>
			<?php endif; ?>

		

	</form>
<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There are no posts for this topic.', 'gf' ) ?></p>
	</div>

<?php endif;?>