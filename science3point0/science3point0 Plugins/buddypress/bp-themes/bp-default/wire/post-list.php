<?php do_action( 'bp_before_wire_post_list_content' ) ?>
	<?php do_action( 'bp_before_wire_post_list_form' ) ?>

	<?php if ( bp_has_wire_posts( 'item_id=' . bp_get_wire_item_id() . '&can_post=' . bp_wire_can_post() ) ) : ?>
		<?php if(!(bp_is_user_profile()&&bp_is_home())):?>
			<?php bp_wire_get_post_form() ?>
		<?php endif;?>
			
	<div class="bp-widget">
		<h4><?php bp_wire_title() ?> <span><a href="<?php bp_wire_see_all_link() ?>"><?php _e( "See All", "buddypress" ) ?> &rarr;</a></span></h4>

		<div id="wire-post-list-content">

		<?php if ( bp_wire_needs_pagination() ) : ?>
				<div class="pagination">

					<div id="wire-count" class="pag-count">
						<?php bp_wire_pagination_count() ?> &nbsp;
						<span class="ajax-loader"></span>
					</div>
		
				<div id="wire-pagination" class="pagination-links">
					<?php bp_wire_pagination() ?>
				</div>
				<br class='clear' />
			</div>
		<?php endif; ?>

		<?php do_action( 'bp_before_wire_post_list' ) ?>
				
		<ul id="wire-post-list" class="item-list">
		<?php while ( bp_wire_posts() ) : bp_the_wire_post(); ?>
			
				<li>
				<?php do_action( 'bp_before_wire_post_list_metadata' ) ?>
				<div class="item-avatar">
					<?php bp_wire_post_author_avatar("height=64&width=64") ?>
				</div>
				<div class='wire-content'>
					<div class="wire-post-metadata">
					<span class='wire-author'>
						<?php echo bp_get_wire_post_author_name();?> :
					</span>
					<span class='wire-date-del'>
						<?php printf ( __( '%1$s', "buddypress" ), bp_get_wire_post_date()) ?>
						<?php bp_wire_delete_link() ?>
					</span>
					<?php do_action( 'bp_wire_post_list_metadata' ) ?>
					
				</div>
				
				<?php do_action( 'bp_after_wire_post_list_metadata' ) ?>
				<?php do_action( 'bp_before_wire_post_list_item' ) ?>
				
				<div class="wire-post-content">
					<?php bp_wire_post_content() ?>
					
					<?php do_action( 'bp_wire_post_list_item' ) ?>
				</div>
				</div>
				<?php do_action( 'bp_after_wire_post_list_item' ) ?>
				<br class="clear" />
			</li>
			
		<?php endwhile; ?>
		</ul>
		
		<?php do_action( 'bp_after_wire_post_list' ) ?>
	
	</div>
</div>


	<?php else: ?>
<?php bp_wire_get_post_form() ?>
		
		
<div id="message" class="error">
	<p><?php bp_wire_no_posts_message() ?></p>
</div>
	
<?php endif;?>
	
<?php do_action( 'bp_after_wire_post_list_form' ) ?>

			


<?php do_action( 'bp_after_wire_post_list_content' ) ?>
