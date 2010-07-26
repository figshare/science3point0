<?php get_header() ?>
	<div id="content">
		<div class="padder">
			<h2><?php _e('Add New Post'); ?></h2>
			<?php do_action( 'template_notices' ) // (error/success feedback) ?>
			<p><?php _e('Use this form to quickly add a post to one of your blogs.','oqp');?><br/>
			<?php _e('If you want more control, add or edit posts using your blog\'s Dashboard !','oqp');?>
			</p>
			<div id="oqp">
				<?php do_action( 'oqp_before_creation_form' ) ?>
				<?php oqp_switch_blog_form();?>
				<?php oqp_creation_form();?>
				<?php do_action( 'oqp_after_creation_form' ) ?>
			</div>
		</div>
	</div>
<?php get_footer() ?>