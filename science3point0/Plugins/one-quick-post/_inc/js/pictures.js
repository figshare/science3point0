jQuery(document).ready(function() {
	var j = jQuery;
	
	var gallery = j('#classified-gallery');
	var new_content = classified_pictures_add_edit_links(gallery);
	gallery.html(new_content);
	
	j('#classified-pictures-upload').click(function() {
		classifieds_pictures_thickbox();
	});
	
});