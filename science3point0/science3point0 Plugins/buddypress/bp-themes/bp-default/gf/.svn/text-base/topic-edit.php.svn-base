<?php if ( gf_has_topic_posts() ) : ?>

	<form action="<?php gf_forum_topic_action() ?>" method="post" id="forum-topic-form" class="standard-form">

		<div id="topic-meta">
			<h3><?php gf_the_topic_title() ?> (<?php gf_the_topic_total_post_count() ?>)</h3>
                        <a class="button" href="<?php gf_forum_permalink() ?>/">&larr; <?php _e( 'Back to Forum', 'gf' ) ?></a> &nbsp; <a class="button" href="<?php echo gf_get_current_topic_permalink() ?>/"><?php _e( 'Cancel', 'gf') ?></a></span>

			<?php if ( gf_current_user_can_admin() || gf_current_user_can_mod() || gf_get_the_topic_is_mine() ) : ?>
				<div class="admin-links"><?php gf_the_topic_admin_links() ?></div>
			<?php endif; ?>
		</div>

		

			<?php if ( gf_is_edit_topic() ) : ?>

				<div id="edit-topic">

					<?php do_action( 'gf_before_edit_forum_topic' ) ?>

					<p><strong><?php _e( 'Edit Topic:', 'gf' ) ?></strong></p>

					<label for="topic_title"><?php _e( 'Title:', 'gf' ) ?></label>
					<input type="text" name="topic_title" id="topic_title" value="<?php gf_the_topic_title() ?>" />

					<label for="topic_text"><?php _e( 'Content:', 'gf' ) ?></label>
					<textarea name="topic_text" id="topic_text"><?php gf_the_topic_text() ?></textarea>

					<?php do_action( 'gf_after_edit_forum_topic' ) ?>

					<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php _e( 'Save Changes', 'gf' ) ?>" /></p>

					<?php wp_nonce_field( 'gf_forums_edit_topic' ) ?>

				</div>

			<?php else : ?>

				<div id="edit-post">

					<?php do_action( 'gf_before_edit_forum_post' ) ?>

					<p><strong><?php _e( 'Edit Post:', 'buddypress' ) ?></strong></p>

					<textarea name="post_text" id="post_text"><?php gf_the_topic_post_edit_text() ?></textarea>

					<?php do_action( 'gf_after_edit_forum_post' ) ?>

					<p class="submit"><input type="submit" name="save_changes" id="save_changes" value="<?php _e( 'Save Changes', 'gf' ) ?>" /></p>

					<?php wp_nonce_field( 'gf_forums_edit_post' ) ?>

				</div>

			<?php endif; ?>

		

	</form>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This topic does not exist.', 'gf' ) ?></p>
	</div>

<?php endif;?>