<?php get_header();?>
	<div id="content">
		<div class="padder">
		<?php 
			$post = bp_quickpress_get_quickpress_post();
			if ($post) :
				setup_postdata($post) 
				?>
				<h2><?php _e('Edit Post');?> <em>#<?php the_ID();?></em></h2>
				<?php do_action( 'template_notices' ) // (error/success feedback) ?>

					<div id="quickpress">
					<?php do_action( 'bp_quickpress_before_edition_form' ) ?>
					<?php bp_quickpress_edition_form();?>
					<?php do_action( 'bp_quickpress_after_edition_form' ) ?>
					</div>

			<?php else : ?>
			<h2 class="center"><?php _e( 'Not Found', 'buddypress' ) ?></h2>
				<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'buddypress' ) ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php get_footer() ?>