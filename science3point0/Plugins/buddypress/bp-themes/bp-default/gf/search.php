<h2><?php

$search_term=$_GET['gfs'];
if($search_term=='Search Forum...')
    $search_term='';
_e(sprintf(__("Forum search for  \"%s \" ","gf"),$search_term));?></h2>
<?php if ( gf_has_forum_topics(  ) ) : ?>

	

	<?php do_action( 'bp_before_directory_forums_list' ) ?>
	<div id="discussions">
		
		<table id="latest">
			<tbody><tr>
					<th><?php _e("Topic","gf");?> </th>
					<th><?php _e("Posts","gf");?></th>
							<!-- <th>Voices</th> -->
					<th><?php _e("Last Poster","gf");?></th>
					<th><?php _e("Freshness","gf");?></th>
				</tr>
				<?php while ( gf_forum_topics() ) : gf_the_forum_topic(); ?>
						<tr class="<?php gf_the_topic_css_class() ?>">
							<td> <a class="topic-title" href="<?php gf_the_topic_permalink() ?>" title="<?php gf_the_topic_title() ?> - <?php _e( 'Permalink', 'buddypress' ) ?>"><?php gf_the_topic_title() ?></a></td>
							<td class="num"><?php gf_the_topic_total_posts() ?></td>
							<!-- <td class="num">8</td> -->
							<td class="last-poster"><a href="<?php gf_the_topic_permalink() ?>"><?php gf_the_topic_last_poster_avatar( 'type=thumb&width=20&height=20' ) ?></a>
								<div class="poster-name"><?php gf_the_topic_last_poster_name() ?></div>
							</td>
							<td class="num"><a href="#"><?php gf_the_topic_time_since_last_post() ?></a></td>
						</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
		<div class="nav">
			<div id="post-count" class="pag-count">
					<?php gf_forum_pagination_count() ?>
			</div>

			<div class="pagination-links" id="topic-pag">
				<?php gf_forum_pagination() ?>
			</div>

	</div>						
					
	</div><!-- end of discussion -->
					
	
	<?php do_action( 'bp_after_directory_forums_list' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no forum topics found.', 'gf' ) ?></p>
	</div>

<?php endif;?>