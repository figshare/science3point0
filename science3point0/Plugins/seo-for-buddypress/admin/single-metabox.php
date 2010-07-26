<?php
function seo4all_metabox(){
	global $post;
	
	$title=get_seo4all_title();
	$description=get_seo4all_description();
	$keywords=get_seo4all_keywords();

?>
<style type="text/css">
#seo4all_title, #seo4all_description, #seo4all_keywords{
	width:99%;
}
</style>
<div id="seo4all" class="postbox">
	<div class="handlediv" title="<?php _e('klick'); ?>">
		<br />
	</div>
	<h3 class="hndle"><?php _e('SEO settings')?></h3>
	<div class="inside">
		<p>
			<label for="seo4all_title"><?php _e('Title')?>:</label>
			<input type="text" name="seo4all_title" id="seo4all_title" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="seo4all_description"><?php _e('Description')?>:</label>
			<input type="text" name="seo4all_description" id="seo4all_description" value="<?php echo $description; ?>" />
		</p>
		<p>
			<label for="seo4all_keywords"><?php _e('Keywords')?>:</label>
			<input type="text" name="seo4all_keywords" id="seo4all_keywords" value="<?php echo $keywords; ?>" />
		</p>
	</div>	
</div>
<?php 
}
?>