<?php 
global $current_user;
global $oqp_args;
global $oqp_post;
?>

<div id="oqp_post_<?php echo $oqp_post->ID;?>" class="oqp_block" rel="<?php echo $oqp_post->ID;?>">
	<?php do_action( 'template_notices' ) ?>
	<form id="oqp_form_<?php echo $oqp_args['form_id'];?>" class="standard-form oqp-form" method="post" name="oqp_form_<?php echo $oqp_args['form_id'];?>">
		<?php do_action('oqp_creation_form_before_fields');?>
		<?php if (!is_user_logged_in()) {?>
			<p>
				<label for="oqp_dummy_name"><?php _e('Name');?></label>
				<input type="text" name="oqp_dummy_name" id="oqp_dummy_name" value="<?php echo $dummy_name;?>"/>
			</p>
			<p>
				<label for="oqp_dummy_email"><?php _e('Email');?></label>
				<input type="text" name="oqp_dummy_email" id="oqp_dummy_email" value="<?php echo $dummy_email;?>"/>
			</p>
		<?php } ?>
		
		<p>
			<label for="oqp_title"><?php _e('Title');?></label>
			<input type="text" name="oqp_title" id="oqp_title" value="<?php echo $oqp_post->post_title;?>"/>
		</p>
		
		<p>
			<label for="oqp_desc"><?php _e('Description');?>
			<?php
			if (!$oqp_args['tiny_mce']) {?>
			<small>- <em><?php _e('HTML allowed','oqp');?></em></small>
			<?php } else {
			?>
				<span class="generic-button">
					<a href="#" class="toggleVisual"><?php _e('Visual');?></a>
					<a href="#" class="toggleHTML"><?php _e('HTML');?></a>
				</span>

			<?php
			}?>
			</label>
			
			<textarea name="oqp_desc" id="oqp_desc" rows="8" col="45"><?php echo $oqp_post->post_content;?></textarea>
		</p>
		<?php oqp_form_taxonomies_html($oqp_args['taxonomies']);?>

		<p>
			<?php 
			if (!$oqp_post->ID) {
				wp_nonce_field( 'oqp-new-post-blog-'.$oqp_args['blog_id'] );
				$button_text=__('Publish');
			}else {
				wp_nonce_field( 'oqp-edit-post'.$oqp_post->ID.'-blog-'.$oqp_args['blog_id'] );
				$button_text=__('Update');
				?>
				<input type="hidden" name="oqp-post-id" value="<?php echo $oqp_post->ID;?>"/>
				<input id="delete" type="submit" value="<?php _e('Trash');?>"/>
				<?php
			}
			if ($oqp_args['blog_id']) {
			?>
				<input type="hidden" name="oqp-blog-id" value="<?php echo $oqp_args['blog_id'];?>"/>
			<?php }?>
			
			<input type="hidden" name="oqp-action" value="oqp-save"/>
			<input type="hidden" name="oqp-form-id" value="<?php echo $oqp_args['form_id'];?>"/>
			<?php do_action('oqp_creation_form_after_fields');?>
			<?php //if ($user_id) {?>
				<div class="submit" id="previous-next">
					<?php /* Previous Button */ ?>
					<?php if ( !yclads_bp_is_first_ad_creation_step() ) : ?>
						<input type="button" value="&larr; <?php _e('Previous Step', 'buddypress') ?>" id="ad-creation-previous" name="previous" onclick="location.href='<?php yclads_bp_ad_creation_previous_link() ?>'" />
					<?php endif; ?>

					<?php /* Next Button */ ?>
					<?php if ( !yclads_bp_is_last_ad_creation_step() && !yclads_bp_is_first_ad_creation_step() ) : ?>
						<input type="submit" value="<?php _e('Next Step', 'buddypress') ?> &rarr;" id="ad-creation-next"/>
					<?php endif;?>

					<?php /* Create Button */ ?>
					<?php if ( yclads_bp_is_first_ad_creation_step() ) : ?>
						<input type="submit" value="<?php _e('Create Ad and Continue', 'yclads') ?> &rarr;" id="ad-creation-create"/>
					<?php endif; ?>

					<?php /* Finish Button */ ?>
					<?php if ( yclads_bp_is_last_ad_creation_step() ) : ?>
						<input type="submit" value="<?php _e('Finish', 'buddypress') ?> &rarr;" id="ad-creation-finish"/>
					<?php endif; ?>
				</div>
			<?php //}?>
		</p>
		
	</form>
	
	
</div>