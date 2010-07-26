<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<?php do_action( 'bp_before_postsonprofile_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">
                <?php
                global $more;
                $more = 0; // set $more to 0 in order to only get the first part of the post
                query_posts('&author='. bp_displayed_user_id() .'&paged='.  bp_pop_cur_page());
                if (have_posts()) {
                    while (have_posts()) : the_post(); ?>
                        <?php do_action( 'bp_before_blog_post' ) ?>

					    <div class="post" id="post-<?php the_ID(); ?>">

						    <div class="author-box">
							    <?php echo get_avatar( get_the_author_meta( 'user_email' ), '50' ); ?>
							    <p><?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></p>
						    </div>

						    <div class="post-content">
							    <h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permanent Link to', 'buddypress' ) ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

							    <p class="date"><?php the_time('F j, Y') ?> <em><?php _e( 'in', 'buddypress' ) ?> <?php the_category(', ') ?> <?php printf( __( 'by %s', 'buddypress' ), bp_core_get_userlink( $post->post_author ) ) ?></em></p>

							    <div class="entry">
								    <?php the_content( __( 'Read the rest of this entry &rarr;', 'buddypress' ) ); ?>
							    </div>

							    <p class="postmetadata"><span class="tags"><?php the_tags( __( 'Tags: ', 'buddypress' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', 'buddypress' ), __( '1 Comment &#187;', 'buddypress' ), __( '% Comments &#187;', 'buddypress' ) ); ?></span></p>
						    </div>

					    </div>

					<?php do_action( 'bp_after_blog_post' ) ?>

                <?php
                    endwhile;
                    if (function_exists('wp_pagenavi')) wp_pagenavi();
                } else {
                    echo '<div class="info" id="message"><p>';
                    _e('There is no posts.', 'bp-postsonprofile');
                    echo '</p></div>';
                }
                ?>
                
				<?php do_action( 'bp_after_postsonprofile_body' ) ?>                

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_postsonprofile_content' ) ?>
			

		</div><!-- .padder -->
	</div><!-- #content -->
	<?php locate_template( array( 'sidebar.php' ), true ) ?>
<?php get_footer() ?>
