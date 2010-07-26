jQuery(document).ready(function() {
	var j = jQuery;
	var gallery_block;
	/*
	var gallery = j('#classified-gallery');
	var new_content = classified_pictures_add_edit_links(gallery);
	gallery.html(new_content);
	*/
	
	//Thickbox for edit pictures links
	j('a.oqp-pictures-upload,a.edit-oqp-gallery-pic').livequery('click',function() {
		gallery_block = j(this).parents('.oqp_gallery_block');
		tb_show('', j(this).attr('href'));
		return false;
	});
	
	//append edit links to every OQP gallery thumb
	j('.oqp_gallery_block').each( function() {
		oqp_gallery_add_edit_links(j(this));
	});
	
	//watch if thickbox is loaded or unloaded
	j('iframe#TB_iframeContent').livequery(function(){ 
	/*Thickbox is loaded*/
		console.log("thickboxLoaded");
	}, function() { 
	/*Thickbox is closed*/
		console.log("thickboxUnLoaded");
		var pid = gallery_block.attr('rel');

		//tab.addClass('loading');

		j.post( ajaxurl, {
			action: 'oqp_gallery',
			'cookie': encodeURIComponent(document.cookie),
			'pid': pid
		},
		function(response)
		{

		
			var old_gallery_block=gallery_block;
			var new_gallery_block=gallery_block.clone();

			response = response.substr(0, response.length-1);
			
			new_gallery_block.find('.gallery').remove();
			j(response).appendTo(new_gallery_block);
			
			oqp_gallery_add_edit_links(new_gallery_block);

			/*replace content : inject links for ajax favorite picture */

			//tab.removeClass('loading');

			
			//new_gallery = classified_pictures_add_edit_links(new_gallery);

			if (old_gallery_block.html() == new_gallery_block.html()) return false; //no changes
			
			gallery_block.fadeIn(200).html(new_gallery_block.html());

		});
		
	});
	
	function oqp_gallery_add_edit_links(gallery_block) {
	
		var gallery = gallery_block.find('.gallery:first');
	
		if (!gallery.length) return false; //empty gallery

		var new_gallery = gallery.clone();

		var items = new_gallery.find('.gallery-item');
		
		//BUILD EDIT URL
		var upload_link = jQuery(gallery_block).find('a.oqp-pictures-upload');
		upload_url=upload_link.attr('href');
		//split url, the 'tab' arg could not be at the end of url or it does not work
		var upload_url_split=upload_url.split('TB_iframe');
		//implode url
		var pic_upload_url=upload_url_split[0]+'tab=gallery&TB_iframe'+upload_url_split[1];

		//append links
		items.each( function() {
			
			link = j(this).find('.edit-oqp-gallery-pic');
			var edit_link_block =  j('<p class="hide-if-no-js"><a href="'+pic_upload_url+'" class="edit-oqp-gallery-pic">Edit</a></p>');
			
			//no yet link
			if (!link.length)
				edit_link_block.appendTo(j(this).find('dt:first'));

		
		});
		
		gallery.html(new_gallery.html());

	}
	
	
});

function GetElementsWithClassName(elementName,className) {
        var allElements = document.getElementsByTagName(elementName);
        var elemColl = new Array();
        for (var i = 0; i< allElements.length; i++) {
  if (hasClass(allElements[i], className)) {
         elemColl[elemColl.length] = allElements[i];
  }
        }
        return elemColl;
}

