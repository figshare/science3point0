<a  name="new_form"> </a>
<h3 class="post-form"><?php _e("Add New Topic","gf");?></h3>
<form name="new_topic_form" action="" class="standard-form postform" method="post">
	<p id="post-form-title-container">
		<label for="topic"><?php _e('Title'); ?>
			<input type="text" name="topic_title" id="topic" size="50" maxlength="80" tabindex="1" />
		</label>
	</p>

<p id="post-form-post-container">
	<label for="post_content"><?php _e('Post',"gf"); ?>
	<textarea name="topic_text" col="48" rows="10" tabindex="2"></textarea>
	</label>
</p>
<p id="post-form-tags-container">
	<label for="tags-input"><?php printf(__('Tags (comma seperated)','gf'), bb_get_tag_page_link()) ?>
		<input type="text" tabindex="3" value="" maxlength="100" size="50" name="topic_tags" id="tags-input" gtbfieldid="44">
	</label>
</p>
<p id="post-form-forum-container">
	<label for="forum-id"><?php _e('Forum','gf'); ?>
	<?php echo gf_get_forum_dropdown(gf_get_root_forum_id());?>
</label>
</p>	

<?php wp_nonce_field("gf_create_topic");?>
<input type="submit" name="submit_topic" value="<?php _e('Create new topic','gf');?> "/>
</form>